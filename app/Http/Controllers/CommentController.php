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
        $comments = Comment::with('post','comment', 'user')->where('userid', $user->id)->orderBy('createddate', 'desc')->paginate(10);
        
        if($request->ajax()){
            return response()->json($comments);
        }

        return $comments;
    }
}
