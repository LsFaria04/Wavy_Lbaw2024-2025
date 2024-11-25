<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            if ($request->ajax()) {
                return response()->json([]);
            }

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
                    //check if user is authenticated to limit the posts that a non authenticated user can see
                    if(Auth::check() && Auth::user()->isadmin){
                        $posts = Post::with('user','media')->whereRaw("to_tsvector('english', message) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                            ->orderBy('createddate', 'desc')
                            ->paginate(10);
                            for($i = 0;$i < sizeof($posts); $i++){
                                $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
                            }
                    }
                    else{
                        $posts = Post::with('user','media')->whereRaw("to_tsvector('english', message) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                            ->where('visibilitypublic', true)
                            ->orderBy('createddate', 'desc')
                            ->paginate(10);
                            for($i = 0;$i < sizeof($posts); $i++){
                                $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
                            }
                    }
                    break;
                case 'users':
                    if(Auth::check()){
                    $users = User::whereRaw("to_tsvector('english', username) @@ to_tsquery('english', ?) or username = ?", [$queryWithPrefix, $queryWithPrefix])
                        ->paginate(10);
                    } else{
                    $users = User::whereRaw("to_tsvector('english', username) @@ to_tsquery('english', ?) or username = ?", [$queryWithPrefix, $queryWithPrefix])
                        ->where('visibilitypublic', true)
                        ->paginate(10);
                    }
                    break;
                case 'groups':
                    $groups = Group::whereRaw("to_tsvector('english', groupName || ' ' || description) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                        ->paginate(10);
                    break;
                default:
                    $posts = Post::whereRaw("to_tsvector('english', message) @@ to_tsquery('english', ?)", [$queryWithPrefix])
                        ->where('visibilitypublic', true)
                        ->paginate(10);
                        for($i = 0;$i < sizeof($posts); $i++){
                            $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
                        }
                    break;
            }
        }

        $message = null;

        if($request->ajax()){
            return response()->json([$posts, $users, $groups]);
        }

        return view('pages.search', compact('message', 'posts', 'users', 'groups', 'query', 'category'));
    }
}
