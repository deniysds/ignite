<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\ReviewRound;
use Modules\Submissions\Models\ReviewerAssignment;
use Modules\Submissions\Models\Submission;
use Modules\Submissions\Models\SubmissionAuthor;
use Modules\Submissions\Models\SubmissionFile;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PeerReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected User $author;
    protected User $reviewer;
    protected Journal $journal;
    protected Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editor = User::factory()->create([
            'name' => 'Prof. Editor Utama',
            'email' => 'editor@ignite.ac.id',
        ]);

        $this->author = User::factory()->create([
            'name' => 'Dr. Penulis Pertama',
            'email' => 'author@ignite.ac.id',
        ]);

        $this->reviewer = User::factory()->create([
            'name' => 'Dr. Mitra Bestari Ahli',
            'email' => 'reviewer@ignite.ac.id',
        ]);

        $this->journal = Journal::create([
            'name' => 'Jurnal Sains Komputasi Terapan',
            'slug' => 'jskt',
            'short_name' => 'JSKT',
            'description' => 'Jurnal komputasi terapan.',
            'is_active' => true,
        ]);

        $this->submission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Algoritma Kuantum untuk Kriptografi Modern',
            'slug' => 'algoritma-kuantum-kriptografi-modern',
            'abstract' => 'Penelitian komprehensif implementasi algoritma pasca-kuantum berbasis kisi.',
            'keywords' => ['quantum', 'cryptography', 'lattice'],
            'status' => 'submitted',
            'submitted_at' => now(),
            'corresponding_author_id' => $this->author->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name' => 'Dr. Penulis Pertama',
            'email' => 'author@ignite.ac.id',
            'affiliation' => 'Institut Teknologi Bandung',
            'is_corresponding_author' => true,
            'sort_order' => 1,
        ]);

        Storage::fake('public');
        Storage::disk('public')->put('submissions/test_manuscript.pdf', 'dummy content');

        SubmissionFile::create([
            'submission_id' => $this->submission->id,
            'file_role' => 'naskah_utama',
            'disk' => 'public',
            'path' => 'submissions/test_manuscript.pdf',
            'original_name' => 'naskah_kuantum.pdf',
        ]);
    }

    #[Test]
    public function test_editor_can_assign_reviewer_to_manuscript(): void
    {
        $response = $this->actingAs($this->editor)->post(route('editor.submissions.assign_reviewer', $this->submission->id), [
            'reviewer_id' => $this->reviewer->id,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('editor.submissions.show', $this->submission->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('review_rounds', [
            'submission_id' => $this->submission->id,
            'round_number' => 1,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('reviewer_assignments', [
            'reviewer_id' => $this->reviewer->id,
            'response_status' => 'pending',
        ]);

        $this->assertDatabaseHas('submissions', [
            'id' => $this->submission->id,
            'status' => 'under_review',
        ]);
    }

    #[Test]
    public function test_editor_cannot_assign_author_as_reviewer(): void
    {
        $response = $this->actingAs($this->editor)->post(route('editor.submissions.assign_reviewer', $this->submission->id), [
            'reviewer_id' => $this->author->id,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('editor.submissions.show', $this->submission->id));
        $response->assertSessionHasErrors(['reviewer_id']);

        $this->assertDatabaseMissing('reviewer_assignments', [
            'reviewer_id' => $this->author->id,
        ]);
    }

    #[Test]
    public function test_reviewer_can_view_assigned_manuscripts_and_open_blinded_workspace(): void
    {
        $round = ReviewRound::create([
            'submission_id' => $this->submission->id,
            'round_number' => 1,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $assignment = ReviewerAssignment::create([
            'review_round_id' => $round->id,
            'reviewer_id' => $this->reviewer->id,
            'response_status' => 'pending',
            'due_date' => now()->addDays(14),
        ]);

        // Reviewer index
        $indexResponse = $this->actingAs($this->reviewer)->get(route('reviewer.assignments.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertViewIs('submissions::reviewer.index');
        $indexResponse->assertSee($this->submission->title);

        // Reviewer show workspace (Double-blind check)
        $showResponse = $this->actingAs($this->reviewer)->get(route('reviewer.assignments.show', $assignment->id));
        $showResponse->assertStatus(200);
        $showResponse->assertViewIs('submissions::reviewer.show');
        $showResponse->assertSee($this->submission->title);
        $showResponse->assertSee($this->submission->abstract);
        // Double-blind: reviewer should NOT see author name
        $showResponse->assertDontSee($this->author->name);
    }

    #[Test]
    public function test_reviewer_can_accept_invitation_and_submit_review(): void
    {
        $round = ReviewRound::create([
            'submission_id' => $this->submission->id,
            'round_number' => 1,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $assignment = ReviewerAssignment::create([
            'review_round_id' => $round->id,
            'reviewer_id' => $this->reviewer->id,
            'response_status' => 'pending',
            'due_date' => now()->addDays(14),
        ]);

        // 1. Accept invitation
        $acceptResponse = $this->actingAs($this->reviewer)->post(route('reviewer.assignments.respond', $assignment->id), [
            'response_status' => 'accepted',
        ]);
        $acceptResponse->assertRedirect(route('reviewer.assignments.show', $assignment->id));
        $this->assertDatabaseHas('reviewer_assignments', [
            'id' => $assignment->id,
            'response_status' => 'accepted',
        ]);

        // 2. Submit evaluation
        $submitResponse = $this->actingAs($this->reviewer)->post(route('reviewer.assignments.submit', $assignment->id), [
            'recommendation' => 'minor_revision',
            'comments_for_author' => 'Metodologi sangat baik, namun mohon perbaiki persamaan matematika pada Bab 3.',
            'confidential_comments_to_editor' => 'Naskah berpotensi tinggi untuk sitasi, layak diterima setelah revisi minor.',
        ]);
        $submitResponse->assertRedirect(route('reviewer.assignments.show', $assignment->id));
        $this->assertDatabaseHas('reviewer_assignments', [
            'id' => $assignment->id,
            'response_status' => 'completed',
            'recommendation' => 'minor_revision',
        ]);

        // 3. Editor can see the reviewer comments and recommendation
        $editorShowResponse = $this->actingAs($this->editor)->get(route('editor.submissions.show', $this->submission->id));
        $editorShowResponse->assertStatus(200);
        $editorShowResponse->assertSee('Rekomendasi: Minor Rev');
        $editorShowResponse->assertSee('Metodologi sangat baik');
        $editorShowResponse->assertSee('Naskah berpotensi tinggi untuk sitasi');
    }
}
