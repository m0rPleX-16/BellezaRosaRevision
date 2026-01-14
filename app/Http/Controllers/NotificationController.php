<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get the authenticated user.
     *
     * @return User
     */
    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }

    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $user = $this->user();
        $notifications = $user->notifications()->paginate(20);

        // Mark all notifications as read when viewing the full list
        $user->unreadNotifications()->get()->each(function ($notification) {
            $notification->markAsRead();
        });

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($notificationId)
    {
        $notification = $this->user()->notifications()->find($notificationId);
        
        if ($notification && $notification->unread()) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unread_count' => $this->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        // Get all unread notifications and mark each as read
        $this->user()->unreadNotifications()->get()->each(function ($notification) {
            $notification->markAsRead();
        });

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount()
    {
        return response()->json([
            'unread_count' => $this->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Get latest notifications.
     */
    public function latest()
    {
        $notifications = $this->user()->notifications()->take(10)->get();

        return response()->json([
            'html' => view('partials.notifications.list', [
                'notifications' => $notifications
            ])->render(),
            'unread_count' => $notifications->where('read_at', null)->count()
        ]);
    }
}
