<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Shows notifications about comments on posts (for now, in the future it should show all notifications)
    public function index() {
        $notifications = Notification::with(['comment.post'])
            ->where('receiverid', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return view('pages.notifications', compact('notifications'));
    }

    protected function getCommentNotifications() {
        return Notification::with(['comment', 'comment.post', 'comment.post.user'])
            ->whereNotNull('commentid')
            ->whereHas('comment', function ($query) {
                $query->whereHas('post', function ($postQuery) {
                    $postQuery->where('userid', Auth::id());
                });
            })
            ->where('receiverid', Auth::id())
            ->get();
    }
    

    protected function markNotificationsAsSeen($notifications) {
        foreach ($notifications as $notification) {
            $notification->update(['seen' => true]); 
        }
    }
}
