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
    /**
     * Display a specific group and its details.
     */
    public function show($id)
    {
        $group = Group::with('members')->find($id);

        if (!$group) {
            return redirect('/home')->withErrors(['Group not found.']);
        }

        if (!$group->visibilitypublic 
            && !$group->members->contains(Auth::id()) 
            && $group->ownerid !== Auth::id()) {
            return redirect('/home')->withErrors(['You do not have access to view this group.']);
        }

        return view('pages.group', compact('group'));
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

        $posts = $group->posts()->with('user', 'media')->orderBy('createddate', 'desc')->paginate(10);

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
            ->where('state', 'Pending') 
            ->orderBy('date', 'desc')
            ->paginate(10);

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
            ->where('state', 'Pending') 
            ->orderBy('date', 'desc')
            ->paginate(10);

        return response()->json($joinRequests);
    }

    /**
     * Create a new group.
     */
    public function create(Request $request)
    {
        $request->validate([
            'groupname' => 'required|string|max:30|unique:groups,groupname',
            'description' => 'nullable|string|max:255',
            'visibilitypublic' => 'required|boolean',
        ]);

        $group = Group::create([
            'groupname' => $request->groupname,
            'description' => $request->description ?? '',
            'visibilitypublic' => $request->visibilitypublic ?? true,
            'ownerid' => Auth::id(),
        ]);

        return response()->json(['message' => 'Group created successfully.', 'group' => $group], 201);
    }    
}
