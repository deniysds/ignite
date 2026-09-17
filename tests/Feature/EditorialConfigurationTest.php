<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EditorialConfigurationTest extends TestCase
{
    #[Test]
    public function editorial_configuration_exposes_safe_defaults(): void
    {
        $this->assertSame('single', config('editorial.journal_mode'));
        $this->assertSame('double_blind', config('editorial.peer_review_model'));
        $this->assertTrue(config('editorial.external_reviewer_registration_enabled'));
        $this->assertSame(['pdf', 'html'], config('editorial.publication_formats'));
        $this->assertSame(7, config('editorial.sla.desk_review_days'));
        $this->assertSame(14, config('editorial.sla.review_round_days'));
        $this->assertSame(14, config('editorial.sla.author_revision_days'));
        $this->assertSame('post_acceptance', config('editorial.issue_assignment_timing'));
    }
}
