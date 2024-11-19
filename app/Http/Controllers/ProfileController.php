<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show($username) {
        $user = User::where('username', $username)
            ->with(['posts', 'comments'])
            ->firstOrFail();
        return view('pages.profile', compact('user'));
    }

    public function update(Request $request, $userid) {
        $user = User::findOrFail($userid);
        $this->authorize('update', $user);
        
        try {
            $validatedData = $request->validate([
                'username' => 'required|string|max:20',
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
