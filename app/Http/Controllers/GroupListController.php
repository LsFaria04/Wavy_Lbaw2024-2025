<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupListController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $searchGroups = collect();
        $userGroups = collect();

        if(Auth::check()) {
            $category = $request->input('category', 'your-groups');
            $user = Auth::user(); 
            // If the user is logged in, fetch their groups for 'your-groups' category
            if ($category === 'your-groups') {
                $userGroups = $user->groups()->paginate(10);  // Paginate user's groups
            }

            // If no search query, return empty JSON
            if (empty($query)) {
                if ($request->ajax()) {
                    return response()->json([$userGroups]);
                }

                return view('pages.groupList', [
                    'category' => $category,
                    'userGroups' => $userGroups,
                    'searchGroups' => $searchGroups,
                ]);
            } else {
                $sanitizedQuery = str_replace("'", "''", $query); // Sanitize the query
                switch($category) {
                    case 'your-groups':
                        $userGroups = $user->groups()->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->paginate(10);
                        break;
                    case 'search-groups':
                        $searchGroups = Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                        ->paginate(10);
                        break;
                    default:
                        $userGroups = $user->groups()->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->paginate(10);
                        break;
                }
            }
            
        } else {
            $category = $request->input('category', 'search-groups');
            // If no search query, return empty JSON
            if (empty($query)) {
                if ($request->ajax()) {
                    return response()->json([]);
                }

                return view('pages.groupList', [
                    'category' => $category,
                    'userGroups' => $userGroups,
                    'searchGroups' => $searchGroups,
                ]);
            } else {
                $sanitizedQuery = str_replace("'", "''", $query); // Sanitize the query
                switch($category) {
                    case 'search-groups':
                        $searchGroups = Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                        ->paginate(10);
                        break;
                    default:
                        $searchGroups = Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->paginate(10);
                        break;
                }
            }
        }

        $message = null;

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([$userGroups, $searchGroups]);
        }

        // Return the view if not an AJAX request
        return view('pages.groupList', compact('message', 'userGroups', 'searchGroups', 'query', 'category'));
    }
}
