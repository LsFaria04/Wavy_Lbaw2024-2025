<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function view(User $user, Group $group)
    {
        return $group->visibilityPublic || $group->ownerid === $user->userid || $group->members->contains($user->userid);
    }

    public function update(User $user, Group $group)
    {
        return $group->ownerid === $user->userid;
    }

    public function delete(User $user, Group $group)
    {
        return $group->ownerid === $user->userid;
    }

    public function join(User $user, Group $group)
    {
        return !$group->members->contains($user->userid) && $group->ownerid !== $user->userid;
    }

    public function invite(User $user, Group $group)
    {
        return $group->ownerid === $user->userid;
    }
}
