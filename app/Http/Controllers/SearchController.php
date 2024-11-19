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
                'category' => $category,
                'posts' => $posts,
                'users' => $users,
                'groups' => $groups
            ]);
        }

        else {

            $queryWithPrefix = $query . ':*';

            switch ($category) {
                case 'posts':
                    $posts = Post::whereRaw("to_tsvector('english', message) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                        ->where('visibilitypublic', true)
                        ->get();
                    break;
                case 'users':
                    $users = User::whereRaw("to_tsvector('english', username) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                        ->where('visibilitypublic', true)
                        ->get();
                    break;
                case 'groups':
                    $groups = Group::whereRaw("to_tsvector('english', groupName || ' ' || description) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                        ->get();
                    break;
                default:
                    $posts = Post::whereRaw("to_tsvector('english', message) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                        ->where('visibilitypublic', true)
                        ->get();
                    break;
            }
        }

        $message = null;
    
        return view('pages.search', compact('message', 'posts', 'users', 'groups', 'query', 'category'));
    }
}
