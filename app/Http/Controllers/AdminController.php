<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 


class AdminController extends Controller
{
    public function index(Request $request) {
        // Search and Filter logic
        $query = Post::query();

        if ($request->has('search')) {
            $query->where('message', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('filter')) {
            $query->where('visibilitypublic', $request->input('filter'));
        }

        $posts = $query->with('user')->paginate(10); // Paginate 10 posts per page

        // Similarly for users
        $usersQuery = User::query();

        if ($request->has('search_users')) {
            $usersQuery->where('username', 'like', '%' . $request->input('search_users') . '%');
        }

        $users = $usersQuery->paginate(10);

        if ($request->ajax()) {
            if ($request->has('section') && $request->input('section') === 'posts') {
                return view('partials.admin.posts-table', compact('posts'))->render();
            } elseif ($request->has('section') && $request->input('section') === 'users') {
                return view('partials.admin.users-table', compact('users'))->render();
            }
        }

        return view('pages.admin', compact('posts', 'users'));
    }

    public function editPost($postId) {
        $post = Post::findOrFail($postId);
        return view('partials.admin.edit-post', compact('post'));
    }

    public function createUser() {
        return view('partials.admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        // Create the user and hash the password
        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'passwordhash' => Hash::make($validated['password']), // Ensure password_hash is set
            'state' => 'active',
            'visibilitypublic' => true,
            'isadmin' => false,
        ]);
    
        return redirect()->route('admin.index')->with('success', 'User created successfully.');
    }
    
    
    public function editUser($id) {
        $user = User::findOrFail($id);
        return view('partials.admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id) {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',userid',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',userid',
        ]);

        $user->update($validated);

        return redirect()->route('admin.index')->with('success', 'User updated successfully.');
    }

    public function destroyUser($id) {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
