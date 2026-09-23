<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Issues\Models\Article;
use Modules\Issues\Models\Issue;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\Submission;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicArticleFilterAndSortingTest extends TestCase
{
    use RefreshDatabase;

    protected Journal $journal1;
    protected Journal $journal2;
    protected Issue $issue2025;
    protected Issue $issue2026;
    protected Article $articleAlpha;
    protected Article $articleBeta;
    protected Article $articleGamma;

    protected function setUp(): void
    {
        parent::setUp();

        $this->journal1 = Journal::create([
            'name' => 'Journal of Genomics',
            'slug' => 'jog',
            'short_name' => 'JOG',
            'is_active' => true,
        ]);

        $this->journal2 = Journal::create([
            'name' => 'Journal of Proteomics',
            'slug' => 'jop',
            'short_name' => 'JOP',
            'is_active' => true,
        ]);

        $this->issue2025 = Issue::create([
            'journal_id' => $this->journal1->id,
            'volume' => 1,
            'number' => 1,
            'publication_year' => 2025,
            'title' => 'Issue 2025',
            'is_published' => true,
            'published_at' => now()->subYear(),
        ]);

        $this->issue2026 = Issue::create([
            'journal_id' => $this->journal2->id,
            'volume' => 2,
            'number' => 1,
            'publication_year' => 2026,
            'title' => 'Issue 2026',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $user = User::factory()->create();

        // Alpha: Low views (10), high downloads (100)
        $subA = Submission::create([
            'journal_id' => $this->journal1->id,
            'title' => 'Alpha Genomic Sequencing',
            'slug' => 'alpha-genomic-sequencing',
            'abstract' => 'Sequencing analysis Alpha',
            'status' => 'published',
            'corresponding_author_id' => $user->id,
        ]);
        $this->articleAlpha = Article::create([
            'issue_id' => $this->issue2025->id,
            'submission_id' => $subA->id,
            'title' => 'Alpha Genomic Sequencing',
            'slug' => 'alpha-genomic-sequencing',
            'abstract' => 'Sequencing analysis Alpha',
            'published_at' => now()->subDays(10),
            'views_count' => 10,
            'downloads_count' => 100,
        ]);

        // Beta: High views (500), low downloads (5)
        $subB = Submission::create([
            'journal_id' => $this->journal2->id,
            'title' => 'Beta Proteomics Atlas',
            'slug' => 'beta-proteomics-atlas',
            'abstract' => 'Proteome analysis Beta',
            'status' => 'published',
            'corresponding_author_id' => $user->id,
        ]);
        $this->articleBeta = Article::create([
            'issue_id' => $this->issue2026->id,
            'submission_id' => $subB->id,
            'title' => 'Beta Proteomics Atlas',
            'slug' => 'beta-proteomics-atlas',
            'abstract' => 'Proteome analysis Beta',
            'published_at' => now()->subDays(5),
            'views_count' => 500,
            'downloads_count' => 5,
        ]);

        // Gamma: Moderate views (50), moderate downloads (20)
        $subG = Submission::create([
            'journal_id' => $this->journal2->id,
            'title' => 'Gamma Transcriptome Analysis',
            'slug' => 'gamma-transcriptome-analysis',
            'abstract' => 'Transcriptome analysis Gamma',
            'status' => 'published',
            'corresponding_author_id' => $user->id,
        ]);
        $this->articleGamma = Article::create([
            'issue_id' => $this->issue2026->id,
            'submission_id' => $subG->id,
            'title' => 'Gamma Transcriptome Analysis',
            'slug' => 'gamma-transcriptome-analysis',
            'abstract' => 'Transcriptome analysis Gamma',
            'published_at' => now()->subDays(1),
            'views_count' => 50,
            'downloads_count' => 20,
        ]);
    }

    #[Test]
    public function visitor_can_filter_articles_by_journal(): void
    {
        $response = $this->get(route('website.articles.index', ['journal_id' => $this->journal1->id]));

        $response->assertStatus(200);
        $response->assertSee('Alpha Genomic Sequencing');
        $response->assertDontSee('Beta Proteomics Atlas');
    }

    #[Test]
    public function visitor_can_filter_articles_by_publication_year(): void
    {
        $response = $this->get(route('website.articles.index', ['year' => 2025]));

        $response->assertStatus(200);
        $response->assertSee('Alpha Genomic Sequencing');
        $response->assertDontSee('Gamma Transcriptome Analysis');
    }

    #[Test]
    public function visitor_can_sort_articles_by_most_popular_views(): void
    {
        $response = $this->get(route('website.articles.index', ['sort' => 'popular']));

        $response->assertStatus(200);
        
        $articles = $response->viewData('articles');
        $this->assertEquals($this->articleBeta->id, $articles->first()->id);
        $this->assertEquals($this->articleAlpha->id, $articles->last()->id);
    }

    #[Test]
    public function visitor_can_sort_articles_by_most_downloads(): void
    {
        $response = $this->get(route('website.articles.index', ['sort' => 'downloads']));

        $response->assertStatus(200);
        
        $articles = $response->viewData('articles');
        $this->assertEquals($this->articleAlpha->id, $articles->first()->id);
        $this->assertEquals($this->articleBeta->id, $articles->last()->id);
    }

    #[Test]
    public function visitor_can_sort_articles_alphabetically_by_title(): void
    {
        $response = $this->get(route('website.articles.index', ['sort' => 'title']));

        $response->assertStatus(200);
        
        $articles = $response->viewData('articles');
        $this->assertEquals('Alpha Genomic Sequencing', $articles->first()->title);
        $this->assertEquals('Gamma Transcriptome Analysis', $articles->last()->title);
    }
}
