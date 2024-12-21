<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                    $topics = [];
                    if (isset($request->topics)) {
                        $topics = explode(',', $request->topics);
                        $topics = array_map('intval', $topics);
                    }
                    Log::info($request);
                
                    // Build the query for posts
                    $postsQuery = Post::with('user', 'media', 'topics', 'user.profilePicture')
                        ->withCount('likes')
                        ->withCount('comments')
                        ->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery]);
                
                    if (Auth::check()) {
                        if (!Auth::user()->isadmin) {
                            $postsQuery->where('visibilitypublic', true); // Normal user constraint
                        }
                
                        if (!empty($topics)) {
                            $postsQuery->whereHas('topics', function ($dbquery) use ($topics) {
                                $dbquery->whereIn('topic.topicid', $topics);
                            });
                        }
                    } else {
                        $postsQuery->where('visibilitypublic', true); // Constraint for unauthenticated users
                
                        if (!empty($topics)) {
                            $postsQuery->whereHas('topics', function ($dbquery) use ($topics) {
                                $dbquery->whereIn('topic.topicid', $topics);
                            });
                        }
                    }
                
                    $posts = $postsQuery->orderBy('createddate', 'desc')->paginate(10);
                
                    foreach ($posts as $post) {
                        if (Auth::check()) {
                            $post->liked = $post->likes()->where('userid', Auth::id())->exists();
                        } else {
                            $post->liked = false; // User is not authenticated
                        }
                        $post->createddate = $post->createddate->diffForHumans();
                    }
                    break;                

                case 'users':
                    $visibility = null;
                    if(isset($request->visibilityPublic)){
                        $visibility = $request->visibilityPublic;
                    }
                   
                    if(Auth::check()) {
                        if($visibility === null){
                            //no filter
                            
                            $users = User::with('profilePicture')->where(function($query) use ($sanitizedQuery) {
                                $query->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                                    ->orWhere('username', $sanitizedQuery);
                            })
                            ->where('state', '<>', 'deleted')
                            ->where('isadmin', false)
                            ->paginate(10);
                        }
                        else {
                            //with filter
                            $users = User::with('profilePicture')->where(function($query) use ($sanitizedQuery) {
                                $query->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                                    ->orWhere('username', $sanitizedQuery);
                            })
                            ->where('state', '<>', 'deleted')
                            ->where('visibilitypublic', $visibility)
                            ->where('isadmin', false)
                            ->paginate(10);
                        }
                    }

                    else {
                        if($visibility === null){
                            //no filter
                            $users = User::with('profilePicture')->where(function($query) use ($sanitizedQuery) {
                                $query->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                                    ->orWhere('username', $sanitizedQuery);
                            })
                            ->where('visibilitypublic', true)
                            ->where('state', '<>', 'deleted')
                            ->where('isadmin', false)
                            ->paginate(10);
                        }
                        else {
                            //with filter
                            $users = User::with('profilePicture')->where(function($query) use ($sanitizedQuery) {
                                $query->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                                    ->orWhere('username', $sanitizedQuery);
                            })
                            ->where('visibilitypublic', true)
                            ->where('state', '<>', 'deleted')
                            ->where('visibilitypublic', $visibility)
                            ->where('isadmin', false)
                            ->paginate(10);
                        }

                    }
                    break;

                case 'groups':
                    $visibility = null;
                    if(isset($request->visibilityPublic)){
                        $visibility = $request->visibilityPublic;
                    }
                    if($visibility === null){
                        $groups = Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->paginate(10);
                    }
                    else {
                        $groups = Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->where('visibilitypublic', $visibility)
                            ->paginate(10);
                    }
                    break;
                default:
                    $posts = Post::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
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
