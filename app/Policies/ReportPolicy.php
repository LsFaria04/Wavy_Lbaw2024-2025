<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Report;

class ReportPolicy {
    /**
     * Create a new policy instance.
     */
    public function __construct() {
        //
    }

    
    public function create (User $user) {
        //admins cannot make reports
        return !$user->isadmin;    
    }

    public function alreadyReported(User $user, $contentId, $isPost) {
        //verify if the user already reported that content
        if($isPost) {
            $report = Report::where('postid', $contentId)
                    ->where('userid', $user->userid)
                    ->first();
            return $report === null;
        }
        else {
            $report = Report::where('commentid', $contentId)
                ->where('userid', $user->userid)
                ->first();
            
            return $report === null;
        }
    }

    public function delete (User $user) {
        //only admins can remove reports
        return $user->isadmin;
    }

    public function get (User $user) {
        //only admins can get reports from the db to view and manage them
        return $user->isadmin;
    }
}
