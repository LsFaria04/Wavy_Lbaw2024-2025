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
            
            if($request->ajax()){
                return response()->json(['message' => 'User was already deleted', 'response' => '403']);
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
    
            if($request->ajax()){
                return response()->json(['message' => 'User deleted sucessfully', 'response' => '200']);
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
            if($request->ajax()){
                return response()->json(['message' => 'Server Problem', 'response' => '500']);
            }
            return redirect()->route('home')->with('error', 'Failed to delete the user.');
        }
    }

    public function follow(Request $request, $userid) {
        // logged-in user (follower)
        $follower = Auth::user();
        \Log::info('1');
        // check if user is authenticated
        if (!$follower) {
            \Log::info('3');
            return response()->json(['error' => 'You must be logged in to follow someone.'], 401);
        }
        \Log::info('2');
        // user to be followed (followee)
        $followee = User::findOrFail($userid);
        \Log::info('4');
        \Log::info("Attempting to follow: Follower ID = {$follower->userid}, Followee ID = {$followee->userid}");
        
        \Log::info('6');
        try {
            \Log::info('Before authorization.');
            $this->authorize('follow', [Follow::class, $followee]);
            \Log::info('After authorization.');
        } catch (\Exception $e) {
            \Log::error('Authorization failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Authorization error.',
                'details' => $e->getMessage()
            ], 500);
        }
        
        \Log::info('7');
        try {
            \Log::info('8');
            // Check if a follow relationship already exists
            $existingFollow = Follow::where('followerid', $follower->userid)
                ->where('followeeid', $followee->userid)
                ->first();
                \Log::info('9');
            // Handle existing relationships
            if ($existingFollow) {
                \Log::info('10');
                if ($existingFollow->state === Follow::STATE_ACCEPTED) {
                    \Log::info('11');
                    // Unfollow the user
                    $existingFollow->delete();
                    return response()->json([
                        'success' => true,
                        'status' => 'Unfollowed'
                    ]);
                } elseif ($existingFollow->state === Follow::STATE_PENDING) {
                    \Log::info('12');
                    // Follow request already sent
                    return response()->json([
                        'success' => false,
                        'message' => 'Follow request already sent.',
                        'status' => 'Pending'
                    ]);
                } elseif ($existingFollow->state === Follow::STATE_REJECTED) {
                    \Log::info('13');
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
                \Log::info('14');
                // No existing follow relationship, create a new one
                $status = $followee->visibilitypublic ? Follow::STATE_ACCEPTED : Follow::STATE_PENDING;
                \Log::info('15');

                Follow::create([
                    'followerid' => $follower->userid,
                    'followeeid' => $followee->userid,
                    'state' => $status,
                    'followdate' => now(),
                ]);
                \Log::info('16');

                return response()->json([
                    'success' => true,
                    'status' => $status === Follow::STATE_ACCEPTED ? 'Accepted' : 'Pending'
                ]);
            }
        } catch (\Exception $e) {
            \Log::info('17');
            \Log::error('Error creating follow relationship: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server problem. Try again.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
