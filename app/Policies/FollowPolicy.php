<?php

namespace App\Policies;

use App\Models\User;

class FollowPolicy {

    public function follow(User $authUser, User $followee) {
        // users cannot follow themselves
        if ($authUser->userid === $followee->userid) {
            return false;
        }

        // cannot follow admins
        if ($followee->isadmin) {
            return false;
        }

        return true;
    }

    public function acceptFollow(User $authUser, User $follower) {
        // only the followee can accept a follow request
        return $authUser->userid === $follower->userid;
    }

    public function rejectFollow(User $authUser, User $follower) {
        // only the followee can reject a follow request
        return $authUser->userid === $follower->userid;
    }

}
