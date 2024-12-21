<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller {
    public function index() {       
        $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'follow.follower'])
            ->where('receiverid', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);

        $commentNotifications = $notifications->filter(function ($notification) {
            return isset($notification->comment);
        });

        $likeNotifications = $notifications->filter(function ($notification) {
            return isset($notification->like);
        });

        $followNotifications = $notifications->filter(function ($notification) {
            return isset($notification->follow);
        });

        return view('pages.notifications', compact('notifications', 'commentNotifications', 'likeNotifications', 'followNotifications'));
    }

    public function getNotifications(Request $request) {
        Log::info('Received Request for Notifications', ['page' => $request->get('page')]);

        $page = $request->get('page', 1);
    
        $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'like.user'])
            ->where('receiverid', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10, ['*'], 'page', $page);

        Log::info('Notifications Loaded', ['total_notifications' => $notifications->total()]);
        Log::info('Notifications Data', ['data' => $notifications->items()]);


        return response()->json([
            'notifications' => $notifications->items(),
            'last_page' => $notifications->lastPage(),
        ]);
    }
    


    // Function to mark notifications as seen (still needs to be fully implemented)
    protected function markNotificationsAsSeen($notifications)
    {
        Notification::whereIn('notificationid', $notifications->pluck('notificationid'))
            ->update(['seen' => true]);
    }
}
