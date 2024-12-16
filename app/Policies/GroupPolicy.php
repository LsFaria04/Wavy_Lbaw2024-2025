<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    /**
     * Determine if a user can view a group.
     * Public groups can be viewed by anyone.
     * Private groups can only be viewed by the owner or members.
     */
    public function view(User $user, Group $group) {
        return $group->visibilitypublic || $group->ownerid === $user->userid || $group->members->contains($user->userid) || $user->isadmin;
    }

    /**
     * Determine if a user can update a group.
     * Only the group owner can update group details.
     */
    public function update(User $user, Group $group) {
        return $group->ownerid === $user->userid || $user->isadmin;
    }

    /**
     * Determine if a user can delete a group.
     * Only the group owner can delete the group.
     */
    public function delete(User $user, Group $group) {
        return $group->ownerid === $user->userid || $user->isadmin;
    }

    /**
     * Determine if a user can invite others to a group.
     * Only the group owner or designated administrators can invite members.
     */
    public function sendInvitation(User $user, Group $group) {
        return $group->ownerid === $user->userid || $user->isadmin;
    }

    /**
     * Determine if a user can manage join requests (accept/reject).
     * Only the group owner or administrators can manage join requests.
     */
    public function acceptJoinRequest(User $user, Group $group) {
        return $group->ownerid === $user->userid || $group->administrators->contains($user->userid) || $user->isadmin;
    }
    
    public function rejectJoinRequest(User $user, Group $group) {
        return $group->ownerid === $user->userid || $group->administrators->contains($user->userid) || $user->isadmin;
    }

    /**
     * Determine if a user can remove a member from the group.
     * Only the group owner or administrators can remove members.
     * Group owners cannot be removed.
     */
    public function removeMember(User $user, Group $group) {
        return $group->ownerid === $user->userid || $user->isadmin;
    }

    /**
     * Determine if a user can leave the group.
     * Any member, except the owner, can leave the group.
     */
    public function leaveGroup(User $user, Group $group) {
        return $group->ownerid !== $user->userid && $group->members->contains($user->userid);
    }
}
