<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class TopicPolicy {
    /*
    Only admins can create topics
    */
    public function create(User $user) {
        return $user->isadmin;
    }

    /*
    Only admins can delete topics
    */
    public function delete(User $user) {
        Log::info(strval($user->isadmin));
        return $user->isadmin;
    }

    /*
    Only users can manage their own topics
    */
    public function userTopics(User $user, $userid) {
        return $user->userid == $userid;
    }
}
