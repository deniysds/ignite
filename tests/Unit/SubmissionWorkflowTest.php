<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SubmissionWorkflowTest extends TestCase
{
    #[Test]
    public function submission_workflow_allows_only_supported_transitions(): void
    {
        $workflowClass = \App\Support\Editorial\SubmissionWorkflow::class;

        $this->assertTrue(class_exists($workflowClass), 'Submission workflow class must exist.');

        /** @var \App\Support\Editorial\SubmissionWorkflow $workflow */
        $workflow = app($workflowClass);

        $this->assertSame(['submitted'], $workflow->allowedTransitions('draft'));
        $this->assertTrue($workflow->canTransition('submitted', 'under_editor_review'));
        $this->assertTrue($workflow->canTransition('revision_required', 'resubmitted'));
        $this->assertFalse($workflow->canTransition('accepted', 'submitted'));
        $this->assertFalse($workflow->canTransition('rejected', 'under_editor_review'));
        $this->assertSame(['accepted', 'published', 'rejected'], $workflow->finalStatuses());
    }
}
