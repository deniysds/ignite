<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\ReviewRound;
use Modules\Submissions\Models\ReviewerAssignment;
use Modules\Submissions\Models\Submission;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditorialReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected User $reviewer;
    protected Journal $journal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editor = User::factory()->create([
            'name' => 'Dr. Editor Utama',
            'email' => 'editor@ignite.ac.id',
            'email_verified_at' => now(),
        ]);

        $this->reviewer = User::factory()->create([
            'name' => 'Prof. Reviewer Hebat',
            'email' => 'reviewer@ignite.ac.id',
            'email_verified_at' => now(),
        ]);

        $this->journal = Journal::create([
            'name' => 'Jurnal Sains Komputer',
            'slug' => 'jsk',
            'short_name' => 'JSK',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function authenticated_editor_can_view_editorial_reports_dashboard(): void
    {
        // Create sample submissions
        $sub1 = Submission::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Analisis Algoritma Dijkstra Terdistribusi',
            'slug'         => 'analisis-algoritma-dijkstra',
            'status'       => 'accepted',
            'submitted_at' => now()->subDays(10),
            'decision_at'  => now()->subDays(2),
        ]);

        $sub2 = Submission::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Studi Kasus Cloud Computing',
            'slug'         => 'studi-kasus-cloud-computing',
            'status'       => 'rejected',
            'submitted_at' => now()->subDays(5),
            'decision_at'  => now()->subDays(1),
        ]);

        // Create sample review round and assignment
        $round = ReviewRound::create([
            'submission_id' => $sub1->id,
            'round_number'  => 1,
            'status'        => 'completed',
        ]);

        ReviewerAssignment::create([
            'review_round_id' => $round->id,
            'reviewer_id'     => $this->reviewer->id,
            'response_status' => 'completed',
            'recommendation'  => 'accept',
            'submitted_at'    => now()->subDays(3),
            'created_at'      => now()->subDays(8),
        ]);

        $response = $this->actingAs($this->editor)->get(route('editor.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Statistik Kinerja');
        $response->assertSee('Prof. Reviewer Hebat');
        $response->assertSee('Jurnal Sains Komputer');
    }

    #[Test]
    public function reports_correctly_calculate_acceptance_and_turnaround_rates(): void
    {
        // 2 accepted, 1 rejected, 1 under_review
        Submission::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Naskah A Diterima',
            'slug'         => 'naskah-a',
            'status'       => 'accepted',
            'submitted_at' => now()->subDays(12),
            'decision_at'  => now()->subDays(2), // 10 days
        ]);

        Submission::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Naskah B Diterima',
            'slug'         => 'naskah-b',
            'status'       => 'published',
            'submitted_at' => now()->subDays(20),
            'decision_at'  => now()->subDays(10), // 10 days
        ]);

        Submission::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Naskah C Ditolak',
            'slug'         => 'naskah-c',
            'status'       => 'rejected',
            'submitted_at' => now()->subDays(5),
            'decision_at'  => now()->subDays(1), // 4 days
        ]);

        Submission::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Naskah D Sedang Ditelaah',
            'slug'         => 'naskah-d',
            'status'       => 'under_review',
            'submitted_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($this->editor)->get(route('editor.reports.index'));

        $response->assertStatus(200);
        // Total submissions = 4
        // Decided = 3 (2 accepted/published, 1 rejected)
        // Acceptance rate = 2/3 = 66.7%
        // Rejection rate = 1/3 = 33.3%
        // Avg turnaround = (10 + 10 + 4) / 3 = 8 days
        $response->assertSee('66.7%');
        $response->assertSee('33.3%');
        $response->assertSee('8');
    }

    #[Test]
    public function editor_can_export_editorial_report_as_csv(): void
    {
        Submission::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Data Science for Healthcare',
            'slug'         => 'data-science-for-healthcare',
            'status'       => 'accepted',
            'submitted_at' => now()->subDays(10),
            'decision_at'  => now()->subDays(2),
        ]);

        $response = $this->actingAs($this->editor)->get(route('editor.reports.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();

        $this->assertStringContainsString('LAPORAN STATISTIK KINERJA REDAKSI & REVIEWER - IGNITE', $content);
        $this->assertStringContainsString('RINGKASAN METRIK EDITORIAL', $content);
        $this->assertStringContainsString('KINERJA MITRA BESTARI (PEER REVIEWERS)', $content);
    }
}
