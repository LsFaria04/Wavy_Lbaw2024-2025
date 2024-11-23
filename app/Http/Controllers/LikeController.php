<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Like;

class LikeController extends Controller
{

    /**
     * Gets the likes created by user (By username)
     */
    function getUserLikesByUsername(Request $request, $username){
        
        $user = User::where('username', $username)->firstOrFail();
        $likes = Like::with('post', 'post.user', 'comment', 'comment.user', 'comment.post','comment.post.user', 'comment.parentComment', 'comment.parentComment.user', 'user')->where('userid', $user->userid)->orderBy('createddate', 'desc')->paginate(10);
        
        for($i = 0;$i < sizeof($likes); $i++){
            if($likes[$i]->comment !== null){
                $likes[$i]->comment->createddate = $likes[$i]->comment->createddate->diffForHumans();
            }
            else{
                $likes[$i]->post->createddate = $likes[$i]->post->createddate->diffForHumans();
            }
        }

        if($request->ajax()){
            return response()->json($likes);
        }

        return $likes;
    }
    
}
