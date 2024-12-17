<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth;

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
    
    //returns the create user form to the admin page
    public function createUser() {
        return view('partials.admin.create-user');
    }

    //stores a user when it is created in the admin page
    public function storeUser(Request $request)
    {   
        
        try{
            $this->authorize('createAdmin', User::class);
        }catch(\Exception $e){
            return response()->json(['message' => 'Your not an admin', 'response' => '403']);
        }
        try{
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        }catch(\Exception $e){
            return response()->json(['message' => 'Bad credentials. Password len < 8 or username/email not unique or password is not confirmed', 'response' => '403']);
        }

        try{
        User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'passwordhash' => Hash::make($validated['password']),
            'state' => 'active',
            'visibilitypublic' => true,
            'isadmin' => false,
        ]);
        }catch(\Exception $e){
            return response()->json(['message' => 'Server problem', 'response' => '500']);
        }
        return response()->json(['message' => 'User created sucessfully', 'response' => '200']);

    }
}
