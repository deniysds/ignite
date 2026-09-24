<?php

namespace Tests\Feature;

use App\Notifications\EditorialNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\Submission;
use Modules\Usermanagement\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Dr. Bambang Wijaya',
            'email' => 'bambang@ignite.ac.id',
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function user_can_receive_in_app_notification(): void
    {
        $this->user->notify(new EditorialNotification(
            title: 'Naskah Diterima',
            message: 'Naskah penelitian Anda telah resmi diterima untuk publikasi.',
            actionUrl: '/submissions/10',
            type: 'success',
            icon: 'ki-check-circle'
        ));

        $this->assertEquals(1, $this->user->notifications()->count());
        $this->assertEquals(1, $this->user->unreadNotifications()->count());

        $notification = $this->user->unreadNotifications->first();
        $this->assertEquals('Naskah Diterima', $notification->data['title']);
        $this->assertEquals('/submissions/10', $notification->data['action_url']);
    }

    #[Test]
    public function authenticated_user_can_view_notifications_center_page(): void
    {
        $this->user->notify(new EditorialNotification(
            title: 'Undangan Review',
            message: 'Anda diundang menelaah naskah baru.',
            actionUrl: '/reviewer/assignments/1',
            type: 'info'
        ));

        $response = $this->actingAs($this->user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Notifikasi');
        $response->assertSee('Undangan Review');
        $response->assertSee('Anda diundang menelaah naskah baru.');
    }

    #[Test]
    public function user_can_mark_notification_as_read_and_redirect_to_target(): void
    {
        $this->user->notify(new EditorialNotification(
            title: 'Revisi Diperlukan',
            message: 'Silakan lakukan revisi minor.',
            actionUrl: '/dashboard',
            type: 'warning'
        ));

        $notification = $this->user->unreadNotifications->first();

        $response = $this->actingAs($this->user)->post(route('notifications.mark_as_read', $notification->id));

        $response->assertRedirect('/dashboard');
        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }

    #[Test]
    public function user_can_mark_all_notifications_as_read(): void
    {
        $this->user->notify(new EditorialNotification(title: 'Notif 1', message: 'Pesan 1'));
        $this->user->notify(new EditorialNotification(title: 'Notif 2', message: 'Pesan 2'));

        $this->assertEquals(2, $this->user->unreadNotifications()->count());

        $response = $this->actingAs($this->user)->post(route('notifications.mark_all_read'));

        $response->assertSessionHas('success');
        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }

    #[Test]
    public function unread_count_endpoint_returns_json(): void
    {
        $this->user->notify(new EditorialNotification(title: 'Alert', message: 'Test message'));

        $response = $this->actingAs($this->user)->get(route('notifications.count'));

        $response->assertStatus(200);
        $response->assertJson(['unread_count' => 1]);
    }
}
