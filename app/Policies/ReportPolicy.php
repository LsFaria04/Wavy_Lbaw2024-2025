<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    
    public function create (User $user, $reportedUserId){
        //only normal users can create and the reported user cannot be and admin
        $reportedUser = User::find($reportedUserId)->firstOrFail();
        return !$user->isadmin && !$reportedUser->isadmin;
    }

    public function delete (User $user){
        //only admins can remove reports
        return $user->isadmin;
    }

    public function get (User $user){
        //only admins can get reports from the db to view and manage them
        return $user->isadmin;
    }
}
