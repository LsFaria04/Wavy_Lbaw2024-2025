<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;


class CommentPolicy {


    public function create(User $user) {
        //Only allows the user to post if they are not an admin
        return !$user->isadmin && ($user->state !== 'suspended');
    }

    public function delete(User $user, Comment $comment) {
        // Allow the delete action only if the user owns the post or is an admin
        return $user->userid === $comment->userid || $user->isadmin;
    }

    public function edit(User $user, Comment $comment) {
        // Allow the edit action only if the user owns the post or is an admin
        return ($user->userid === $comment->userid && ($user->state !== 'suspended')) || $user->isadmin;
    }
}
