<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;


class UserPolicy {

    public function update(User $authUser, User $user) {
        return $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user) {
        return $authUser->id === $user->id || $authUser->isadmin;
    }

    public function createAdmin(User $user){
        return $user->isadmin;
    }
}
