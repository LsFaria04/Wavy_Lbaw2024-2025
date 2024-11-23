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
        $comments = Like::with('post', 'comment', 'user')->where('userid', $user->id)->orderBy('createddate', 'desc')->paginate(10);
        
        if($request->ajax()){
            return response()->json($comments);
        }

        return $comments;
    }
    
}
