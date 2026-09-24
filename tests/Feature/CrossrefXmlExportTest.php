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

class CrossrefXmlExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected Journal $journal;
    protected Issue $issue;
    protected Submission $submission;
    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Modules\Issues\Database\Seeders\PermissionSeeder::class);

        $this->editor = User::factory()->create([
            'name' => 'Managing Editor',
            'email' => 'editor@ignite.ac.id',
            'email_verified_at' => now(),
        ]);
        $this->editor->givePermissionTo(['issues.read', 'issues.update']);

        $this->journal = Journal::create([
            'name'       => 'Indonesian Journal of Genomics and Bioinformatics',
            'slug'       => 'ijgb',
            'short_name' => 'IJGB',
            'issn_p'     => '1234-5678',
            'issn_e'     => '8765-4321',
            'is_active'  => true,
        ]);

        $this->issue = Issue::create([
            'journal_id'       => $this->journal->id,
            'volume'           => 3,
            'number'           => 2,
            'publication_year' => 2026,
            'title'            => 'Vol. 3 No. 2 (2026): Regular Issue',
            'is_published'     => true,
            'published_at'     => '2026-09-01',
        ]);

        $this->submission = Submission::create([
            'journal_id'              => $this->journal->id,
            'title'                   => 'Comparative Analysis of CRISPR-Cas9 Variants in Rice',
            'slug'                    => 'crispr-cas9-rice-analysis',
            'status'                  => 'published',
            'submitted_at'            => now()->subDays(30),
            'decision_at'             => now()->subDays(5),
            'corresponding_author_id' => $this->editor->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name'          => 'Dr. Siti Aminah',
            'email'         => 'siti@univ.ac.id',
            'affiliation'   => 'Universitas Indonesia',
            'is_corresponding_author' => true,
            'sort_order'    => 1,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name'          => 'Budi Santoso',
            'email'         => 'budi@univ.ac.id',
            'affiliation'   => 'BRIN Indonesia',
            'is_corresponding_author' => false,
            'sort_order'    => 2,
        ]);

        $this->article = Article::create([
            'issue_id'        => $this->issue->id,
            'submission_id'   => $this->submission->id,
            'title'           => 'Comparative Analysis of CRISPR-Cas9 Variants in Rice',
            'slug'            => 'crispr-cas9-rice-analysis',
            'pages'           => '45-58',
            'doi'             => '10.1234/ijgb.2026.3.2.001',
            'views_count'     => 10,
            'downloads_count' => 5,
            'published_at'    => '2026-09-01',
        ]);
    }

    #[Test]
    public function editor_can_export_crossref_schema_53_xml(): void
    {
        $response = $this->actingAs($this->editor)
            ->get(route('issues.export_crossref_xml', $this->issue->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $content = $response->getContent();

        // 1. Verify Crossref Schema 5.3 root element
        $this->assertStringContainsString('<doi_batch', $content);
        $this->assertStringContainsString('xmlns="http://www.crossref.org/schema/5.3.1"', $content);
        $this->assertStringContainsString('version="5.3.1"', $content);

        // 2. Verify header elements
        $this->assertStringContainsString('<head>', $content);
        $this->assertStringContainsString('<doi_batch_id>IGNITE_ISSUE_' . $this->issue->id, $content);
        $this->assertStringContainsString('<depositor_name>', $content);

        // 3. Verify journal metadata
        $this->assertStringContainsString('<full_title>Indonesian Journal of Genomics and Bioinformatics</full_title>', $content);
        $this->assertStringContainsString('<abbrev_title>IJGB</abbrev_title>', $content);
        $this->assertStringContainsString('<issn media_type="print">1234-5678</issn>', $content);
        $this->assertStringContainsString('<issn media_type="electronic">8765-4321</issn>', $content);

        // 4. Verify issue elements
        $this->assertStringContainsString('<volume>3</volume>', $content);
        $this->assertStringContainsString('<issue>2</issue>', $content);

        // 5. Verify article metadata
        $this->assertStringContainsString('<title>Comparative Analysis of CRISPR-Cas9 Variants in Rice</title>', $content);
        $this->assertStringContainsString('<surname>Aminah</surname>', $content);
        $this->assertStringContainsString('<given_name>Dr. Siti</given_name>', $content);
        $this->assertStringContainsString('<institution_name>Universitas Indonesia</institution_name>', $content);

        // 6. Verify pages and DOI
        $this->assertStringContainsString('<first_page>45</first_page>', $content);
        $this->assertStringContainsString('<last_page>58</last_page>', $content);
        $this->assertStringContainsString('<doi>10.1234/ijgb.2026.3.2.001</doi>', $content);

        // 7. Verify valid XML parsing
        $xml = simplexml_load_string($content);
        $this->assertNotFalse($xml, 'The generated Crossref XML string must be well-formed XML.');
    }

    #[Test]
    public function issue_show_page_displays_crossref_export_button(): void
    {
        $response = $this->actingAs($this->editor)->get(route('issues.show', $this->issue->id));

        $response->assertStatus(200);
        $response->assertSee(route('issues.export_crossref_xml', $this->issue->id));
        $response->assertSee('Ekspor Crossref XML');
    }
}
