<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller {   
    /*
    * Show the profile of the user with the same username as the one provided
    */
    public function show($username) {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return redirect('/home')->with('error', 'User not found.');
        }

        $posts = $user->posts()->whereNull('groupid')->orderBy('createddate', 'desc')->paginate(10);
        $comments = [];

        $followStatus = null;

        if (Auth::check() && Auth::id() !== $user->userid) {
            if (Follow::isFollowing(Auth::id(), $user->userid)) {
                $followStatus = Follow::STATE_ACCEPTED;
            } elseif (Follow::isPending(Auth::id(), $user->userid)) {
                $followStatus = Follow::STATE_PENDING;
            }
        }

        return view('pages.profile', compact('user', 'posts', 'comments', 'followStatus'));
    }

    //gets all the data of a user and sends it as json 
    public function getProfileUserData($username) {
        $user = User::where('username', $username)->firstOrFail();
        return response()->json($user);
    }
    
    //used to update the information of a user when he edits its profile
    public function update(Request $request, $userid) {
        $user = User::findOrFail($userid);
        $this->authorize('update', $user);
        
        //try the data input validation
        try {
            $validatedData = $request->validate([
                'username' => [
                    'required',
                    'string',
                    'max:30',
                    'alpha_dash',
                    Rule::unique('users', 'username')->ignore($userid, 'userid'),
                ],
                'bio' => 'nullable|string|max:130',
                'visibilitypublic' => 'required|boolean',
            ]);
        
            $user->update($validatedData);

        
            return redirect()->route('profile', $user->username)
                             ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update user profile', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return redirect()->route('profile', $user->username)
                             ->with('error', 'Your changes were rejected.');
        }    
    }

    /*
    * Used to delete a user from the site. Only removes the personal informations but retains it's if and published contents
    */
    public function delete(Request $request, $id) {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

    
        $currentUser = Auth::user();
        $isAdmin = $currentUser->isadmin === true;
        
        //if user isn't admin we need to check if the password inserted is valid
        if (!$isAdmin) {
            $request->validate([
                'password' => 'required|string',
            ]);
    
            $passwordMatches = Hash::check($request->password, $currentUser->passwordhash);
    
            if (!$passwordMatches) {
    
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Incorrect password',
                    ], 403); 
                }
    
                return redirect()->route('profile', $user->username)->with('error', 'Incorrect password. Deletion aborted.');
            }
        }
        
        //user already deleted
        if ($user->state === 'deleted') {

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has already been deleted.',
                ], 400);
            }
    
            return redirect()->route('home')->with('error', 'User has already been deleted.');
        }
        
        //starts a transaction to delete the user
        DB::beginTransaction();
        try {
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
    
            $user->username = NULL;
            $user->passwordhash = '';
            $user->bio = '';
            $user->email = NULL;
            $user->state = 'deleted';
            $user->save();
    
            DB::commit();
    
            if ($currentUser->userid === $user->userid) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
    
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully!',
                ]);
            }
    
            return redirect()->route('home')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
    
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete the user.',
                ], 500);
            }
    
            return redirect()->route('home')->with('error', 'Failed to delete the user.');
        }
    }

    public function follow(Request $request, $userid) {
        // logged-in user (follower)
        $follower = Auth::user();

        // check if user is authenticated
        if (!$follower) {
            return response()->json(['error' => 'You must be logged in to follow someone.'], 401);
        }

        // user to be followed (followee)
        $followee = User::findOrFail($userid);

        \Log::info("Attempting to follow: Follower ID = {$follower->userid}, Followee ID = {$followee->userid}");


        try {
            $this->authorize('follow', [Follow::class, $followee]); 
        } catch (AuthorizationException $e) {
            \Log::info("Authorization failed: User cannot follow this user.");
            return response()->json(['error' => 'You cannot follow this user.'], 403);
        }

        try {
            // Check if a follow relationship already exists
            $existingFollow = Follow::where('followerid', $follower->userid)
                ->where('followeeid', $followee->userid)
                ->first();

            // Handle existing relationships
            if ($existingFollow) {
                if ($existingFollow->state === Follow::STATE_ACCEPTED) {
                    // Unfollow the user
                    $existingFollow->delete();
                    return response()->json([
                        'success' => true,
                        'status' => 'Unfollowed'
                    ]);
                } elseif ($existingFollow->state === Follow::STATE_PENDING) {
                    // Follow request already sent
                    return response()->json([
                        'success' => false,
                        'message' => 'Follow request already sent.',
                        'status' => 'Pending'
                    ]);
                } elseif ($existingFollow->state === Follow::STATE_REJECTED) {
                    // Resend the follow request
                    $existingFollow->update([
                        'state' => Follow::STATE_PENDING,
                        'followdate' => now(),
                    ]);
                    return response()->json([
                        'success' => true,
                        'status' => 'Pending'
                    ]);
                }
            } else {
                // No existing follow relationship, create a new one
                $status = $followee->visibilitypublic ? Follow::STATE_ACCEPTED : Follow::STATE_PENDING;

                Follow::create([
                    'followerid' => $follower->userid,
                    'followeeid' => $followee->userid,
                    'state' => $status,
                    'followdate' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $status === Follow::STATE_ACCEPTED ? 'Accepted' : 'Pending'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server problem. Try again.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
