<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{

    /**
     * Gets the likes created by user (By username)
     */
    function getUserLikesByUsername(Request $request, $username){
        $user = User::where('username', $username)->firstOrFail();
    
        $likes = Like::with([
            'post' => function ($query) {
                $query->with('user', 'user.profilePicture')->withCount('likes')->withCount('comments');
            },
            'comment' => function ($query) {
                $query->withCount('likes');
            }, 'comment.user', 'comment.post', 'comment.post.user',
            'comment.parentComment', 'comment.parentComment.user', 'user'
        ])
        ->where('userid', $user->userid)
        ->orderBy('createddate', 'desc')
        ->paginate(10);
    
        foreach ($likes as $like) {
            if ($like->post) {
                $like->post->createddate = $like->post->createddate->diffForHumans();
    
                if (Auth::check()) {
                    $like->post->liked = $like->post->likes()->where('userid', Auth::id())->exists();
                } else {
                    $like->post->liked = false;
                }
            }
    
            if ($like->comment) {
                $like->comment->createddate = $like->comment->createddate->diffForHumans();
    
                if (Auth::check() && $like->comment->post) {
                    $like->comment->post->liked = $like->comment->post->likes()->where('userid', Auth::id())->exists();
                }
            }
        }
    
        if ($request->ajax()) {
            return response()->json($likes);
        }
    
        return $likes;
    }
    
    public function create(Request $request){~

        Like::create([
            'userid' => $request->userid,
            'postid' => $request->postid,
            'commentid' => $request->commentid,
            'createddate' => $request->createddate,
        ]);
        
    }
    
}
