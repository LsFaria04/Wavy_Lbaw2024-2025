<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Gets the comments created by user (By username)
     */
    function getUserCommentsByUsername(Request $request, $username){
        
        $user = User::where('username', $username)->firstOrFail();
        $comments = Comment::with('post', 'post.user','parentComment', 'parentComment.user', 'user')->where('userid', $user->userid)->orderBy('createddate', 'desc')->paginate(10);

        for($i = 0;$i < sizeof($comments); $i++){
            $comments[$i]->createddate = $comments[$i]->createddate->diffForHumans();
        }

        
        if($request->ajax()){
            return response()->json($comments);
        }

        return $comments;
    }
}
