<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupMembership;
use App\Models\JoinGroupRequest;
use App\Models\GroupInvitation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'groupname' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibilitypublic' => 'required|boolean',
        ]);

        // Create the group
        $group = Group::create([
            'groupname' => $request->input('groupname'),
            'description' => $request->input('description'),
            'visibilitypublic' => $request->input('visibilitypublic'),
            'ownerid' => Auth::id(),
        ]);

        // Add the owner as a member
        GroupMembership::create([
            'groupid' => $group->groupid, // Use the ID from the created group
            'userid' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Group created successfully!');
    }

    /**
     * Display a specific group and its details.
     */
    public function show($name)
    {
        $group = Group::where('groupname', $name)->first();

        if (!$group) {
            return redirect('/home')->withErrors(['Group not found.']);
        }

        $posts = $group->posts()->orderBy('createddate', 'desc')->paginate(10);

        return view('pages.group', compact('group', 'posts'));
    }

    /**
     * Get all the data of a group and send it as JSON.
     */
    public function getGroupData($id)
    {
        $group = Group::findOrFail($id);

        if (!$group->visibilitypublic && 
            !$group->members->contains(Auth::id()) && 
            $group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($group);
    }

    /**
     * Get paginated posts for a specific group.
     */
    public function getGroupPosts($id)
    {
        $group = Group::findOrFail($id);

        if (!$group->visibilitypublic &&
            !$group->members->contains(Auth::id()) &&
            $group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $posts = $group->posts()->with('user', 'media','topics')->orderBy('createddate', 'desc')->paginate(10);

        for($i = 0;$i < sizeof($posts); $i++){
            $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
        }

        return response()->json($posts);
    }

    /**
     * Get paginated members for a specific group.
     */
    public function getGroupMembers($id)
    {
        $group = Group::findOrFail($id);

        if (!$group->visibilitypublic &&
            !$group->members->contains(Auth::id()) &&
            $group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $members = $group->members()->paginate(10);

        return response()->json($members);
    }

    /**
     * Get paginated invitations for a specific group.
     */
    public function getGroupInvitations($id)
    {
        $group = Group::findOrFail($id);

        if ($group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $invitations = GroupInvitation::with('user') 
            ->where('groupid', $id)
            ->orderBy('createddate', 'desc')
            ->paginate(10);

        for($i = 0;$i < sizeof($invitations); $i++){
            $invitations[$i]->createddate = $invitations[$i]->createddate->diffForHumans();
        }

        return response()->json($invitations);
    }

    /**
     * Get paginated join requests for a specific group.
     */
    public function getJoinRequests($id)
    {
        $group = Group::findOrFail($id);

        if ($group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $joinRequests = JoinGroupRequest::with('user')
            ->where('groupid', $id)
            ->orderBy('createddate', 'desc')
            ->paginate(10);

        for($i = 0;$i < sizeof($joinRequests); $i++){
            $joinRequests[$i]->createddate = $joinRequests[$i]->createddate->diffForHumans();
        }

        return response()->json($joinRequests);
    }

    public function sendInvitation(Request $request, $groupid)
    {
        $request->validate([
            'userid' => 'required|integer|exists:users,userid',
        ]);

        $userid = $request->input('userid');
        $group = Group::findOrFail($groupid);

        // Check if user is already a member or invited
        if ($group->members()->where('group_membership.userid', $userid)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'User is already a member.'], 400);
        }

        if (GroupInvitation::where('groupid', $groupid)->where('group_invitation.userid', $userid)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'User is already invited.'], 400);
        }

        // Create the invitation
        GroupInvitation::create([
            'groupid' => $groupid,
            'userid' => $userid,
            'createddate' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'User invited successfully.'], 200);
    }
    
    public function update(Request $request, $groupid) {
        $group = Group::findOrFail($groupid);
        $this->authorize('update', $group);
        
        //try the data input validation
        try {
            $validatedData = $request->validate([
                'groupname' => [
                    'required',
                    'string',
                    'max:30',
                    'regex:/^[A-Za-z0-9 _-]+$/',
                    Rule::unique('groups', 'groupname')->ignore($groupid, 'groupid'),
                ],
                'description' => 'nullable|string|max:130',
                'visibilitypublic' => 'required|boolean',
            ]);
        
            $group->update($validatedData);

        
            return redirect()->route('group', $group->groupname)
                             ->with('success', 'Group Page updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update group page', ['groupid' => $group->groupid, 'error' => $e->getMessage()]);

            return redirect()->route('group', $group->groupname)
                             ->with('error', 'Your changes were rejected.');
        }    
    }

    public function cancelInvitation($groupid, $invitationid)
    {
        $invitation = GroupInvitation::where('groupid', $groupid)
            ->where('invitationid', $invitationid)
            ->firstOrFail();

        $invitation->delete();

        return response()->json(['status' => 'success', 'message' => 'Invitation canceled successfully.'], 200);
    }

    public function sendJoinRequest(Request $request, $groupid)
    {
        $user = Auth::user();

        // Check if the user is already a member or has sent a request
        $group = Group::findOrFail($groupid);
        if ($group->members->contains($user)) {
            return response()->json(['message' => 'You are already a member of this group.'], 400);
        }

        $existingRequest = JoinGroupRequest::where('groupid', $groupid)
            ->where('userid', $user->userid)
            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'You already have a pending join request.'], 400);
        }

        // Create the join request
        JoinGroupRequest::create([
            'groupid' => $groupid,
            'userid' => $user->userid,
            'createddate' => now(),
        ]);

        return response()->json(['message' => 'Your join request has been sent successfully.'], 200);
    }

    public function rejectJoinRequest($groupid, $requestid)
    {
        $joinRequest = JoinGroupRequest::where('groupid', $groupid)
            ->where('requestid', $requestid)
            ->firstOrFail();

        $joinRequest->delete();

        return response()->json(['status' => 'success', 'message' => 'Join request rejected successfully.'], 200);
    }

    public function acceptJoinRequest($groupid, $requestid)
    {
        $joinRequest = JoinGroupRequest::where('groupid', $groupid)
            ->where('requestid', $requestid)
            ->firstOrFail();

        $joinRequest->delete();

        $existingMembership = GroupMembership::where('groupid', $groupid)
            ->where('userid', $joinRequest->userid)
            ->first();

        if (!$existingMembership) {
            GroupMembership::create([
                'groupid' => $groupid,
                'userid' => $joinRequest->userid,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Join request accepted and user added to group.'], 200);
    }

    public function leaveGroup($groupid)
    {
        $user = Auth::user();
        $group = Group::findOrFail($groupid);

        // Check if the user is a member
        $membership = GroupMembership::where('groupid', $groupid)
            ->where('userid', $user->userid)
            ->first();

        if (!$membership) {
            return redirect()->back()->with('error', 'You are not a member of this group.');
        }

        // Prevent group owner from leaving the group
        if ($group->ownerid === $user->userid) {
            return redirect()->back()->with('error', 'Group owners cannot leave their own group.');
        }

        // Delete the membership
        $membership->delete();

        return redirect()->back()->with('success', 'You have successfully left the group.');
    }

    public function removeMember(Request $request, $groupid, $userid)
    {
        $group = Group::findOrFail($groupid);
        // Check if the user is a member
        $membership = GroupMembership::where('groupid', $groupid)
            ->where('userid', $userid)
            ->first();
            
        if (!$membership) {
            return redirect()->back()->with('error', 'The user is not a member of this group.');
        }
    
        // Prevent group owner from leaving the group
        if ((int) $userid === (int) $group->ownerid) {
            return redirect()->back()->with('error', 'Group owners cannot be removed.');
        }

        // Delete the membership
        $membership->delete();

        return redirect()->back()->with('success', "User has been removed from the group.");
    }

}
