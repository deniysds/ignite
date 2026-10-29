<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Issues\Models\Article;
use Modules\Issues\Models\Issue;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\ReviewRound;
use Modules\Submissions\Models\ReviewerAssignment;
use Modules\Submissions\Models\Submission;
use Modules\Submissions\Models\SubmissionAuthor;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;
    protected User $editor;
    protected User $reviewer;
    protected Journal $journal;
    protected Issue $issue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create([
            'name' => 'Prof. Author Indonesia',
            'email' => 'author@ignite.ac.id',
        ]);

        $this->editor = User::factory()->create([
            'name' => 'Dr. Editor In Chief',
            'email' => 'eic@ignite.ac.id',
        ]);
        $editorRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $this->editor->assignRole($editorRole);

        $this->reviewer = User::factory()->create([
            'name' => 'Dr. Reviewer Sejawat',
            'email' => 'reviewer@ignite.ac.id',
        ]);
        $reviewerRole = Role::create(['name' => 'reviewer', 'guard_name' => 'web']);
        $this->reviewer->assignRole($reviewerRole);

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
            'title'            => 'Vol. 1 No. 1 (2026): Launch Edition',
            'is_published'     => true,
            'published_at'     => now(),
        ]);
    }

    #[Test]
    public function guest_cannot_access_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function author_can_view_dashboard_with_author_metrics_and_submissions(): void
    {
        $sub = Submission::create([
            'journal_id'              => $this->journal->id,
            'title'                   => 'Clinical Exome Sequencing in Neurological Diseases',
            'slug'                    => 'clinical-exome-sequencing-neurological',
            'abstract'                => 'Investigation of neurological genetic variants in Indonesian patients.',
            'keywords'                => ['Exome', 'Neurology'],
            'status'                  => 'revision_required',
            'submitted_at'            => now()->subDays(5),
            'corresponding_author_id' => $this->author->id,
        ]);

        SubmissionAuthor::create([
            'submission_id'           => $sub->id,
            'name'                    => $this->author->name,
            'email'                   => $this->author->email,
            'is_corresponding_author' => true,
            'sort_order'              => 1,
        ]);

        $response = $this->actingAs($this->author)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang, ' . $this->author->name);
        $response->assertSee('Naskah Saya (Author Workspace)');
        $response->assertSee('Perlu Tindakan Revisi');
        $response->assertSee('Clinical Exome Sequencing in Neurological Diseases');
        $response->assertSee('Unggah Revisi');
    }

    #[Test]
    public function reviewer_can_view_dashboard_with_reviewer_workspace_and_assignments(): void
    {
        $sub = Submission::create([
            'journal_id'              => $this->journal->id,
            'title'                   => 'Targeted Panel NGS in Hereditary Cancers',
            'slug'                    => 'targeted-panel-ngs-hereditary-cancers',
            'abstract'                => 'Evaluation of BRCA1/2 and mismatch repair genes.',
            'keywords'                => ['Cancer', 'NGS'],
            'status'                  => 'under_review',
            'submitted_at'            => now()->subDays(10),
            'corresponding_author_id' => $this->author->id,
        ]);

        $round = ReviewRound::create([
            'submission_id' => $sub->id,
            'round_number'  => 1,
            'status'        => 'active',
            'started_at'    => now()->subDays(2),
        ]);

        $assign = ReviewerAssignment::create([
            'review_round_id' => $round->id,
            'reviewer_id'     => $this->reviewer->id,
            'response_status' => 'pending',
            'due_date'        => now()->addDays(12),
        ]);

        $response = $this->actingAs($this->reviewer)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ruang Kerja Mitra Bestari (Reviewer)');
        $response->assertSee('Undangan Menunggu Respon');
        $response->assertSee('Targeted Panel NGS in Hereditary Cancers');
        $response->assertSee('Buka Lembar Telaah');
    }

    #[Test]
    public function editor_can_view_dashboard_with_editorial_kpi_and_action_needed_queue(): void
    {
        $sub = Submission::create([
            'journal_id'              => $this->journal->id,
            'title'                   => 'Neonatal Genomic Screening for Rare Inborn Errors of Metabolism',
            'slug'                    => 'neonatal-genomic-screening-iem',
            'abstract'                => 'Comprehensive sequencing panel for early diagnosis.',
            'keywords'                => ['Neonatal', 'Metabolism'],
            'status'                  => 'resubmitted',
            'submitted_at'            => now()->subDays(15),
            'corresponding_author_id' => $this->author->id,
        ]);

        SubmissionAuthor::create([
            'submission_id'           => $sub->id,
            'name'                    => 'Dr. Siti Nurhaliza',
            'email'                   => 'siti@rscm.ac.id',
            'is_corresponding_author' => true,
            'sort_order'              => 1,
        ]);

        $art = Article::create([
            'issue_id'        => $this->issue->id,
            'submission_id'   => $sub->id,
            'title'           => $sub->title,
            'slug'            => $sub->slug,
            'pages'           => '1-18',
            'doi'             => '10.1234/img.2026.1.1.010',
            'views_count'     => 350,
            'downloads_count' => 120,
            'published_at'    => now(),
        ]);

        $response = $this->actingAs($this->editor)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Metrik Operasional Dewan Redaksi');
        $response->assertSee('Naskah Memerlukan Tindakan Cepat (Action Needed)');
        $response->assertSee('Revisi Masuk');
        $response->assertSee('Neonatal Genomic Screening for Rare Inborn Errors of Metabolism');
        $response->assertSee('Pintasan Kerja Redaksi');
        $response->assertSee('Kelola Naskah Masuk');
        $response->assertSee('Publikasi Ilmiah Terkini');
    }

    #[Test]
    public function dashboard_displays_platform_overview_statistics_for_all_users(): void
    {
        $response = $this->actingAs($this->author)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Statistik Ekosistem Publikasi IGNITE');
        $response->assertSee('Jurnal Aktif');
        $response->assertSee('Nomor Terbitan');
        $response->assertSee('Artikel Terbit');
        $response->assertSee('Naskah Masuk');
        $response->assertSee('Tayangan Pembaca');
        $response->assertSee('Unduhan PDF');
    }

    #[Test]
    public function sidebar_menu_renders_dashboard_link(): void
    {
        $response = $this->actingAs($this->author)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertSee('Dashboard');
    }

    #[Test]
    public function login_redirects_to_dashboard_instead_of_home(): void
    {
        $response = $this->post('/login', [
            'login' => $this->author->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
    }
}
