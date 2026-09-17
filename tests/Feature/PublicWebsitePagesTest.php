<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicWebsitePagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_open_the_public_website_pages(): void
    {
        $this->get(route('website.home'))->assertOk();
        $this->get(route('website.journals.index'))->assertOk();
        $this->get(route('website.issues.archive'))->assertOk();
    }
}
