<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Mail\ReviewCompletedMail;
use Modules\Submissions\Mail\ReviewerInvitationMail;
use Modules\Submissions\Mail\SubmissionReceivedMail;
use Modules\Submissions\Models\ReviewRound;
use Modules\Submissions\Models\ReviewerAssignment;
use Modules\Submissions\Models\Submission;
use Modules\Submissions\Models\SubmissionAuthor;
use Modules\Submissions\Models\SubmissionFile;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditorialEmailNotificationsTest extends TestCase
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
            'abstract' => 'Comprehensive framework using deep learning to curate genomic variants.',
            'keywords' => ['Genomics', 'Deep Learning', 'Variants'],
            'status' => 'submitted',
            'submitted_at' => now()->subDays(5),
            'corresponding_author_id' => $this->author->id,
            'assigned_editor_id' => $this->editor->id,
            'created_by' => $this->author->id,
        ]);

        SubmissionAuthor::create([
            'submission_id' => $this->submission->id,
            'name' => $this->author->name,
            'email' => $this->author->email,
            'is_corresponding_author' => true,
            'sort_order' => 1,
        ]);
    }

    #[Test]
    public function editor_assigning_reviewer_dispatches_reviewer_invitation_mail(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->editor)->post(route('editor.submissions.assign_reviewer', $this->submission->id), [
            'reviewer_id' => $this->reviewer->id,
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

        $response->assertRedirect(route('editor.submissions.show', $this->submission->id));

        Mail::assertSent(ReviewerInvitationMail::class, function ($mail) {
            return $mail->hasTo($this->reviewer->email) &&
                   str_contains($mail->envelope()->subject, 'IMG') &&
                   str_contains($mail->envelope()->subject, 'Deep Learning Genomics for Variant Curation in Indonesia');
        });
    }

    #[Test]
    public function author_submitting_final_manuscript_dispatches_submission_received_mail(): void
    {
        Mail::fake();

        $draftSubmission = Submission::create([
            'journal_id' => $this->journal->id,
            'title' => 'Single-cell RNA sequencing in Tropical Diseases',
            'slug' => 'single-cell-rna-seq-tropical-diseases',
            'abstract' => 'Single cell transcriptome profiling reveals immune cell signatures.',
            'status' => 'draft',
            'corresponding_author_id' => $this->author->id,
            'created_by' => $this->author->id,
        ]);

        SubmissionFile::create([
            'submission_id' => $draftSubmission->id,
            'file_role' => 'naskah_utama',
            'disk' => 'public',
            'path' => 'submissions/sample.docx',
            'original_name' => 'manuscript.docx',
        ]);

        $response = $this->actingAs($this->author)->post(route('submissions.finish.step4', $draftSubmission->id));

        $response->assertRedirect(route('submissions.index'));

        Mail::assertSent(SubmissionReceivedMail::class, function ($mail) use ($draftSubmission) {
            return $mail->hasTo($this->author->email) &&
                   str_contains($mail->envelope()->subject, 'Single-cell RNA sequencing in Tropical Diseases');
        });
    }

    #[Test]
    public function reviewer_submitting_review_dispatches_review_completed_mail_to_editor(): void
    {
        Mail::fake();

        $round = ReviewRound::create([
            'submission_id' => $this->submission->id,
            'round_number' => 1,
            'status' => 'in_progress',
        ]);

        $assignment = ReviewerAssignment::create([
            'review_round_id' => $round->id,
            'reviewer_id' => $this->reviewer->id,
            'invitation_token' => 'test-invitation-token-12345',
            'response_status' => 'accepted',
            'due_date' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->reviewer)->post(route('reviewer.assignments.submit', $assignment->id), [
            'recommendation' => 'accept',
            'comments_for_author' => 'Naskah penelitian ini disusun dengan metode yang sangat komprehensif dan valid.',
            'confidential_comments_to_editor' => 'Rekomendasi saya naskah ini layak langsung diterima.',
        ]);

        $response->assertRedirect(route('reviewer.assignments.show', $assignment->id));

        Mail::assertSent(ReviewCompletedMail::class, function ($mail) {
            return $mail->hasTo($this->editor->email) &&
                   str_contains($mail->envelope()->subject, 'Telaah Sejawat Selesai') &&
                   str_contains($mail->envelope()->subject, 'Deep Learning Genomics for Variant Curation in Indonesia');
        });
    }

    #[Test]
    public function reviewer_can_access_workspace_via_invitation_token(): void
    {
        $round = ReviewRound::create([
            'submission_id' => $this->submission->id,
            'round_number' => 1,
            'status' => 'in_progress',
        ]);

        $assignment = ReviewerAssignment::create([
            'review_round_id' => $round->id,
            'reviewer_id' => $this->reviewer->id,
            'invitation_token' => 'secret-token-xyz-987',
            'response_status' => 'pending',
            'due_date' => now()->addDays(14),
        ]);

        $response = $this->actingAs($this->reviewer)->get(route('reviewer.invitation.handle', $assignment->invitation_token));

        $response->assertRedirect(route('reviewer.assignments.show', $assignment->id));
    }
}
