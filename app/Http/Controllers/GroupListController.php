<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\GroupInvitation;
use Illuminate\Support\Facades\Auth;

class GroupListController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $searchGroups = collect();
        $userGroups = collect();
        $invitations = collect();
        $category = $request->input('category', Auth::check() ? 'your-groups' : 'search-groups');

        if(Auth::check()) {
            $user = Auth::user(); 
            $sanitizedQuery = str_replace("'", "''", $query); 

            switch($category) {
                case 'your-groups':
                    $userGroups = empty($query) 
                        ? $user->groups()->paginate(10)
                        : $user->groups()->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->paginate(10);
                    break;
                case 'manage-invitations':
                    try {
                        $invitations = GroupInvitation::with('user')
                            ->with('group')
                            ->where('userid', $user->userid)
                            ->orderBy('createddate', 'desc')
                            ->paginate(10);
                    
                        $invitations->getCollection()->transform(function ($invitation) {
                            $invitation->createddate = $invitation->createddate->diffForHumans();
                            return $invitation;
                        });
                    } catch (\Exception $e) {
                        \Log::error('Error in manage-invitations: ' . $e->getMessage());
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                    break;
                                      
                case 'search-groups':
                    $searchGroups = empty($query)
                        ? Group::paginate(10)
                        : Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                            ->paginate(10);
                    break;
                default:
                    $userGroups = $user->groups()->whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                        ->paginate(10);
                    break;
            }
        } else {
            if ($category === 'search-groups') {
                $sanitizedQuery = str_replace("'", "''", $query); 
                $searchGroups = empty($query)
                    ? Group::paginate(10)
                    : Group::whereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                        ->paginate(10);
            }
        }

        if ($request->ajax()) {
            try {
                return response()->json([$userGroups, $searchGroups, $invitations]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }        

        return view('pages.groupList', compact('userGroups', 'searchGroups', 'invitations', 'query', 'category'));
    }
}
