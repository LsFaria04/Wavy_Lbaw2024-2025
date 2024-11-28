<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{   
    //Perfoms the search according to the query inserted by the user
    public function search(Request $request) {
        $query = $request->input('q');
        $category = $request->input('category', 'posts');
        $posts = $users = $groups = collect();

        //No query so no data is provided
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

            //sanitizes the query to separate the words
            $sanitizedQuery = str_replace("'", "''", $query);

            //Performs the DB query according to the search category
            switch ($category) {
                case 'posts':
                    if(Auth::check()){
                        if(Auth::user()->isadmin){
                            $posts = Post::with('user','media')->whereRaw("to_tsvector('english', message) @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->orderBy('createddate', 'desc')
                            ->paginate(10);
                            for($i = 0;$i < sizeof($posts); $i++) {
                                $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
                            }
                            
                        }
                        else {
                            $posts = Post::with('user','media')->whereRaw("to_tsvector('english', message) @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->where('visibilitypublic', true)
                            ->orderBy('createddate', 'desc')
                            ->paginate(10);
                            for($i = 0;$i < sizeof($posts); $i++) {
                                $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
                            }
                        }
                    }

                    else {
                        $posts = Post::with('user','media')->whereRaw("to_tsvector('english', message) @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                        ->where('visibilitypublic', true)
                        ->orderBy('createddate', 'desc')
                        ->paginate(10);
                        for($i = 0;$i < sizeof($posts); $i++) {
                            $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
                        }
                    }
                    break;

                case 'users':

                    if(Auth::check()) {
                        $users = User::where(function($query) use ($sanitizedQuery) {
                            $query->whereRaw("to_tsvector('english', username) @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                                  ->orWhere('username', $sanitizedQuery);
                        })
                        ->where('state', '<>', 'deleted')
                        ->where('isadmin', false)
                        ->paginate(10);
                    }

                    else {
                        $users = User::where(function($query) use ($sanitizedQuery) {
                            $query->whereRaw("to_tsvector('english', username) @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                                  ->orWhere('username', $sanitizedQuery);
                        })
                        ->where('visibilitypublic', true)
                        ->where('state', '<>', 'deleted')
                        ->where('isadmin', false)
                        ->paginate(10);
                    }
                    break;

                case 'groups':
                    $groups = Group::whereRaw("to_tsvector('english', groupName || ' ' || description) @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                        ->paginate(10);
                    break;
                default:
                    $posts = Post::whereRaw("to_tsvector('english', message) @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
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
