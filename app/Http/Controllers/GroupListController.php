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
        $category = $request->input('category', Auth::check() ? 'your-groups' : 'search-groups');

        if(Auth::check()) {
            $user = Auth::user(); 
            if ($category === 'your-groups') {
                $userGroups = $user->groups()->paginate(10);  
            } elseif ($category === 'search-groups') {
                $searchGroups = Group::paginate(10);  
            }

            if (empty($query)) {
                if ($request->ajax()) {
                    return response()->json([$userGroups, $searchGroups]);
                }

                return view('pages.groupList', [
                    'category' => $category,
                    'userGroups' => $userGroups,
                    'searchGroups' => $searchGroups,
                ]);
            } else {
                $sanitizedQuery = str_replace("'", "''", $query); 
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
            if (empty($query)) {
                $searchGroups = Group::paginate(10);  
                if ($request->ajax()) {
                    return response()->json([$userGroups, $searchGroups]);
                }

                return view('pages.groupList', [
                    'category' => $category,
                    'userGroups' => $userGroups,
                    'searchGroups' => $searchGroups,
                ]);
            } else {
                $sanitizedQuery = str_replace("'", "''", $query); 
                if ($category === 'search-groups') {
                    $searchGroups = Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                        ->paginate(10);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([$userGroups, $searchGroups]);
        }

        return view('pages.groupList', compact('userGroups', 'searchGroups', 'query', 'category'));
    }
}
