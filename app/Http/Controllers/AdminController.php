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

        $posts = $query->paginate(10); // Paginate 10 posts per page

        // Similarly for users
        $usersQuery = User::query();
        if ($request->has('search_users')) {
            $usersQuery->where('username', 'like', '%' . $request->input('search_users') . '%');
        }

        $users = $usersQuery->paginate(10);

        return view('pages.admin', compact('posts', 'users'));
    }
}
