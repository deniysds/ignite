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
use Tests\TestCase;

class PublishToIssueTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected Journal $journal;
    protected Issue $issue;
    protected Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editor = User::factory()->create([
            'name' => 'Dr. Editor In Chief',
            'email' => 'eic@ignite.ac.id',
        ]);

        $author = User::factory()->create([
            'name' => 'Dr. Author',
            'email' => 'author@ignite.ac.id',
        ]);

        $this->journal = Journal::create([
            'name' => 'Indonesian Medical Genomics',
            'slug' => 'img',
            'short_name' => 'IMG',
            'is_active' => true,
        ]);

        $this->issue = Issue::create([
            'journal_id' => $this->journal->id,
            'volume' => 1,
            'number' => 1,
            'publication_year' => 2026,
            'title' => 'Vol. 1 No. 1 (2026): Launching Issue',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->submission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Next Generation Sequencing in Rare Genetic Diseases',
            'slug' => 'ngs-rare-genetic-diseases',
            'abstract' => 'Comprehensive diagnosis using whole exome sequencing.',
            'keywords' => ['NGS', 'Genetics', 'Rare Diseases'],
            'status' => 'accepted',
            'submitted_at' => now()->subDays(20),
            'decision_at' => now()->subDays(1),
            'corresponding_author_id' => $author->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name' => 'Dr. Author',
            'email' => 'author@ignite.ac.id',
            'is_corresponding_author' => true,
            'sort_order' => 1,
        ]);
    }

    #[Test]
    public function editor_can_publish_accepted_submission_to_an_issue(): void
    {
        $response = $this->actingAs($this->editor)
            ->post(route('editor.submissions.publish_to_issue', $this->submission->id), [
                'issue_id' => $this->issue->id,
                'pages'    => '15-28',
                'doi'      => '10.1234/img.2026.1.1.002',
            ]);

        $response->assertRedirect(route('editor.submissions.show', $this->submission->id));
        $response->assertSessionHas('success');

        // Check article record exists
        $this->assertDatabaseHas('articles', [
            'submission_id' => $this->submission->id,
            'issue_id'      => $this->issue->id,
            'title'         => 'Next Generation Sequencing in Rare Genetic Diseases',
            'pages'         => '15-28',
            'doi'           => '10.1234/img.2026.1.1.002',
        ]);

        // Check submission status is now published
        $this->submission->refresh();
        $this->assertEquals('published', $this->submission->status);

        // Check public article page is now accessible
        $publicResponse = $this->get(route('website.articles.show', $this->submission->slug));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Next Generation Sequencing in Rare Genetic Diseases');
        $publicResponse->assertSee('10.1234/img.2026.1.1.002');
    }
}
