<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Modules\Issues\Models\Article;
use Modules\Issues\Models\Issue;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\Submission;
use Modules\Submissions\Models\SubmissionAuthor;
use Modules\Submissions\Models\SubmissionFile;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicArticleDetailTest extends TestCase
{
    use RefreshDatabase;

    protected Journal $journal;
    protected Issue $issue;
    protected Submission $submission;
    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->journal = Journal::create([
            'name' => 'Indonesian Journal of Genomics',
            'slug' => 'ijg',
            'short_name' => 'IJG',
            'description' => 'Official genomics journal of Ignite',
            'is_active' => true,
        ]);

        $this->issue = Issue::create([
            'journal_id' => $this->journal->id,
            'volume' => 1,
            'number' => 1,
            'publication_year' => 2026,
            'title' => 'Inaugural Issue on Indonesian Biodiversity Genomics',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $authorUser = User::factory()->create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@ignite.ac.id',
        ]);

        $this->submission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Genome-wide association study of indigenous population in Wallacea',
            'slug' => 'gwas-indigenous-wallacea-2026',
            'abstract' => 'This comprehensive study maps genetic variation across the Wallacea region.',
            'keywords' => ['genomics', 'wallacea', 'biodiversity'],
            'status' => 'published',
            'submitted_at' => now()->subDays(30),
            'decision_at' => now()->subDays(5),
            'corresponding_author_id' => $authorUser->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@ignite.ac.id',
            'affiliation' => 'Ignite Research Institute',
            'is_corresponding_author' => true,
            'sort_order' => 1,
        ]);

        $this->article = Article::create([
            'issue_id' => $this->issue->id,
            'submission_id' => $this->submission->id,
            'title' => $this->submission->title,
            'slug' => $this->submission->slug,
            'abstract' => $this->submission->abstract,
            'keywords' => $this->submission->keywords,
            'pages' => '1-14',
            'doi' => '10.1234/ijg.2026.1.1.001',
            'published_at' => now(),
            'views_count' => 10,
            'downloads_count' => 5,
        ]);
    }

    #[Test]
    public function visitor_can_view_public_article_detail_page(): void
    {
        $response = $this->get(route('website.articles.show', $this->article->slug));

        $response->assertStatus(200);
        $response->assertSee('Genome-wide association study of indigenous population in Wallacea');
        $response->assertSee('Dr. Budi Santoso');
        $response->assertSee('Ignite Research Institute');
        $response->assertSee('10.1234/ijg.2026.1.1.001');
        $response->assertSee('Sitasi Naskah Ini (How to Cite)');
    }

    #[Test]
    public function article_detail_increments_views_counter(): void
    {
        $initialViews = $this->article->views_count;

        $this->get(route('website.articles.show', $this->article->slug));

        $this->article->refresh();
        $this->assertEquals($initialViews + 1, $this->article->views_count);
    }

    #[Test]
    public function issue_recognizes_active_articles_relation(): void
    {
        $this->assertTrue($this->issue->hasActiveRelations());
        $this->assertEquals(1, $this->issue->articles()->count());
    }

    #[Test]
    public function visitor_can_download_article_file(): void
    {
        Storage::fake('public');
        $filePath = 'manuscripts/test-article.pdf';
        Storage::disk('public')->put($filePath, 'PDF-DUMMY-CONTENT');

        SubmissionFile::create([
            'submission_id' => $this->submission->id,
            'file_role' => 'naskah_utama',
            'disk' => 'public',
            'path' => $filePath,
            'original_name' => 'manuscript-final.pdf',
        ]);

        $initialDownloads = $this->article->downloads_count;

        $response = $this->get(route('website.articles.download', $this->article->slug));

        $response->assertStatus(200);
        $this->article->refresh();
        $this->assertEquals($initialDownloads + 1, $this->article->downloads_count);
    }
}
