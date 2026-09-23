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

class PublicArticlePdfViewerTest extends TestCase
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
            'name' => 'Indonesian Medical Genomics Journal',
            'slug' => 'imgj',
            'short_name' => 'IMGJ',
            'description' => 'Genomics journal of YSDS',
            'is_active' => true,
        ]);

        $this->issue = Issue::create([
            'journal_id' => $this->journal->id,
            'volume' => 2,
            'number' => 1,
            'publication_year' => 2026,
            'title' => 'Advances in Clinical Genomics',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $authorUser = User::factory()->create([
            'name' => 'Prof. Dr. Andi Wijaya',
            'email' => 'andi@ignite.ac.id',
        ]);

        $this->submission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Pharmacogenomics markers for adverse drug reaction screening in Indonesia',
            'slug' => 'pharmacogenomics-markers-adr-indonesia-2026',
            'abstract' => 'This study examines genetic polymorphisms influencing adverse drug reactions.',
            'keywords' => ['pharmacogenomics', 'HLA-B', 'drug safety'],
            'status' => 'published',
            'submitted_at' => now()->subDays(20),
            'decision_at' => now()->subDays(2),
            'corresponding_author_id' => $authorUser->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name' => 'Prof. Dr. Andi Wijaya',
            'email' => 'andi@ignite.ac.id',
            'affiliation' => 'YSDS Research Center',
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
            'pages' => '15-28',
            'doi' => '10.1234/imgj.2026.2.1.002',
            'published_at' => now(),
            'views_count' => 15,
            'downloads_count' => 8,
        ]);
    }

    #[Test]
    public function visitor_can_view_article_pdf_inline_in_browser(): void
    {
        Storage::fake('public');
        $pdfPath = 'manuscripts/adr-screening.pdf';
        Storage::disk('public')->put($pdfPath, '%PDF-1.4 DUMMY RESEARCH CONTENT');

        SubmissionFile::create([
            'submission_id' => $this->submission->id,
            'file_role' => 'naskah_utama',
            'disk' => 'public',
            'path' => $pdfPath,
            'original_name' => 'adr-screening-manuscript.pdf',
        ]);

        $initialViews = $this->article->views_count;

        $response = $this->get(route('website.articles.view_pdf', $this->article->slug));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition') ?? '');

        $this->article->refresh();
        $this->assertEquals($initialViews + 1, $this->article->views_count);
    }

    #[Test]
    public function article_detail_page_renders_in_browser_pdf_viewer_elements(): void
    {
        Storage::fake('public');
        $pdfPath = 'manuscripts/adr-screening.pdf';
        Storage::disk('public')->put($pdfPath, '%PDF-1.4 DUMMY RESEARCH CONTENT');

        SubmissionFile::create([
            'submission_id' => $this->submission->id,
            'file_role' => 'naskah_utama',
            'disk' => 'public',
            'path' => $pdfPath,
            'original_name' => 'adr-screening-manuscript.pdf',
        ]);

        $response = $this->get(route('website.articles.show', $this->article->slug));

        $response->assertStatus(200);
        $response->assertSee('Baca Online (PDF Viewer)');
        $response->assertSee('Unduh PDF Resmi');
        $response->assertSee(route('website.articles.view_pdf', $this->article->slug));
        $response->assertSee('PDF Galley');
    }

    #[Test]
    public function viewing_nonexistent_pdf_returns_404(): void
    {
        $response = $this->get(route('website.articles.view_pdf', $this->article->slug));

        $response->assertStatus(404);
    }
}
