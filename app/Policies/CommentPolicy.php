<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;


class CommentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user){
        //Only allow the user to post if it is not an admin
        return !$user->isadmin;
    }

    public function delete(User $user, Comment $comment)
    {
        // Allow the delete action only if the user owns the post or is an admin
        return $user->userid === $comment->userid || $user->isadmin;
    }

    public function edit(User $user, Comment $comment){
        // Allow the edit action only if the user owns the post or is an admin
        return $user->userid === $comment->userid || $user->isadmin;
    }
}
