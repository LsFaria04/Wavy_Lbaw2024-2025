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
    public function store(Request $request) {
        $validated = $request->validate([
            'groupname' => 'required|string|max:255|unique:groups,groupname',
            'description' => 'required|string',
            'visibilitypublic' => 'required|boolean',
        ]);
    
        try {
            // Create the group
            $group = Group::create([
                'groupname' => 'groupname',
                'description' => 'description',
                'visibilitypublic' => 'visibilitypublic',
                'ownerid' => Auth::id(),
            ]);
    
            if (!$group) {
                return redirect()->back()->withErrors(['error' => 'Failed to create the group. Please try again.']);
            }
    
            // Add the owner as a member
            $membership = GroupMembership::create([
                'groupid' => $group->groupid, // Use the ID from the created group
                'userid' => Auth::id(),
            ]);
    
            if (!$membership) {
                return redirect()->back()->withErrors(['error' => 'Failed to add the group owner as a member.']);
            }
    
            return redirect()->back()->with('success', 'Group created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }
    }

    /**
     * Display a specific group and its details.
     */
    public function show($name) {
        $group = Group::where('groupname', $name)->first();
    
        if (!$group) {
            return redirect('/home')->withErrors(['Group not found.']);
        }
    
        $posts = $group->posts()
                       ->with('user', 'media', 'topics', 'user.profilePicture') // Load necessary relationships
                       ->withCount('likes') 
                       ->withCount('comments')
                       ->orderBy('createddate', 'desc')
                       ->paginate(10);
    
        if (Auth::check()) {
            foreach ($posts as $post) {
                $post->liked = $post->likes()->where('userid', Auth::id())->exists();
                $post->createddate = $post->createddate->diffForHumans();
            }
        } else {
            foreach ($posts as $post) {
                $post->liked = false; // User is not logged in, can't like posts
                $post->createddate = $post->createddate->diffForHumans();
            }
        }
    
        return view('pages.group', compact('group', 'posts'));
    }    

    /**
     * Get all the data of a group and send it as JSON.
     */
    public function getGroupData($id) {
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
    public function getGroupPosts($id) {
        $group = Group::findOrFail($id);
    
        if (!$group->visibilitypublic &&
            !$group->members->contains(Auth::id()) &&
            $group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        $posts = $group->posts()
                       ->with('user', 'media', 'topics', 'user.profilePicture') 
                       ->withCount('likes') 
                       ->withCount('comments')
                       ->orderBy('createddate', 'desc')
                       ->paginate(10);
    
        if (Auth::check()) {
            foreach ($posts as $post) {
                $post->liked = $post->likes()->where('userid', Auth::id())->exists();
                $post->createddate = $post->createddate->diffForHumans();
            }
        } else {
            foreach ($posts as $post) {
                $post->liked = false; // User is not logged in, can't like posts
                $post->createddate = $post->createddate->diffForHumans();
            }
        }
    
        return response()->json($posts);
    }    

    /**
     * Get paginated members for a specific group.
     */
    public function getGroupMembers($id) {
        $group = Group::findOrFail($id);

        if (!$group->visibilitypublic &&
            !$group->members->contains(Auth::id()) &&
            $group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Log::info($group->members()->paginate(10));

        $members = $group->members()->with('profilePicture')->paginate(10);

        return response()->json($members);
    }

    /**
     * Get paginated invitations for a specific group.
     */
    public function getGroupInvitations($id) {
        $group = Group::findOrFail($id);

        if ($group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $invitations = GroupInvitation::with('user', 'user.profilePicture') 
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
    public function getJoinRequests($id) {
        $group = Group::findOrFail($id);

        if ($group->ownerid !== Auth::id() && !Auth::user()->isadmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $joinRequests = JoinGroupRequest::with('user', 'user.profilePicture')
            ->where('groupid', $id)
            ->orderBy('createddate', 'desc')
            ->paginate(10);

        for($i = 0;$i < sizeof($joinRequests); $i++){
            $joinRequests[$i]->createddate = $joinRequests[$i]->createddate->diffForHumans();
        }

        return response()->json($joinRequests);
    }

    public function sendInvitation(Request $request, $groupid) {
        $request->validate([
            'userid' => 'required|integer|exists:users,userid',
        ]);
    
        $group = Group::findOrFail($groupid);
    
        $this->authorize('sendInvitation', $group);
    
        $userid = $request->input('userid');
    
        // Check if the user is already a member
        if ($group->members()->where('group_membership.userid', $userid)->exists()) {
            return response()->json('User is already a member.', 400);
        }
    
        // Check if the user has a pending join request
        if (JoinGroupRequest::where('groupid', $groupid)->where('userid', $userid)->exists()) {
            return response()->json('User has already requested to join this group.', 400);
        }
    
        // Check if the user is already invited
        if (GroupInvitation::where('groupid', $groupid)->where('group_invitation.userid', $userid)->exists()) {
            return response()->json('User is already invited.', 400);
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

    public function cancelInvitation($groupid, $invitationid) {
        $group = Group::findOrFail($groupid);
        $this->authorize('sendInvitation', $group);
    
        $invitation = GroupInvitation::where('groupid', $groupid)
            ->where('invitationid', $invitationid)
            ->firstOrFail();
    
        $invitation->delete();
    
        return response()->json(['status' => 'success', 'message' => 'Invitation canceled successfully.'], 200);
    }
    
    public function acceptInvitation($groupid, $invitationid) {
        $invitation = GroupInvitation::where('groupid', $groupid)
            ->where('invitationid', $invitationid)
            ->firstOrFail();
    
        // Ensure the current user matches the invited user
        if ($invitation->userid !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Add the user to the group
        GroupMembership::create([
            'groupid' => $groupid,
            'userid' => Auth::id(),
        ]);
    
        // Delete the invitation
        $invitation->delete();
    
        return response()->json(['status' => 'success', 'message' => 'You have joined the group.']);
    }    
    
    public function rejectInvitation($groupid, $invitationid) {
        $invitation = GroupInvitation::where('groupid', $groupid)
            ->where('invitationid', $invitationid)
            ->firstOrFail();
    
        // Ensure the current user matches the invited user
        if ($invitation->userid !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Delete the invitation
        $invitation->delete();
    
        return response()->json(['status' => 'success', 'message' => 'You have rejected the invitation.']);
    }    

    public function sendJoinRequest(Request $request, $groupid) {
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

        // Check if the user has a pending join request
        if (GroupInvitation::where('groupid', $groupid)->where('userid', $user->userid)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'User has already been invited to join this group.'], 400);
        }

        // Create the join request
        JoinGroupRequest::create([
            'groupid' => $groupid,
            'userid' => $user->userid,
            'createddate' => now(),
        ]);

        return response()->json(['message' => 'Your join request has been sent successfully.'], 200);
    }

    public function rejectJoinRequest($groupid, $requestid) {
        $group = Group::findOrFail($groupid);
        $this->authorize('rejectJoinRequest', $group);
        
        $joinRequest = JoinGroupRequest::where('groupid', $groupid)
            ->where('requestid', $requestid)
            ->firstOrFail();

        $joinRequest->delete();

        return response()->json(['status' => 'success', 'message' => 'Join request rejected successfully.'], 200);
    }

    public function acceptJoinRequest($groupid, $requestid) {
        $group = Group::findOrFail($groupid);
        $this->authorize('acceptJoinRequest', $group);
        
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

    public function leaveGroup($groupid) {
        $user = Auth::user();
        $group = Group::findOrFail($groupid);
    
        $this->authorize('leaveGroup', $group);
    
        $membership = GroupMembership::where('groupid', $groupid)
            ->where('userid', $user->userid)
            ->first();
    
        if (!$membership) {
            return redirect()->back()->with('error', 'You are not a member of this group.');
        }
    
        $membership->delete();
    
        return redirect()->back()->with('success', 'You have successfully left the group.');
    }   

    public function removeMember(Request $request, $groupid, $userid) {
        $group = Group::findOrFail($groupid);
        $this->authorize('removeMember', $group);
    
        $membership = GroupMembership::where('groupid', $groupid)
            ->where('userid', $userid)
            ->first();
    
        if (!$membership) {
            return redirect()->back()->with('error', 'The user is not a member of this group.');
        }
    
        if ((int) $userid === (int) $group->ownerid) {
            return redirect()->back()->with('error', 'Group owners cannot be removed.');
        }
    
        $membership->delete();
    
        return redirect()->back()->with('success', "User has been removed from the group.");
    }
    
    public function deleteGroup($groupid) {
        $group = Group::findOrFail($groupid);
    
        $this->authorize('delete', $group);
    
        try {
            $group->delete();
    
            return redirect()->route('groupList')
                             ->with('success', 'Group deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete group', ['groupid' => $groupid, 'error' => $e->getMessage()]);
    
            return redirect()->route('group', $group->groupname)
                             ->with('error', 'Failed to delete the group. Please try again later.');
        }
    }    
}
