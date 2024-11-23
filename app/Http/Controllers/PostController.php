<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    /**
     * Gets the posts for pagination (infinite scrolling) and return a json response
     */
    public function getPostPagination(Request $request){

        if (Auth::check()){
            $posts = Post::with('user','media')->orderBy('createddate', 'desc')->paginate(10);  
        }
        else {
            $posts = Post::with('user', 'media')->where('visibilitypublic', true)->orderBy('createddate', 'desc')->paginate(10);
        }

        for($i = 0;$i < sizeof($posts); $i++){
            $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
        }

        return response()->json($posts);
    }

    public function showFirstSet()
    {
        if (Auth::check()){
            $posts = Post::with('user')->orderBy('createddate', 'desc')->paginate(10);  
        }
        else {
            $posts = Post::with('user')->where('visibilitypublic', true)->orderBy('createddate', 'desc')->paginate(10);
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
            'media' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:10000', 
        ]);
    
        // Check if the user is authorized to create a post
        if ($request->user()->cannot('create', Post::class)) {
            return redirect()->route('home')->with('error', 'You cannot create a post!');
        }
    
        // Initialize image path variable
        $mediaPath = null;
    
        // Check if the image is uploaded
        if ($request->hasFile('media')) {
            // Store the image in the 'images' directory under 'public'
            if($request->file('media')->isValid()){
                $mediaPath = $request->file('media')->store('images', 'public');
            }
            else{
                return redirect()->route('home')->with('error', 'Could not upload the file!');
            }
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
        if ($mediaPath) {
            Media::create([
                'postid' => $post->postid, // Associate media with this post
                'userid' => NULL, 
                'path' => $mediaPath, // Store the image path
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

        if ($request->input('remove_media') == '1') {
            $mediaArray = Media::where('postid', $post->postid)->get();
            foreach ($mediaArray as $media) {
                if (Storage::exists('public/' . $media->path)) {
                    Storage::delete('public/' . $media->path);
                }
            }
            $post->media()->delete(); // Remove media record
        }

        if ($request->hasFile('media')) {         
            
            $mediaArray = Media::where('postid', $post->postid)->get();
            foreach($mediaArray as $media){
                if (Storage::exists('public/'. $media->path)){
                    Storage::delete('public/'. $media->path);
                }
            }
            Log::info("here");
            $post->media()->delete();
    
            $mediaPath = $request->file('media')->store('images', 'public');
    
            Media::create([
                'postid' => $post->postid, 
                'userid' => NULL,
                'path' => $mediaPath, 
            ]);
        }
        else{
            Log::info("no image");
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

        $mediaArray = Media::where('postid', $post->postid)->get();
        foreach($mediaArray as $media){
            if (Storage::exists('public/'. $media->path)){
                Storage::delete('public/'. $media->path);
            }
        }


        $post->media()->delete();
        
        // Delete the post
        $post->delete();

        

        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }
}
