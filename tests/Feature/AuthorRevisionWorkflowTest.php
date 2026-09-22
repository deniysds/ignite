<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\ReviewRound;
use Modules\Submissions\Models\ReviewerAssignment;
use Modules\Submissions\Models\Submission;
use Modules\Submissions\Models\SubmissionAuthor;
use Modules\Submissions\Models\SubmissionHistory;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthorRevisionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;
    protected User $editor;
    protected User $reviewer;
    protected Journal $journal;
    protected Submission $submission;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->author = User::factory()->create([
            'name' => 'Dr. Vincentius Budhyanto',
            'email' => 'vincent@ignite.ac.id',
        ]);

        $this->editor = User::factory()->create([
            'name' => 'Dr. Editor In Chief',
            'email' => 'eic@ignite.ac.id',
        ]);

        $this->reviewer = User::factory()->create([
            'name' => 'Prof. Secret Reviewer',
            'email' => 'reviewer@mitrabestari.ac.id',
        ]);

        $this->journal = Journal::create([
            'name' => 'Indonesian Medical Genomics',
            'slug' => 'img',
            'short_name' => 'IMG',
            'is_active' => true,
        ]);

        $this->submission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Deep Learning Genomics for Variant Curation in Indonesia',
            'slug' => 'deep-learning-genomics-variant-curation',
            'abstract' => 'Comprehensive framework using deep learning to curate variants.',
            'keywords' => ['Genomics', 'Deep Learning', 'Variants'],
            'status' => 'revision_required',
            'submitted_at' => now()->subDays(10),
            'corresponding_author_id' => $this->author->id,
            'created_by' => $this->author->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name' => $this->author->name,
            'email' => $this->author->email,
            'is_corresponding_author' => true,
            'sort_order' => 1,
        ]);

        SubmissionHistory::create([
            'submission_id' => $this->submission->id,
            'from_status'   => 'under_review',
            'to_status'     => 'revision_required',
            'actor_id'      => $this->editor->id,
            'notes'         => 'Mohon lakukan revisi mayor terutama pada metodologi sampling DNA dan validasi model.',
            'happened_at'   => now()->subDays(1),
        ]);

        $round = ReviewRound::create([
            'submission_id' => $this->submission->id,
            'round_number'  => 1,
            'status'        => 'completed',
        ]);

        ReviewerAssignment::create([
            'review_round_id'     => $round->id,
            'reviewer_id'         => $this->reviewer->id,
            'response_status'     => 'accepted',
            'recommendation'      => 'major_revision',
            'comments_for_author' => 'Metodologi sangat potensial, namun perlu ditambahkan perbandingan dengan algoritma konvensional.',
            'confidential_comments_to_editor' => 'Paper ini bagus tapi penulis harus memperbaiki benchmark.',
            'submitted_at'        => now()->subDays(2),
        ]);
    }

    #[Test]
    public function author_can_view_revision_instructions_and_double_blind_reviewer_comments(): void
    {
        $response = $this->actingAs($this->author)
            ->get(route('submissions.show', $this->submission->id));

        $response->assertStatus(200);
        $response->assertSee('Instruksi &amp; Pengunggahan Naskah Revisi', false);
        $response->assertSee('Mohon lakukan revisi mayor terutama pada metodologi sampling DNA');
        $response->assertSee('Metodologi sangat potensial, namun perlu ditambahkan perbandingan dengan algoritma konvensional.');
        $response->assertSee('Penelaah / Mitra Bestari #1');

        // Double-blind verification: author cannot see reviewer's real name or confidential comments
        $response->assertDontSee('Prof. Secret Reviewer');
        $response->assertDontSee('Paper ini bagus tapi penulis harus memperbaiki benchmark.');
    }

    #[Test]
    public function author_can_resubmit_revised_manuscript_and_notes(): void
    {
        $fakeFile = UploadedFile::fake()->create('naskah_revisi_v2.docx', 1200, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->author)
            ->post(route('submissions.resubmit', $this->submission->id), [
                'file'           => $fakeFile,
                'response_notes' => 'Kami telah merevisi metodologi sampling dan menambahkan perbandingan algoritma konvensional sesuai masukan Reviewer 1.',
            ]);

        $response->assertRedirect(route('submissions.show', $this->submission->id));
        $response->assertSessionHas('success');

        // Verify database status
        $this->submission->refresh();
        $this->assertEquals('resubmitted', $this->submission->status);

        // Verify file stored and record created
        $this->assertDatabaseHas('submission_files', [
            'submission_id' => $this->submission->id,
            'file_role'     => 'naskah_revisi',
            'original_name' => 'naskah_revisi_v2.docx',
        ]);

        // Verify history logged
        $this->assertDatabaseHas('submission_histories', [
            'submission_id' => $this->submission->id,
            'from_status'   => 'revision_required',
            'to_status'     => 'resubmitted',
        ]);
    }

    #[Test]
    public function author_cannot_resubmit_if_status_is_not_revision_required(): void
    {
        $this->submission->update(['status' => 'submitted']);

        $fakeFile = UploadedFile::fake()->create('naskah_revisi.docx', 500);

        $response = $this->actingAs($this->author)
            ->post(route('submissions.resubmit', $this->submission->id), [
                'file'           => $fakeFile,
                'response_notes' => 'Mencoba submit saat status submitted.',
            ]);

        $response->assertRedirect(route('submissions.show', $this->submission->id));
        $response->assertSessionHasErrors('status');

        $this->submission->refresh();
        $this->assertEquals('submitted', $this->submission->status);
    }

    #[Test]
    public function other_user_cannot_resubmit_another_authors_submission(): void
    {
        $otherUser = User::factory()->create([
            'email' => 'other@ignite.ac.id',
        ]);

        $fakeFile = UploadedFile::fake()->create('hack.docx', 500);

        $response = $this->actingAs($otherUser)
            ->post(route('submissions.resubmit', $this->submission->id), [
                'file'           => $fakeFile,
                'response_notes' => 'Upaya modifikasi naskah orang lain.',
            ]);

        $response->assertStatus(404);
    }
}
