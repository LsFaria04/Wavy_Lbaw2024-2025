<?php

namespace App\Policies;

use App\Models\User;

class MediaPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, Media $post){
        // Allow the delete action only if the user owns the post or is an admin
        return $user->userid === $media->userid || $user->isadmin;
    }
}
