<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show($username) {
        $user = User::where('username', $username)->firstOrFail();
    
        $posts = $user->posts()->orderBy('createddate', 'desc')->get();
        $comments = $user->comments()->orderBy('createddate', 'desc')->get();
    
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
        
            return redirect()->route('profile', $user->username)
                             ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('profile', $user->username)
                             ->with('error', 'Your changes were rejected.');
        }    
    }
    
}
