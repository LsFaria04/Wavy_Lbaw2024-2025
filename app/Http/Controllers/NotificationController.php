<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller {
    public function index() {       
        $notifications = Notification::with(['comment.post', 'like.post', 'comment.user','like.comment', 'follow.follower'])
            ->where('receiverid', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(20);

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

        $category = $request->input('category');
        $notifications = null;
        switch($category){
            case 'all-notifications':
                $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'like.user','like.comment', 'follow.follower'])
                    ->where('receiverid', Auth::id())
                    ->orderBy('date', 'desc')
                    ->paginate(20);
                break;
            case 'likes':
                $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'like.user','like.comment', 'follow.follower'])
                    ->where('receiverid', Auth::id())
                    ->whereNotNull('likeid')
                    ->orderBy('date', 'desc')
                    ->paginate(20);
                break;
            case 'comments':
                $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'like.user','like.comment', 'follow.follower'])
                    ->where('receiverid', Auth::id())
                    ->whereNotNull('commentid')
                    ->orderBy('date', 'desc')
                    ->paginate(20);
                break;
            case 'follows':
                $notifications = Notification::with(['comment.post', 'like.post', 'comment.user', 'like.user', 'like.comment','follow.follower'])
                    ->where('receiverid', Auth::id())
                    ->whereNotNull('followid')
                    ->orderBy('date', 'desc')
                    ->paginate(20);
                break;
        }
    
        
        
        foreach($notifications as $notification){
            $notification->date =  Carbon::parse($notification->date)->diffForHumans();
        }

        return response()->json(
            $notifications
        );
    }
    


    // Function to mark notifications as seen (still needs to be fully implemented)
    protected function markNotificationsAsSeen($notifications)
    {
        Notification::whereIn('notificationid', $notifications->pluck('notificationid'))
            ->update(['seen' => true]);
    }
}
