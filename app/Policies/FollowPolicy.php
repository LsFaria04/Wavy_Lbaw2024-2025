<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;


class FollowPolicy {

    public function follow(User $authUser, User $followee) {
        \Log::info("Checking follow permission: AuthUser ID = {$authUser->userid}, Followee ID = {$followee->userid}, Followee Admin Status = {$followee->isadmin}");
    
        // users cannot follow themselves
        if ($authUser->userid === $followee->userid) {
            \Log::info("User cannot follow themselves.");
            return false;
        }
    
        // cannot follow admins
        if ($followee->isadmin) {
            \Log::info("User cannot follow an admin.");
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
