<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;
use App\Models\Media;

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
        // Validate input
        $request->validate([
            'message' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);
    
        // Check if the user is authorized to create a post
        if ($request->user()->cannot('create', Post::class)) {
            return redirect()->route('home')->with('error', 'You cannot create a post!');
        }
    
        // Initialize image path variable
        $imagePath = null;
    
        // Check if the image is uploaded
        if ($request->hasFile('image')) {
            // Store the image in the 'images' directory under 'public'
            $imagePath = $request->file('image')->store('images', 'public');
        }
    
        // Create the post
        $post = Post::create([
            'userid' => Auth::id(),
            'message' => $request->message,
            'visibilitypublic' => true, // Set visibility to true (public)
            'createddate' => now(),
            'groupid' => null, 
        ]);
    
        // If an image was uploaded, create a media entry
        if ($imagePath) {
            Media::create([
                'postid' => $post->postid, // Associate media with this post
                'userid' => NULL, 
                'path' => $imagePath, // Store the image path
            ]);
        }
    
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
    
        // Update the post message
        $post->update([
            'message' => $request->message,
        ]);
    
        if ($request->hasFile('image')) {

            $post->media()->delete();
    
            $imagePath = $request->file('image')->store('images', 'public');
    
            Media::create([
                'postid' => $post->postid, 
                'userid' => NULL,
                'path' => $imagePath, 
            ]);
        }
    
        return redirect()->route('home')->with('success', 'Post updated successfully!');
    }
    

    // Delete the post
    public function destroy(Post $post)
    {
        // Check if the authenticated user is the owner of the post
        if ($post->userid != Auth::id()) {
            return redirect()->route('home')->with('error', 'You are not authorized to delete this post.');
        }

        // Delete the post
        $post->delete();

        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }
}
