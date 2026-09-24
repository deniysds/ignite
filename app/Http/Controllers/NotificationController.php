<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications of the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $filter = $request->get('filter', 'all');
        $query = $user->notifications();

        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('pages.notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read and optionally redirect to its action URL.
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $actionUrl = $notification->data['action_url'] ?? null;
            if ($actionUrl) {
                return redirect($actionUrl);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Return current unread notifications count for AJAX header badge.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        return response()->json([
            'unread_count' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
    }
}
