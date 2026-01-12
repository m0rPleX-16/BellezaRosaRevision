<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        // Mark all notifications as read when viewing the full list
        Auth::user()->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAllRead()
    {
        // Get all unread notifications
        $notifications = Auth::user()->unreadNotifications;

        // Mark each notification as read
        $notifications->each(function ($notification) {
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
            'unread_count' => Auth::user()->unreadNotifications->count()
        ]);
    }

    /**
     * Get latest notifications.
     */
    public function latest()
    {
        $notifications = Auth::user()->notifications()->take(10)->get();

        return response()->json([
            'html' => view('partials.notifications.list', [
                'notifications' => $notifications
            ])->render(),
            'unread_count' => $notifications->where('read_at', null)->count()
        ]);
    }
}
