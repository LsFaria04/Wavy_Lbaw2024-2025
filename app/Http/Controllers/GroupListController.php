<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupListController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $category = $request->input('category', 'your-groups');
        $user = auth()->user(); // Get the logged-in user

        // Initialize variables for groups
        $userGroups = collect();
        $searchGroups = collect();

        // If the user is logged in, fetch their groups for 'your-groups' category
        if ($user && $category === 'your-groups') {
            $userGroups = $user->groups()->paginate(10);  // Paginate user's groups
        }

        // If no search query, return empty JSON
        if (empty($query)) {
            if ($request->ajax()) {
                return response()->json([
                    'userGroups' => $userGroups,
                    'searchGroups' => $searchGroups,
                    'lastPage' => $userGroups->lastPage() ?? 1,
                ]);
            }

            return view('pages.groupList', [
                'category' => $category,
                'userGroups' => $userGroups,
                'searchGroups' => $searchGroups,
            ]);
        }

        // If a search query exists and the category is 'search-groups', search across all groups
        if (!empty($query)) {
            $sanitizedQuery = str_replace("'", "''", $query); // Sanitize the query

            if ($category === 'search-groups') {
                $searchGroups = Group::whereRaw(
                    "to_tsvector('english', groupName || ' ' || description) @@ plainto_tsquery('english', ?)",
                    [$sanitizedQuery]
                )
                ->paginate(10); // Paginate search results
            } elseif ($category === 'your-groups' && $user) {
                // If the category is 'your-groups' and the user is logged in, filter user's groups based on the query
                $userGroups = $user->groups()->whereRaw(
                    "to_tsvector('english', groupName || ' ' || description) @@ plainto_tsquery('english', ?)",
                    [$sanitizedQuery]
                )->paginate(10);
            }
        }

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'userGroups' => $userGroups,
                'searchGroups' => $searchGroups,
                'lastPage' => $searchGroups->lastPage() ?? 1,
            ]);
        }

        // Return the view if not an AJAX request
        return view('pages.groupList', [
            'userGroups' => $userGroups,
            'searchGroups' => $searchGroups,
            'query' => $query,
            'category' => $category,
        ]);
    }
}
