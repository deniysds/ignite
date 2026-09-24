<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Journals\Models\Journal;
use Modules\Journals\Models\JournalAnnouncement;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JournalAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Journal $journal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Jurnal',
            'email' => 'admin@ignite.ac.id',
            'email_verified_at' => now(),
        ]);

        $this->journal = Journal::create([
            'name' => 'Jurnal Teknologi Informasi',
            'slug' => 'jti',
            'short_name' => 'JTI',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function authenticated_user_can_view_announcements_index(): void
    {
        JournalAnnouncement::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Call for Papers Vol. 5 No. 1',
            'type'         => 'call_for_papers',
            'content'      => 'Silakan kirimkan manuskrip terbaik Anda.',
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('announcements.index'));

        $response->assertStatus(200);
        $response->assertSee('Call for Papers Vol. 5 No. 1');
    }

    #[Test]
    public function admin_can_create_a_call_for_papers_announcement(): void
    {
        $payload = [
            'journal_id'   => $this->journal->id,
            'title'        => 'Call for Papers Edisi Khusus AI & Robotika',
            'type'         => 'call_for_papers',
            'summary'      => 'Pengumuman penerimaan naskah untuk topik Artificial Intelligence.',
            'content'      => '<p>Topik yang diterima meliputi Machine Learning, Computer Vision, dll.</p>',
            'deadline'     => now()->addMonths(2)->format('Y-m-d'),
            'is_pinned'    => '1',
            'is_published' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('announcements.store'), $payload);

        $response->assertRedirect(route('announcements.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('journal_announcements', [
            'journal_id'   => $this->journal->id,
            'title'        => 'Call for Papers Edisi Khusus AI & Robotika',
            'type'         => 'call_for_papers',
            'is_pinned'    => true,
            'is_published' => true,
        ]);
    }

    #[Test]
    public function admin_can_update_announcement(): void
    {
        $announcement = JournalAnnouncement::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Judul Pengumuman Awal',
            'type'         => 'general',
            'content'      => 'Konten awal.',
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('announcements.update', $announcement->id), [
            'journal_id'   => $this->journal->id,
            'title'        => 'Judul Pengumuman Diperbarui',
            'type'         => 'event',
            'content'      => 'Konferensi internasional berlangsung bulan depan.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('announcements.index'));
        $announcement->refresh();
        $this->assertEquals('Judul Pengumuman Diperbarui', $announcement->title);
        $this->assertEquals('event', $announcement->type);
    }

    #[Test]
    public function admin_can_toggle_published_status(): void
    {
        $announcement = JournalAnnouncement::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Pengumuman Draf',
            'type'         => 'general',
            'content'      => 'Isi draf.',
            'is_published' => false,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('announcements.toggle', $announcement->id));

        $response->assertRedirect(route('announcements.index'));
        $announcement->refresh();
        $this->assertTrue($announcement->is_published);
    }

    #[Test]
    public function admin_can_delete_announcement(): void
    {
        $announcement = JournalAnnouncement::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Pengumuman Akan Dihapus',
            'type'         => 'general',
            'content'      => 'Konten.',
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('announcements.destroy', $announcement->id));

        $response->assertRedirect(route('announcements.index'));
        $this->assertSoftDeleted('journal_announcements', ['id' => $announcement->id]);
    }

    #[Test]
    public function public_visitors_can_view_published_announcements_on_website(): void
    {
        JournalAnnouncement::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'CFP: Naskah Terpilih 2026',
            'type'         => 'call_for_papers',
            'summary'      => 'Ringkasan CFP untuk publik.',
            'content'      => 'Isi lengkap CFP.',
            'deadline'     => now()->addDays(30),
            'is_published' => true,
        ]);

        JournalAnnouncement::create([
            'journal_id'   => $this->journal->id,
            'title'        => 'Draf Rahasia Internal',
            'type'         => 'general',
            'content'      => 'Belum dipublikasikan.',
            'is_published' => false,
        ]);

        $response = $this->get(route('website.announcements'));

        $response->assertStatus(200);
        $response->assertSee('CFP: Naskah Terpilih 2026');
        $response->assertDontSee('Draf Rahasia Internal');
    }
}
