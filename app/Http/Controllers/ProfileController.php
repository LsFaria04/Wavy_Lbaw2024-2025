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
    public function show($username) {

        $user = User::where('username', $username)->firstOrFail();
        $posts = $user->posts()->orderBy('createddate', 'desc')->paginate(10);
        $comments = [];
    
        return view('pages.profile', compact('user', 'posts', 'comments'));
    }
    
    
    public function update(Request $request, $userid) {
        $user = User::findOrFail($userid);
        $this->authorize('update', $user);
        
        try {
            $validatedData = $request->validate([
                'username' => [
                    'required',
                    'string',
                    'max:250',
                    'alpha_dash',
                    Rule::unique('users', 'username')->ignore($userid, 'userid'),
                ],
                'bio' => 'nullable|string|max:500',
                'visibilitypublic' => 'required|boolean',
            ]);
        
            $user->update($validatedData);

            Log::info('User profile updated', ['user_id' => $user->id, 'username' => $user->username]);
        
            return redirect()->route('profile', $user->username)
                             ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update user profile', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return redirect()->route('profile', $user->username)
                             ->with('error', 'Your changes were rejected.');
        }    
    }

    public function delete(Request $request, $id) {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        Log::info('Attempt to delete user', ['user_id' => $user->id, 'username' => $user->username]);


        if ($user->state === 'deleted') {

            Log::warning('User already deleted', ['user_id' => $user->id, 'username' => $user->username]);


            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has already been deleted.',
                ], 400); 
            }
    
            return redirect()->route('home')->with('error', 'User has already been deleted.');
        }
    
        DB::beginTransaction();  
    
        try {
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
    
            $user->username = NULL;
            $user->passwordhash = '';
            $user->bio = '';
            $user->email = NULL;
            $user->state = 'deleted';
            $user->save();

            Log::info('User successfully deleted', ['user_id' => $user->id, 'username' => $user->username]);
    
            DB::commit();
            
            if (Auth::user()->userid === $user->userid) {
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

            Log::error('Failed to delete user', ['user_id' => $user->id, 'username' => $user->username, 'error' => $e->getMessage()]);

    
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete the user.',
                ], 500);
            }
    
            return redirect()->route('home')->with('error', 'Failed to delete the user.');
        }
    }
    
    public function verifyPassword(Request $request) {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        Log::info('Password verification attempt', ['user_id' => $user->id, 'username' => $user->username]);


        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }

        if (Hash::check($request->password, $user->password)) {
            Log::info('Password verification successful', ['user_id' => $user->id, 'username' => $user->username]);
            return response()->json(['success' => true]);
        }

        Log::warning('Incorrect password attempt', ['user_id' => $user->id, 'username' => $user->username]);

        return response()->json(['success' => false, 'message' => 'Incorrect password']);
    }
    
}
