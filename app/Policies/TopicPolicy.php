<?php

namespace App\Policies;

use App\Models\User;

class TopicPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /*
    Only admins can create topics
    */
    public function create(User $user){
        return $user->isadmin;
    }

    /*
    Only admins can delete topics
    */
    public function delete(User $user){
        return $user->isadmin;
    }
}
