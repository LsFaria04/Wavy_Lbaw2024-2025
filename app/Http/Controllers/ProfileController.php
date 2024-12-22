<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\Follow;
use App\Events\FollowNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller {   
    /*
    * Show the profile of the user with the same username as the one provided
    */
    public function show($username) {
        $user = User::with('profilePicture')
            ->withCount(['followers' => function ($query) { $query->where('follow.state', 'Accepted');}])
            ->withCount(['follows' => function ($query) { $query->where('follow.state', 'Accepted');}])
            ->where('username', $username)
            ->first();
    
        if (!$user) {
            return redirect('/home')->with('error', 'User not found.');
        }
    
        if ($user->getIsAdmin() && Auth::id() !== $user->userid) {
            return redirect('/home')->with('error', 'User not found.');
        }
    
        // Include likes_count and other relevant relationships
        $posts = $user->posts()
                      ->whereNull('groupid')
                      ->orderBy('createddate', 'desc')
                      ->withCount('likes') 
                      ->withCount('comments') 
                      ->paginate(10);
    
        if (Auth::check()) {
            foreach ($posts as $post) {
                $post->liked = $post->likes()->where('userid', Auth::id())->exists();
                $post->createddate = $post->createddate->diffForHumans();
            }
        } else {
            foreach ($posts as $post) {
                $post->liked = false;
                $post->createddate = $post->createddate->diffForHumans();
            }
        }
    
        $comments = [];
    
        $followStatus = 'not-following'; 
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
    
        Log::info($request);
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
                'profilePic' => 'nullable|mimes:jpeg,png,jpg|max:10000'
            ]);

            // Handle new file uploads
            if ($request->hasFile('profilePic')) {

                $previousFile = Media::where('path', 'Like', 'images/profile%')->where('userid', $userid)->first();
                if($previousFile && Storage::exists('public/' . $previousFile->path)){
                    Storage::delete('public/' . $previousFile->path);
                    $previousFile->delete();
                }

                $fileName = 'profile' . $userid . '.' . $request->profilePic->extension();
                $mediaPath = $request->file('profilePic')->storeAs('images', $fileName, 'public');

                    // Create new media record for the post
                Media::create([
                    'postid' => null,
                    'userid' => $userid, // Assuming the media belongs to the authenticated user
                    'path' => $mediaPath,
                ]);
            }
            if ($request->hasFile('bannerPic')) {

                $previousFile = Media::where('path', 'Like', 'images/banner%')->where('userid', $userid)->first();
                if($previousFile && Storage::exists('public/' . $previousFile->path)){
                    Storage::delete('public/' . $previousFile->path);
                    $previousFile->delete();
                }

                $fileName = 'banner' . $userid . '.' . $request->profilePic->extension();
                $mediaPath = $request->file('bannerPic')->storeAs('images', $fileName, 'public');

                    // Create new media record for the post
                Media::create([
                    'postid' => null,
                    'userid' => $userid, // Assuming the media belongs to the authenticated user
                    'path' => $mediaPath,
                ]);
            }
            
        
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

        $files = Media::where('userid', $id)->get();
        foreach($files as $file){
            if(Storage::exists('public/' . $file->path)){
                Storage::delete('public/' . $file->path);
                $file->delete();
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
        Log::info("1");

        // logged-in user (follower)
        $follower = Auth::user();
        
        if (!$follower) {
            return response()->json(['error' => 'You must be logged in to follow someone.'], 401);
        }
        Log::info("2");

        // user to be followed (followee)
        $followee = User::findOrFail($userid);
    
        try {
            $this->authorize('follow', [Follow::class, $followee]);
            Log::info("3");

        } catch (\Exception $e) {
            Log::info("4");

            return response()->json([
                'error' => 'Authorization error.',
                'details' => $e->getMessage()
            ], 500);
        }

        // check if a follow already exists
        $existingFollow = Follow::where('followerid', $follower->userid)
                                ->where('followeeid', $followee->userid)
                                ->first();

        if ($existingFollow) {
            Log::info("5");

            return $this->handleExistingFollow($existingFollow, $request);
        } else {
            Log::info("6");

            return $this->createFollowRequest($follower, $followee);
        }
    }
    
    private function handleExistingFollow(Follow $existingFollow, $request) {
        Log::info("7");

        if ($existingFollow->state === Follow::STATE_ACCEPTED) {
            Log::info("8");
            return $this->unfollow($request, $existingFollow->followeeid);

        } elseif ($existingFollow->state === Follow::STATE_PENDING) {

            Log::info("9");

            Follow::where('followerid', $existingFollow->followerid)
            ->where('followeeid', $existingFollow->followeeid)
            ->delete();

            $follower = User::findOrFail($existingFollow->followerid);

            event(new FollowNotification($follower, $existingFollow->followeeid, 'follow-request-canceled'));

            return response()->json([
                'success' => true,
                'status' => 'follow-request-canceled',
                'message' => 'Follow request canceled.'
            ]);
        }
    }
    
    
    
    private function createFollowRequest($follower, $followee) {
        Log::info("10");

        $status = $followee->visibilitypublic ? Follow::STATE_ACCEPTED : Follow::STATE_PENDING;

        Follow::create([
            'followerid' => $follower->userid,
            'followeeid' => $followee->userid,
            'state' => $status,
            'followdate' => now(),
        ]);
        Log::info("11");

        if ($status == Follow::STATE_ACCEPTED) {
            $status = 'follow';
        }
        else if ($status == Follow::STATE_PENDING) {
            $status = 'follow-request';
        }
        event(new FollowNotification($follower, $followee->userid, $status));

        return response()->json([
            'success' => true,
            'status' => $status === Follow::STATE_ACCEPTED ? 'follow' : 'follow-request'
        ]);
    }

    public function getFollowRequests(Request $request, $userid){
        Log::info("13");

        try{
            $followers = Follow::with('follower', 'follower.profilePicture')
            ->where('followeeid', $userid )
            ->where('state', Follow::STATE_PENDING)->paginate(10);
        } catch (\Exception $e) {
            Log::info("15");

            return response()->json(['message' => 'Server Problem', 'response' => '500']);
        }
        Log::info("16");

        return response()->json($followers);
    }

    public function getFollows(Request $request, $userid){
        try{
            $followers = Follow::with('followee', 'followee.profilePicture')
            ->where('followerid', $userid )
            ->where('state', Follow::STATE_ACCEPTED)->paginate(10);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Problem', 'response' => '500']);
        }

        return response()->json($followers);
    }


    public function getFollowers(Request $request, $userid){
        try{
            $followers = Follow::with('follower', 'follower.profilePicture')
            ->where('followeeid', $userid )
            ->where('state', Follow::STATE_ACCEPTED)->paginate(10);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Problem', 'response' => '500']);
        }

        return response()->json($followers);
    }

    public function acceptFollowRequest(Request $request, $userid){
        try{
            DB::update('update follow set state = ? where followeeid = ? and followerid = ?', [Follow::STATE_ACCEPTED, Auth::user()->userid, $userid]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Problem', 'response' => '500']);
        }

        return response()->json(["message" => "Follow request accepted successfully", "response" => "200", "acceptedId" => $userid]);
    }

    public function rejectFollowRequest(Request $request, $userid){
        try{
            $follow = Follow::where('followeeid',Auth::user()->userid )->where('followerid', $userid);
            $follow->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Problem', 'response' => '500']);
        }


        return response()->json(["message" => "Follow request rejected successfully", "response" => "200", "rejectedId" => $userid]);
    }
    
    public function unfollow(Request $request, $userid) {
        Log::info("20");

        $followerId = auth()->user()->userid;

        $existingFollow = Follow::where('followerid', $followerId)
            ->where('followeeid', $userid)
            ->first();
        

        if (!$existingFollow) {
            Log::info("21");

            return response()->json([
                'success' => false,
                'message' => 'Follow relationship not found.',
            ], 404);
        }
        Log::info("22");

        $existingFollow->delete();
        Log::info("23");

        $previousNotification = Notification::where('followid', $followerId)
        ->where('receiverid', $userid)
        ->first();

        if ($previousNotification) {
            $previousNotification->delete();
        }

        $follower = User::findOrFail($existingFollow->followerid);

        Log::info('Follower type', ['type' => get_class($follower)]);

        event(new FollowNotification($follower, $userid, 'unfollowed'));
        
        Log::info("pois");
        return response()->json([
            'success' => true,
            'status' => 'Unfollowed'
        ]);
    }
}
