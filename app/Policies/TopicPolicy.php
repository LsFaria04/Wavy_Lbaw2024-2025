<?php

namespace App\Policies;

use App\Models\User;

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
        return $user->isadmin;
    }

    /*
    Only users can manage their own topics
    */
    public function userTopics(User $user, $userid) {
        return $user->userid == $userid;
    }
}
