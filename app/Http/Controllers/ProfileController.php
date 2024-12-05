<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{   
    /*
    * Show the profile of the user with the same username as the one provided
    */
    public function show($username) {

        //gets the firts set of data
        $user = User::where('username', $username)->first();

        if (!$user) {
            return redirect('/home');
        }

        $posts = $user->posts()->whereNull('groupid')->orderBy('createddate', 'desc')->paginate(10);
        $comments = [];
    
        return view('pages.profile', compact('user', 'posts', 'comments'));
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
}
