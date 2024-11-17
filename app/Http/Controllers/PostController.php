<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;

class PostController extends Controller
{
    /**
     * Creates a new post.
     */
    public function create(Request $request)
    {
    
        Post::create([
            'userid' => $request->userid,
            'message' => $request->message,
            'visibilitypublic' => $request->visibilitypublic,
            'createddate' => $request->createddate,
            'groupid' => $request->groupid,
        ]);
    }

    public function showAll()
    {
        if (Auth::check()){
            // Retrieve all posts, optionally with related user info (e.g., user name)
            $posts = Post::with('user')->get();  // Eager load the related user
        }
        else {
            $posts = Post::with('user')->where('visibilitypublic',true)->get();
        }
        // Return the view and pass the posts data
        return view('pages.home', compact('posts'));
    }

    /**
     * Stores a new post.
     */
    public function store(Request $request)
    {
    
        $request->validate([
            'message' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // Create the post
        Post::create([
            'userid' => Auth::id(),
            'message' => $request->message,
            'visibilitypublic' => true, 
            'createddate' => now(),
            'groupid' => null, 
        ]);
        
        return redirect()->route('home')->with('success', 'Post created successfully!');

    }

    public function delete(Request $request, $id)
    {
        // Find the post by its ID.
        $post = Post::find($id);

        // Check if the post exists.
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Check if the current user is authorized to delete this post.
        $this->authorize('delete', $post);

        // Delete the post.
        $post->delete();

        // Return a response indicating the post was deleted.
        return response()->json(['message' => 'Post deleted successfully', 'deletedPost' => $post], 200);
    }
}
