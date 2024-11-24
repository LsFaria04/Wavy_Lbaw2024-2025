<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Post;
use App\Models\Media;
use App\Models\User;

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
     * Gets the posts for the timeline
     */
    public function getPostsTimeline(Request $request){

        if (Auth::check()){
            $posts = Post::with('user','media')->orderBy('createddate', 'desc')->paginate(10);  
        }
        else {
            $posts = Post::with('user', 'media')->where('visibilitypublic', true)->orderBy('createddate', 'desc')->paginate(10);
        }

        for($i = 0;$i < sizeof($posts); $i++){
            $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
        }

        if($request->ajax()){
            return response()->json($posts);
        }

        // Return the view and pass the posts data
        return view('pages.home', compact('posts'));
        
    }

    /**
     * Gets the posts from a specific user 
     */
     public function getUserPosts(Request $request, $username){

        $user = User::where('username', $username)->firstOrFail();

        if (Auth::check()){
            $posts = Post::with('user','media')
            ->where('userid',$user->userid)
            ->orderBy('createddate', 'desc')->paginate(10);  
        }
        else {
            $posts = Post::with('user', 'media')
            ->where('visibilitypublic', true)
            ->where('userid',$user->userid)
            ->orderBy('createddate', 'desc')->paginate(10);
        }

        for($i = 0;$i < sizeof($posts); $i++){
            $posts[$i]->createddate = $posts[$i]->createddate->diffForHumans();
        }

        if($request->ajax()){
            return response()->json($posts);
        }

        return $posts;
     }
    
    /**
     * Stores a new post.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:10000', 
        ]);
    
        // Check if the user is authorized to create a post
        if ($request->user()->cannot('create', Post::class)) {
            return redirect()->route('home')->with('error', 'You cannot create a post!');
        }
    
        // Initialize image path variable
        $mediaPath = null;
    
        // Create the post
        $post = Post::create([
            'userid' => Auth::id(),
            'message' => $request->message,
            'visibilitypublic' => true, 
            'createddate' => now(),
            'groupid' => null, 
        ]);
        
        if ($request->hasFile('media')) {
            // Check if there are more than 4 files
            if (count($request->file('media')) > 4) {
                return redirect()->route('home')->with('error', 'You can only upload a maximum of 4 media files.');
            }
            
            // Store the images in the 'images' directory under 'public'
            foreach ($request->file('media') as $file) {
                if($file->isValid()){
                    $mediaPath = $file->store('images', 'public');

                    Media::create([
                        'postid' => $post->postid, // Associate media with this post
                        'userid' => NULL, 
                        'path' => $mediaPath, // Store the image path
                    ]);
                }
                else{
                    return redirect()->route('home')->with('error', 'Could not upload the file!');
                }
            }
        }
    
        return redirect()->route('home')->with('success', 'Post created successfully!');
    }
    

    // Update the post
    public function update(Request $request, Post $post)
    {
        // Check if the authenticated user is the owner of the post
        if ($post->userid != Auth::id() && !Auth::user()->isadmin) {
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
        if ($post->userid != Auth::id() && !Auth::user()->isadmin) {
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
