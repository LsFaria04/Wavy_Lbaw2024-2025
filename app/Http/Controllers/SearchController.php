<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;

class SearchController extends Controller
{
    public function search(Request $request) {
        $query = $request->input('q');
        $category = $request->input('category', 'posts');
        $posts = $users = $groups = collect();

        if (empty($query)) {
            return view('pages.search', [
                'message' => 'Please insert a search term.',
                'category' => $category,
                'posts' => $posts,
                'users' => $users,
                'groups' => $groups
            ]);
        }

        else {
            switch ($category) {
                case 'posts':
                    $posts = Post::where('message', 'LIKE', '%' . $query . '%')->get();
                    break;
                case 'users':
                    $users = User::where('username', 'LIKE', '%' . $query . '%')->get();
                    break;
                case 'groups':
                    $groups = Group::where('name', 'LIKE', '%' . $query . '%')->get();
                    break;
                default:
                    $posts = Post::where('message', 'LIKE', '%' . $query . '%')->get();
                    break;
            }
            $message = null;
        }
    
        return view('pages.search', compact('message', 'posts', 'users', 'groups', 'query', 'category'));
    }
}
