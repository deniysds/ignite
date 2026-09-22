<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Issues\Models\Article;
use Modules\Issues\Models\Issue;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\Submission;
use Modules\Submissions\Models\SubmissionAuthor;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class IssueArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected User $regularUser;
    protected Journal $journal;
    protected Issue $issue;
    protected Submission $submission;
    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Modules\Issues\Database\Seeders\PermissionSeeder::class);

        $this->editor = User::factory()->create([
            'name' => 'Dr. Managing Editor',
            'email' => 'managing_editor@ignite.ac.id',
        ]);
        $this->editor->givePermissionTo(['issues.read', 'issues.update', 'issues.delete']);

        $this->regularUser = User::factory()->create([
            'name' => 'Regular Author',
            'email' => 'regular@ignite.ac.id',
        ]);

        $this->journal = Journal::create([
            'name' => 'Indonesian Medical Genomics',
            'slug' => 'img',
            'short_name' => 'IMG',
            'is_active' => true,
        ]);

        $this->issue = Issue::create([
            'journal_id'       => $this->journal->id,
            'volume'           => 1,
            'number'           => 1,
            'publication_year' => 2026,
            'title'            => 'Vol. 1 No. 1 (2026): Launching Edition',
            'is_published'     => false,
            'published_at'     => null,
        ]);

        $this->submission = Submission::create([
            'journal_id'              => $this->journal->id,
            'title'                   => 'Whole Genome Sequencing in Undiagnosed Pediatric Disorders',
            'slug'                    => 'wgs-undiagnosed-pediatric-disorders',
            'abstract'                => 'Investigation of rare genetic variants across pediatric cohort.',
            'keywords'                => ['Pediatrics', 'WGS', 'Rare Diseases'],
            'status'                  => 'published',
            'submitted_at'            => now()->subDays(30),
            'decision_at'             => now()->subDays(5),
            'corresponding_author_id' => $this->editor->id,
        ]);

        SubmissionAuthor::create([
            'submission_id'           => $this->submission->id,
            'name'                    => 'Prof. Aisyah Rahman',
            'email'                   => 'aisyah@ui.ac.id',
            'is_corresponding_author' => true,
            'sort_order'              => 1,
        ]);

        $this->article = Article::create([
            'issue_id'        => $this->issue->id,
            'submission_id'   => $this->submission->id,
            'title'           => $this->submission->title,
            'slug'            => $this->submission->slug,
            'abstract'        => $this->submission->abstract,
            'keywords'        => $this->submission->keywords,
            'pages'           => '1-12',
            'doi'             => '10.1234/img.2026.1.1.001',
            'published_at'    => now(),
            'views_count'     => 120,
            'downloads_count' => 45,
        ]);
    }

    #[Test]
    public function editor_can_view_issue_detail_with_articles_and_statistics(): void
    {
        $response = $this->actingAs($this->editor)
            ->get(route('issues.show', $this->issue->id));

        $response->assertStatus(200);
        $response->assertSee('Vol. 1 No. 1 (2026): Launching Edition');
        $response->assertSee('Daftar Naskah &amp; Artikel Terbit', false);
        $response->assertSee('Whole Genome Sequencing in Undiagnosed Pediatric Disorders');
        $response->assertSee('Prof. Aisyah Rahman');
        $response->assertSee('1-12');
        $response->assertSee('10.1234/img.2026.1.1.001');
        $response->assertSee('120'); // views
        $response->assertSee('45');  // downloads
    }

    #[Test]
    public function editor_can_toggle_issue_published_status(): void
    {
        $this->assertFalse($this->issue->is_published);

        // Toggle to Published
        $response = $this->actingAs($this->editor)
            ->post(route('issues.toggle_publish', $this->issue->id));

        $response->assertRedirect(route('issues.show', $this->issue->id));
        $response->assertSessionHas('success');

        $this->issue->refresh();
        $this->assertTrue($this->issue->is_published);
        $this->assertNotNull($this->issue->published_at);

        // Toggle back to Draft
        $response2 = $this->actingAs($this->editor)
            ->post(route('issues.toggle_publish', $this->issue->id));

        $response2->assertRedirect(route('issues.show', $this->issue->id));
        $this->issue->refresh();
        $this->assertFalse($this->issue->is_published);
    }

    #[Test]
    public function editor_can_update_article_pages_and_doi_in_issue(): void
    {
        $response = $this->actingAs($this->editor)
            ->put(route('issues.articles.update', [$this->issue->id, $this->article->id]), [
                'pages' => '15-32',
                'doi'   => '10.1234/img.2026.1.1.005',
            ]);

        $response->assertRedirect(route('issues.show', $this->issue->id));
        $response->assertSessionHas('success');

        $this->article->refresh();
        $this->assertEquals('15-32', $this->article->pages);
        $this->assertEquals('10.1234/img.2026.1.1.005', $this->article->doi);
    }

    #[Test]
    public function editor_can_remove_article_from_issue_and_revert_submission_status(): void
    {
        $this->assertEquals('published', $this->submission->status);

        $response = $this->actingAs($this->editor)
            ->delete(route('issues.articles.destroy', [$this->issue->id, $this->article->id]));

        $response->assertRedirect(route('issues.show', $this->issue->id));
        $response->assertSessionHas('success');

        // Article record should be deleted
        $this->assertSoftDeleted('articles', ['id' => $this->article->id]);

        // Submission status must be reverted to accepted
        $this->submission->refresh();
        $this->assertEquals('accepted', $this->submission->status);

        // History record should be logged
        $this->assertDatabaseHas('submission_histories', [
            'submission_id' => $this->submission->id,
            'from_status'   => 'published',
            'to_status'     => 'accepted',
        ]);
    }

    #[Test]
    public function user_without_permission_cannot_manage_articles_or_toggle_issue(): void
    {
        $toggleResponse = $this->actingAs($this->regularUser)
            ->post(route('issues.toggle_publish', $this->issue->id));
        $toggleResponse->assertStatus(403);

        $updateResponse = $this->actingAs($this->regularUser)
            ->put(route('issues.articles.update', [$this->issue->id, $this->article->id]), [
                'pages' => '99-100',
            ]);
        $updateResponse->assertStatus(403);

        $deleteResponse = $this->actingAs($this->regularUser)
            ->delete(route('issues.articles.destroy', [$this->issue->id, $this->article->id]));
        $deleteResponse->assertStatus(403);
    }
}
