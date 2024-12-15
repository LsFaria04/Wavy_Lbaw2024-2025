<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'like.user'])
                ->where('receiverid', Auth::id())
                ->orderBy('date', 'desc')
                ->paginate(10); 

            $commentNotifications = $notifications->filter(function($notification) {
                return isset($notification->comment);
            });

            $likeNotifications = $notifications->filter(function($notification) {
                return isset($notification->like);
            });

            $followNotifications = $notifications->filter(function($notification) {
                return isset($notification->follower) && isset($notification->followee);
            });

            return response()->json([
                'notifications' => $notifications->items(),
                'next_page' => $notifications->hasMorePages() ? $notifications->currentPage() + 1 : null,
            ]);
        }

        $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'like.user'])
            ->where('receiverid', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);

        $commentNotifications = $notifications->filter(function($notification) {
            return isset($notification->comment);
        });

        $likeNotifications = $notifications->filter(function($notification) {
            return isset($notification->like);
        });

        $followNotifications = $notifications->filter(function($notification) {
            return isset($notification->follower) && isset($notification->followee);
        });

        return view('pages.notifications', compact('notifications', 'commentNotifications', 'likeNotifications', 'followNotifications'));
    }

    // Function to mark notifications as seen (still needs to be fully implemented)
    protected function markNotificationsAsSeen($notifications)
    {
        Notification::whereIn('notificationid', $notifications->pluck('notificationid'))
            ->update(['seen' => true]);
    }
}
