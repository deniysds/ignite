<?php

namespace App\Support\Editorial;

use Illuminate\Support\Facades\Log;

class SubmissionWorkflow
{
    /**
     * Returns the allowed next statuses for the given submission status.
     *
     * @param string $status
     *
     * @return array<int, string>
     */
    public function allowedTransitions(string $status): array
    {
        return $this->transitions()[$status] ?? [];
    }

    /**
     * Determines whether a submission is allowed to move to the target status.
     *
     * @param string $fromStatus
     * @param string $toStatus
     *
     * @return bool
     */
    public function canTransition(string $fromStatus, string $toStatus): bool
    {
        $canTransition = in_array($toStatus, $this->allowedTransitions($fromStatus), true);

        Log::info('Checking submission status transition.', [
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'allowed' => $canTransition,
        ]);

        return $canTransition;
    }

    /**
     * Returns the statuses that end the active editorial workflow.
     *
     * @return array<int, string>
     */
    public function finalStatuses(): array
    {
        return ['accepted', 'published', 'rejected'];
    }

    /**
     * Returns the supported submission workflow transitions.
     *
     * @return array<string, array<int, string>>
     */
    protected function transitions(): array
    {
        return [
            'draft' => ['submitted'],
            'submitted' => ['under_editor_review', 'under_review', 'rejected'],
            'under_editor_review' => ['under_review', 'revision_required', 'accepted', 'rejected'],
            'under_review' => ['revision_required', 'accepted', 'rejected'],
            'revision_required' => ['resubmitted'],
            'resubmitted' => ['under_editor_review', 'under_review'],
            'accepted' => ['published'],
            'published' => [],
            'rejected' => [],
        ];
    }
}
