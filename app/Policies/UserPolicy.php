<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;


class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user) {
        return $authUser->id === $user->id || $authUser->isadmin;
    }
}
