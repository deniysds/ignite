<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocaleSwitcherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('module:migrate', ['module' => 'Usermanagement']);
        $this->artisan('module:migrate', ['module' => 'Journals']);
        $this->artisan('module:migrate', ['module' => 'Issues']);
        $this->artisan('module:migrate', ['module' => 'Website']);
    }

    public function test_user_can_switch_locale_to_english_and_session_is_stored(): void
    {
        $response = $this->get(route('locale.switch', 'en'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');
    }

    public function test_user_can_switch_locale_to_indonesian_and_session_is_stored(): void
    {
        $response = $this->get(route('locale.switch', 'id'));

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'id');
    }

    public function test_invalid_locale_is_ignored_and_not_set_in_session(): void
    {
        $response = $this->withSession(['locale' => 'id'])
            ->get(route('locale.switch', 'fr'));

        $response->assertRedirect();
        $this->assertEquals('id', session('locale'));
    }

    public function test_public_navbar_renders_indonesian_translations_by_default(): void
    {
        $response = $this->withSession(['locale' => 'id'])->get(route('website.home'));

        $response->assertStatus(200);
        $response->assertSee('Beranda');
        $response->assertSee('Profil');
        $response->assertSee('Program & Jurnal');
        $response->assertSee('Publikasi');
        $response->assertSee('Panduan');
        $response->assertSee('Pengumuman');
        $response->assertSee('Mitra Kami');
    }

    public function test_public_navbar_renders_english_translations_when_locale_is_english(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get(route('website.home'));

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Profile');
        $response->assertSee('Programs & Journals');
        $response->assertSee('Publications');
        $response->assertSee('Guidelines');
        $response->assertSee('Announcements');
        $response->assertSee('Our Partners');
    }

    public function test_admin_header_includes_locale_switcher_links(): void
    {
        $admin = \Modules\Usermanagement\Models\User::factory()->create();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('locale.switch', 'id'));
        $response->assertSee(route('locale.switch', 'en'));
    }
}
