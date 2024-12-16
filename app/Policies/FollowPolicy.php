<?php

namespace App\Policies;

use App\Models\User;

class FollowPolicy {

    public function follow(User $authUser, User $user) {
        if ($authUser->userid === $user->userid) {
            return false; 
        }
        
        if ($authUser->isadmin) {
            return false; 
        }

        return true;
    }
    
}
