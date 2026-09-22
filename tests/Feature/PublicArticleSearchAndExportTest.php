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

class PublicArticleSearchAndExportTest extends TestCase
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
            'name' => 'Indonesian Journal of Genomics and Precision Medicine',
            'slug' => 'ijgpm',
            'short_name' => 'IJGPM',
            'issn_e' => '2988-1234',
            'description' => 'Official genomics and precision medicine journal of Ignite',
            'is_active' => true,
        ]);

        $this->issue = Issue::create([
            'journal_id' => $this->journal->id,
            'volume' => 1,
            'number' => 1,
            'publication_year' => 2026,
            'title' => 'Inaugural Issue on Indonesian Population Genomics',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $authorUser = User::factory()->create([
            'name' => 'Prof. Dr. Vincentius Budhyanto',
            'email' => 'vincent@ysds.or.id',
        ]);

        $this->submission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Deep Learning Framework for Population Genomics in Indonesia',
            'slug' => 'deep-learning-framework-population-genomics-indonesia',
            'abstract' => 'We present a machine learning architecture trained on whole genome sequencing data.',
            'keywords' => ['deep learning', 'genomics', 'indonesia'],
            'status' => 'published',
            'submitted_at' => now()->subDays(40),
            'decision_at' => now()->subDays(10),
            'corresponding_author_id' => $authorUser->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name' => 'Prof. Dr. Vincentius Budhyanto',
            'email' => 'vincent@ysds.or.id',
            'affiliation' => 'Yayasan Satriabudi Dharma Setia',
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
            'pages' => '1-15',
            'doi' => '10.1234/ijgpm.2026.1.1.001',
            'published_at' => now(),
            'views_count' => 50,
            'downloads_count' => 20,
        ]);
    }

    #[Test]
    public function visitor_can_browse_and_search_articles_catalog(): void
    {
        $response = $this->get(route('website.articles.index'));
        $response->assertStatus(200);
        $response->assertSee('Katalog &amp; Pencarian Artikel', false);
        $response->assertSee('Deep Learning Framework for Population Genomics in Indonesia');

        // Test search query
        $searchResponse = $this->get(route('website.articles.index', ['search' => 'Deep Learning']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Deep Learning Framework');

        // Test search query that should yield no results
        $noMatchResponse = $this->get(route('website.articles.index', ['search' => 'NonExistentKeywordXYZ']));
        $noMatchResponse->assertStatus(200);
        $noMatchResponse->assertSee('Tidak Ada Artikel yang Cocok');
    }

    #[Test]
    public function visitor_can_export_article_citation_in_ris_format(): void
    {
        $response = $this->get(route('website.articles.export.ris', $this->article->slug));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-research-info-systems; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . $this->article->slug . '.ris"');

        $content = $response->getContent();
        $this->assertStringContainsString('TY  - JOUR', $content);
        $this->assertStringContainsString('TI  - Deep Learning Framework for Population Genomics in Indonesia', $content);
        $this->assertStringContainsString('AU  - Prof. Dr. Vincentius Budhyanto', $content);
        $this->assertStringContainsString('DO  - 10.1234/ijgpm.2026.1.1.001', $content);
        $this->assertStringContainsString('ER  -', $content);
    }

    #[Test]
    public function visitor_can_export_article_citation_in_bibtex_format(): void
    {
        $response = $this->get(route('website.articles.export.bib', $this->article->slug));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-bibtex; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . $this->article->slug . '.bib"');

        $content = $response->getContent();
        $this->assertStringContainsString('@article{', $content);
        $this->assertStringContainsString('Deep Learning Framework for Population Genomics in Indonesia', $content);
        $this->assertStringContainsString('Vincentius Budhyanto', $content);
        $this->assertStringContainsString('10.1234/ijgpm.2026.1.1.001', $content);
    }

    #[Test]
    public function article_detail_page_includes_google_scholar_citation_meta_tags(): void
    {
        $response = $this->get(route('website.articles.show', $this->article->slug));

        $response->assertStatus(200);
        $response->assertSee('name="citation_title"', false);
        $response->assertSee('name="citation_author"', false);
        $response->assertSee('name="citation_doi"', false);
        $response->assertSee('name="citation_journal_title"', false);
        $response->assertSee('name="DC.Title"', false);
        $response->assertSee('Unduh Sitasi:', false);
        $response->assertSee('.RIS', false);
        $response->assertSee('.BIB', false);
    }
}
