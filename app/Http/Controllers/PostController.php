<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            'visibilitypublic' => 'true',
            'createddate' => $request->createddate,
            'groupid' => $request->groupid,
        ]);
    }

    public function showAll()
    {
        // Retrieve all posts, optionally with related user info (e.g., user name)
        $posts = Post::with('user')->get();  // Eager load the related user
    
        // Return the view and pass the posts data
        return view('pages.home', compact('posts'));
    }

    /**
     * Deletes a specific post.
     */

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
