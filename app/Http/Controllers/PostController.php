<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Post;
use App\Models\Media;
use App\Models\User;
use App\Models\Group;

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
            //$friendsId = Follow::where('follower',Auth::id())->pluck('followee')->toArray();
            //whereIn(userid, $friendsId) --> Posts of friends, do the same to groups and topics when implemented.
            $posts = Post::with('user','media', 'topics')->whereNull('groupid')->orderBy('createddate', 'desc')->paginate(10);  
        }
        else {
            $posts = Post::with('user', 'media', 'topics')->whereNull('groupid')->where('visibilitypublic', true)->orderBy('createddate', 'desc')->paginate(10);
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
            $posts = Post::with('user','media','topics')
            ->whereNull('groupid')
            ->where('userid',$user->userid)
            ->orderBy('createddate', 'desc')->paginate(10);  
        }
        else {
            $posts = Post::with('user', 'media','topics')
            ->whereNull('groupid')
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

        $request->topics = explode(',', $request->topics[0]);

        // Validate input
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:8192',
            'topics.*' => 'nullable|string'
        ]);

        //the general topic is the default
        if($request->topics[0] == ""){
            $request->topics[0] = "1";
        }

        
    
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
            'groupid' => $request->groupid ?? null, // Use groupid if available, otherwise null
        ]);

        //insert the topics to the post
        foreach($request->topics as $topic){
            $post->topics()->attach($topic);
        }
        
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
                        'userid' => null, 
                        'commentid' => null,
                        'path' => $mediaPath, // Store the image path
                    ]);
                }
                else{
                    return redirect()->route('home')->with('error', 'Could not upload the file!');
                }
            }
        }

        

        // Redirect back to the group page or home page
        if ($request->groupid) {
            $group = Group::where('groupid', $request->groupid)->firstOrFail();
            return redirect()->route('group', ['groupname' => $group->groupname])->with('success', 'Post created successfully!');
        }
    
        return redirect()->route('home')->with('success', 'Post created successfully!');
    }

    public function show($id)
    {
        $post = Post::with(['user', 'media', 'comments.user', 'comments.media'])->findOrFail($id);
        return view('pages.post', compact('post'));
    }
    
    /**
     * Updates the content of a post.
     */
    public function update(Request $request, Post $post)
    {
        // Check if the authenticated user is the owner of the post
        try { $this->authorize('edit', $post); 
        }catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to update this post.');
        }

        // Validate the input
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:10000', 
        ]);

        // Update the post message
        $post->update([
            'message' => $request->message,
        ]);


        // Handle file removals
        if ($request->input('remove_media')) {
            $removeMediaIds = json_decode($request->input('remove_media'), true);

            foreach ($removeMediaIds as $mediaId) {
                $media = Media::find($mediaId);
                if ($media && Storage::exists('public/' . $media->path)) {
                    Storage::delete('public/' . $media->path);
                    $media->delete(); // Delete the media record
                }
            }
        }

        // Get current media count attached to the post
        $currentMediaCount = $post->media()->count();

        // Check if there are already 4 or more files attached to the post
        $mediaCount = $request->hasFile('media') ? count($request->file('media')) : 0;

        if ($currentMediaCount + $mediaCount > 4) {
            return redirect()->route('home', $post->postid)->with('error', 'You can only upload a maximum of 4 files.');
        }

        // Handle new file uploads
        if ($request->hasFile('media')) {
            Log::info("files have arrived");
            foreach ($request->file('media') as $file) {
                $mediaPath = $file->store('images', 'public');

                // Create new media record for the post
                Media::create([
                    'postid' => $post->postid,
                    'userid' => NULL, // Assuming the media belongs to the authenticated user
                    'path' => $mediaPath,
                ]);
            }
        }
        return redirect()->route('home')->with('success', 'Post updated successfully!');
    }

    /**
     * Deletes a post.
     */
    public function destroy(Post $post) {
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found.'], 404);
        }

        // Check if the authenticated user is the owner of the post
        try { $this->authorize('delete', $post); 
        }catch (AuthorizationException $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to delete this post.']);
            }
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

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Post deleted successfully!']);
        }

        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }
}
