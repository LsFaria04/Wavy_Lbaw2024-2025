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

    public function createUser() {
        return view('partials.admin.create-user');
    }

    public function storeUser(Request $request)
    {   
        if(Auth::user()->isadmin){
            try{
            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);
            }catch(\Exception $e){
                return redirect()->route('admin.index')->with('error', "Please verify the password length, username and email");
            }


            User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'passwordhash' => Hash::make($validated['password']),
                'state' => 'active',
                'visibilitypublic' => true,
                'isadmin' => false,
            ]);

            return redirect()->route('admin.index')->with('success', "Created a user successfully");
        }
        else{
            return redirect()->route('admin.index')->with('error', "You are not an admin");
        }

    }
    
    public function edit($id) {
        if(Auth::user()->isadmin){
            Log::info("is an admin");
        $user = User::find($id);
        if ($user) {
            return response()->json(['success' => true, 'user' => $user]);
        }
            return response()->json(['success' => false]);
        }
        else{
            return response()->json(['success' => false]);
        }
    }

    public function update(Request $request, $id) {
        if(Auth::user()->isadmin){
            $user = User::find($id);
            if ($user) {
                try{
                    if($request['visibilitypublic'] === 'true'){
                        Log::info("data is being sent wrongly");
                    }
                    if($request['visibilitypublic'] === 'false'){
                        Log::info("data is being sent correctly");
                    }
                $user->update($request->only('username', 'email', 'state', 'visibilitypublic', 'isadmin'));
                
                } catch (\Exception $e){
                    Log::info("error on update");
                }
                return response()->json(['success' => true, 'user' => $user]);
            }
            return response()->json(['success' => false]);
        }
        else{
            return response()->json(['success' => false]);
        }
    }


    public function destroyUser($id) {
        if(Auth::user()->isadmin){
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }
        else{
            return response()->json(['success' => false]);
        }
    }
}
