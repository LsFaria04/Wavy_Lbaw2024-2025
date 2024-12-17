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
        $usersQuery = User::query();
        $users = $usersQuery->Where('isadmin', false)->paginate(10);

        return view('pages.admin', compact('users'));
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

    public function getUsersForAdmin(Request $request){
        
        try{
            $this->authorize('getForAdmin', User::class);
        }catch(\Exception $e){
            return response()->json(['message' => 'Your not an admin', 'response' => '403']);
        }

        try{
           $users = User::where('isadmin', false)->paginate(10);
        } catch(\Exception $e){
            return response()->json(["message" => 'Server problem', 'response' => '500']);
        }

        return response()->json($users);
    }

    public function searchUsersForAdmin(Request $request){
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);

        try{
            $this->authorize('getForAdmin', User::class);
        }catch(\Exception $e){
            return response()->json(['message' => 'Your not an admin', 'response' => '403']);
        }

        try{
           $users = User::Where('username',$sanitizedQuery )->paginate(10);
        } catch(\Exception $e){
            return response()->json(["message" => 'Server problem', 'response' => '500']);
        }

        return response()->json($users);
    }
}
