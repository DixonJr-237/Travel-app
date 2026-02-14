<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->notifications();

        // Filter by read/unread status
        if ($request->has('status')) {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);

        $notification->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        $user->notifications()
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
        ]);
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $count = $user->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }
}
