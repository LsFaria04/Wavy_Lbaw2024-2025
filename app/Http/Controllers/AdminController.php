<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

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

        return view('pages.admin', compact('posts', 'users'));
    }

    public function createUser() {
        return view('admin.users.create');
    }

    public function storeUser(Request $request) {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.editUser', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
