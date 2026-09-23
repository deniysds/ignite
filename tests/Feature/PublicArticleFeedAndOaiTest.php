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

class PublicArticleFeedAndOaiTest extends TestCase
{
    use RefreshDatabase;

    protected Journal $journal;
    protected Issue $issue;
    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->journal = Journal::create([
            'name' => 'Indonesian Journal of Clinical Genomics',
            'slug' => 'ijcg',
            'short_name' => 'IJCG',
            'is_active' => true,
        ]);

        $this->issue = Issue::create([
            'journal_id' => $this->journal->id,
            'volume' => 3,
            'number' => 2,
            'publication_year' => 2026,
            'title' => 'Special Issue: Precision Medicine in Indonesia',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $user = User::factory()->create();

        $submission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Single Nucleotide Polymorphisms in Indonesian Population',
            'slug' => 'snp-indonesian-population-2026',
            'abstract' => 'Comprehensive profiling of common SNPs identified across Indonesian archipelago.',
            'keywords' => ['SNP', 'genomics', 'Wallacea'],
            'status' => 'published',
            'corresponding_author_id' => $user->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $submission->id,
            'name' => 'Dr. Rina Handayani',
            'email' => 'rina@ignite.ac.id',
            'affiliation' => 'Biomedical Center',
            'is_corresponding_author' => true,
            'sort_order' => 1,
        ]);

        $this->article = Article::create([
            'issue_id' => $this->issue->id,
            'submission_id' => $submission->id,
            'title' => $submission->title,
            'slug' => $submission->slug,
            'abstract' => $submission->abstract,
            'keywords' => $submission->keywords,
            'pages' => '45-56',
            'doi' => '10.1234/ijcg.2026.3.2.045',
            'published_at' => now(),
            'views_count' => 88,
            'downloads_count' => 30,
        ]);
    }

    #[Test]
    public function visitor_can_retrieve_valid_rss_2_feed(): void
    {
        $response = $this->get(route('website.feed.rss'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $this->assertStringContainsString('<rss version="2.0"', $response->getContent());
        $this->assertStringContainsString('xmlns:dc="http://purl.org/dc/elements/1.1/"', $response->getContent());
        $this->assertStringContainsString('Single Nucleotide Polymorphisms in Indonesian Population', $response->getContent());
        $this->assertStringContainsString('Dr. Rina Handayani', $response->getContent());
        $this->assertStringContainsString('doi:10.1234/ijcg.2026.3.2.045', $response->getContent());
    }

    #[Test]
    public function harvester_can_call_oai_identify(): void
    {
        $response = $this->get(route('website.oai', ['verb' => 'Identify']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $this->assertStringContainsString('<OAI-PMH', $response->getContent());
        $this->assertStringContainsString('<Identify>', $response->getContent());
        $this->assertStringContainsString('<repositoryName>', $response->getContent());
        $this->assertStringContainsString('<protocolVersion>2.0</protocolVersion>', $response->getContent());
    }

    #[Test]
    public function harvester_can_call_oai_list_records(): void
    {
        $response = $this->get(route('website.oai', [
            'verb' => 'ListRecords',
            'metadataPrefix' => 'oai_dc',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $this->assertStringContainsString('<ListRecords>', $response->getContent());
        $this->assertStringContainsString('oai:ignite.ysds.or.id:article/' . $this->article->id, $response->getContent());
        $this->assertStringContainsString('<dc:title>', $response->getContent());
        $this->assertStringContainsString('Single Nucleotide Polymorphisms in Indonesian Population', $response->getContent());
        $this->assertStringContainsString('Dr. Rina Handayani', $response->getContent());
        $this->assertStringContainsString('doi:10.1234/ijcg.2026.3.2.045', $response->getContent());
    }

    #[Test]
    public function harvester_can_call_oai_list_metadata_formats(): void
    {
        $response = $this->get(route('website.oai', ['verb' => 'ListMetadataFormats']));

        $response->assertStatus(200);
        $this->assertStringContainsString('<metadataPrefix>oai_dc</metadataPrefix>', $response->getContent());
        $this->assertStringContainsString('http://www.openarchives.org/OAI/2.0/oai_dc.xsd', $response->getContent());
    }
}
