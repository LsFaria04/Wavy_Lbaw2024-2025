<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
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

    public function delete(User $user, Post $post)
    {
        // Allow the delete action only if the user owns the post or is an admin
        return $user->userid === $post->userid || $user->isadmin;
    }
}
