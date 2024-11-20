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
            $posts = Post::with('user')->orderBy('createddate', 'desc')->get();  
        }
        else {
            $posts = Post::with('user')->where('visibilitypublic', true)->orderBy('createddate', 'desc')->get();
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


    // Update the post
    public function update(Request $request, Post $post)
    {
        // Check if the authenticated user is the owner of the post
        if ($post->userid != Auth::id()) {
            return redirect()->route('home')->with('error', 'You are not authorized to update this post.');
        }

        // Validate the input
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Update the post
        $post->update([
            'message' => $request->message,
        ]);

        return redirect()->route('home')->with('success', 'Post updated successfully!');
    }

    // Delete the post
    public function destroy(Post $post)
    {
        // Check if the authenticated user is the owner of the post
        if ($post->user_id != Auth::id()) {
            return redirect()->route('home')->with('error', 'You are not authorized to delete this post.');
        }

        // Delete the post
        $post->delete();

        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }
}
