<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function search(Request $request) {
        $validator = Validator::make($request->all(), [
            'q' => 'nullable|string|max:255', 
            'category' => 'nullable|in:posts,users,groups', 
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Entrada inválida.'], 400);
        }

        $query = $request->input('q');
        $category = $request->input('category', 'posts');

        if (empty($query)) {

            if ($request->ajax()) {
                return response()->json(['results' => []]);
            }

            return view('pages.search', [
                'category' => $category,
                'posts' => $posts,
                'users' => $users,
                'groups' => $groups,
                'query' => $query
            ]);
        }

        $queryWithPrefix = $query . ':*';

        $validCategories = ['posts', 'users', 'groups'];

        if (!in_array($category, $validCategories)) {
            return response()->json(['error' => 'Categoria inválida.'], 400);
        }

        try {
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao realizar a busca.'], 500);
        }

        $message = null;

        return view('pages.search', compact('message', 'posts', 'users', 'groups', 'query', 'category'));
    }
}
