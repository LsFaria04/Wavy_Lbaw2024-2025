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

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'passwordhash' => Hash::make($validated['password']),
            'state' => 'active',
            'visibilitypublic' => true,
            'isadmin' => false,
        ]);

        return response()->json(['success' => true]);
    }
    
    public function edit($id) {
        $user = User::find($id);
        if ($user) {
            return response()->json(['success' => true, 'user' => $user]);
        }
        return response()->json(['success' => false]);
    }

    public function update(Request $request, $id) {
        $user = User::find($id);
        if ($user) {
            $user->update($request->only('username', 'email', 'state', 'visibilitypublic', 'isadmin'));
            return response()->json(['success' => true, 'user' => $user]);
        }
        return response()->json(['success' => false]);
    }


    public function destroyUser($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
}
