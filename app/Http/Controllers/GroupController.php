<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupMembership;
use App\Models\JoinGroupRequest;
use App\Models\GroupInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function show($groupid)
    {
        // Retrieve the group using groupid
        $group = Group::find($groupid);

        // If no group is found, redirect to home or another page
        if (!$group) {
            return redirect('/home');
        }

        $posts = [];
        $members = [];

        // Pass the group data to the view
        return view('pages.group', compact('group'));
    }

    // Create a new group
    public function create(Request $request)
    {
        $request->validate([
            'groupname' => 'required|string|max:30|unique:groups,groupname',
            'description' => 'string|max:255',
            'visibilitypublic' => 'boolean',
        ]);

        $group = Group::create([
            'groupname' => $request->groupName,
            'description' => $request->description,
            'visibilitypublic' => $request->visibilityPublic ?? true,
            'ownerid' => Auth::id(),
        ]);

        return response()->json(['message' => 'Group created successfully.', 'group' => $group], 201);
    }

    // Get group details
    public function getGroup($id)
    {
        $group = Group::with('members')->findOrFail($id);

        if (!$group->visibilitypublic && !$group->members->contains(Auth::id()) && $group->ownerid !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to view this group.'], 403);
        }

        return response()->json($group);
    }

    // Join a group (request membership)
    public function requestJoin($id)
    {
        $group = Group::findOrFail($id);

        if ($group->ownerid === Auth::id() || GroupMembership::where(['groupid' => $id, 'userid' => Auth::id()])->exists()) {
            return response()->json(['message' => 'You are already in this group or own it.'], 400);
        }

        JoinGroupRequest::updateOrCreate(
            ['groupid' => $id, 'userid' => Auth::id()],
            ['state' => 'Pending']
        );

        return response()->json(['message' => 'Join request sent successfully.']);
    }

    // Respond to a join request
    public function respondJoinRequest(Request $request, $groupid, $userid)
    {
        $request->validate([
            'state' => 'required|string|in:Accepted,Rejected',
        ]);

        $group = Group::findOrFail($groupid);

        if ($group->ownerid !== Auth::id()) {
            return response()->json(['error' => 'Only the group owner can respond to join requests.'], 403);
        }

        $joinRequest = JoinGroupRequest::where(['groupid' => $groupid, 'userid' => $userid])->firstOrFail();

        $joinRequest->update(['state' => $request->state]);

        return response()->json(['message' => 'Join request responded successfully.']);
    }

    // Invite a user to a group
    public function inviteUser(Request $request, $id)
    {
        $request->validate([
            'userid' => 'required|exists:users,userid',
        ]);

        $group = Group::findOrFail($id);

        if ($group->ownerid !== Auth::id()) {
            return response()->json(['error' => 'Only the group owner can invite users.'], 403);
        }

        GroupInvitation::updateOrCreate(
            ['groupid' => $id, 'userid' => $request->userid],
            ['state' => 'Pending']
        );

        return response()->json(['message' => 'User invited successfully.']);
    }

    // Respond to a group invitation
    public function respondInvitation(Request $request, $id)
    {
        $request->validate([
            'state' => 'required|string|in:Accepted,Rejected',
        ]);

        $invitation = GroupInvitation::where(['groupid' => $id, 'userid' => Auth::id()])->firstOrFail();

        $invitation->update(['state' => $request->state]);

        return response()->json(['message' => 'Invitation responded successfully.']);
    }

    // Delete a group
    public function deleteGroup($id)
    {
        $group = Group::findOrFail($id);

        if ($group->ownerid !== Auth::id()) {
            return response()->json(['error' => 'Only the group owner can delete the group.'], 403);
        }

        $group->delete();

        return response()->json(['message' => 'Group deleted successfully.']);
    }
}

