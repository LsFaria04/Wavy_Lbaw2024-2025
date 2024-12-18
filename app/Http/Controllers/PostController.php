<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Events\PostLike;

use App\Models\Post;
use App\Models\Media;
use App\Models\User;
use App\Models\Group;
use App\Models\Like;

class PostController extends Controller {
    /**
     * Gets the posts for the timeline
     */
    public function getPostsTimeline(Request $request)
    {
        if (Auth::check()) {
            // Include the comment count
            $posts = Post::with('user', 'media','topics')
                        ->withCount('comments')  // This will add comments_count to the Post model~
                        ->withCount('likes')
                        ->whereNull('groupid')
                        ->orderBy('createddate', 'desc')
                        ->paginate(10);

            foreach ($posts as $post) {
                $post->liked = $post->likes()->where('userid', Auth::user()->userid)->exists();
                $post->createddate = $post->createddate->diffForHumans();  // Format the created date
            }
        } else {
            $posts = Post::with('user', 'media','topics')
                        ->withCount('comments')  // Add the comment count
                        ->withCount('likes')
                        ->whereNull('groupid')
                        ->where('visibilitypublic', true)
                        ->orderBy('createddate', 'desc')
                        ->paginate(10);

            foreach ($posts as $post) {
                $post->liked = false;  // If the user is not authenticated, they cannot like a post
                $post->createddate = $post->createddate->diffForHumans();  // Format the created date
            }
        }
    
        // Format the created date to human-readable format
        foreach ($posts as $post) {
            $post->createddate = $post->createddate->diffForHumans();
        }
    
        if ($request->ajax()) {
            return response()->json($posts);
        }
    
        // Return the view and pass the posts data
        return view('pages.home', compact('posts'));

    }
    

    /**
     * Gets the posts from a specific user 
     */
    public function getUserPosts(Request $request, $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        if (Auth::check()) {
            $posts = Post::with('user', 'media', 'topics')
                        ->withCount('comments')  // Add the comment count
                        ->withCount('likes')
                        ->whereNull('groupid')
                        ->where('userid', $user->userid)
                        ->orderBy('createddate', 'desc')
                        ->paginate(10);
            foreach ($posts as $post) {
                $post->liked = $post->likes()->where('userid', Auth::user()->userid)->exists();
                $post->createddate = $post->createddate->diffForHumans();  // Format the created date
            }
        } else {
            $posts = Post::with('user', 'media', 'topics')
                        ->withCount('comments')  // Add the comment count
                        ->whereNull('groupid')
                        ->where('visibilitypublic', true)
                        ->where('userid', $user->userid)
                        ->orderBy('createddate', 'desc')
                        ->paginate(10);
            foreach ($posts as $post) {
                $post->liked = false;  // If the user is not authenticated, they cannot like a post
                $post->createddate = $post->createddate->diffForHumans();  // Format the created date
            }
        }

        // Format the created date to human-readable format
        foreach ($posts as $post) {
            $post->createddate = $post->createddate->diffForHumans();
        }

        if ($request->ajax()) {
            return response()->json($posts);
        }

        return $posts;
    }


    public function likePost(Request $request, $postId) {

        $user = Auth::user(); // Get the authenticated user
        $post = Post::findOrFail($postId);
        
        // Check if the user has already liked this post
        $existingLike = Like::where('postid', $postId)
                            ->where('userid', $user->userid)
                            ->first();
    

        Log::info($existingLike);
        if ($existingLike) {
            // If the user has already liked the post, remove the like
            Log::info("Olá");
            $existingLike->delete();
            $liked = false;
        } else {
            // If the user has not liked the post, add the like
            Like::create([
                'userid' => $user->userid,
                'postid' => $postId,
                'createddate' => now(),
            ]);
            $liked = true;
        }
    
        // Get the updated like count
        $likeCount = $post->likes()->count();

        event(new PostLike($postId, $user, $post->user->userid));
    
        // Return the updated like status and like count
        return response()->json([
            'liked' => $liked,
            'likeCount' => $likeCount
        ]);
    }
    
    /**
     * Stores a new post.
     */
    public function store(Request $request)
    {   

        if($request->topics !== null){
            $request->topics = explode(',', $request->topics[0]);
        }

        // Validate input
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:8192',
            'topics.*' => 'nullable|string'
        ]);

        //the general topic is the default
        if($request->topics !== null){
            if($request->topics[0] == ""){
                $request->topics[0] = "1";
            }
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
        if($request->topics !== null){
            foreach($request->topics as $topic){
                $post->topics()->attach($topic);
            }
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
        // Eager load user, media, and comments (with their users, media, likes, and subcomments recursively)
        $post = Post::with([
                'user', 
                'media', 
                'comments.user', 
                'comments.media',
                'comments.commentLikes', // Eager load commentLikes for parent comments
                'comments.subcomments' => function ($query) {
                    $query->with(['user', 'commentLikes', 'subcomments']); // Load subcomments recursively
                }
            ])
            ->withCount('comments')  // Add comments_count to the Post model
            ->withCount('likes')     // Add likes_count to the Post model
            ->findOrFail($id);
    
        // Process likes and recursively handle subcomments
        if (Auth::check()) {
            $post->liked = $post->likes()->where('userid', Auth::user()->userid)->exists();
            $post->createddate = $post->createddate->diffForHumans(); // Format the created date    
    
            // Recursive function to process comments and their subcomments
            $this->processComments($post->comments, Auth::user()->userid);
        } else {
            $post->liked = false;
            $post->createddate = $post->createddate->diffForHumans(); // Format the created date
    
            // Set liked to false for all comments and subcomments
            $this->processComments($post->comments, null);
        }
    
        // Pass the post data to the view
        return view('pages.post', compact('post'));
    }

    private function processComments($comments, $userId)
    {
        foreach ($comments as $comment) {
            // Process likes for the current comment
            $comment->liked = $userId ? $comment->commentLikes()->where('userid', $userId)->exists() : false;
            $comment->comment_likes_count = $comment->commentLikes()->count();

            // Recursively process subcomments
            if ($comment->subcomments->isNotEmpty()) {
                $this->processComments($comment->subcomments, $userId);
            }
        }
    }
    
    
    /**
     * Updates the content of a post.
     */
    public function update(Request $request, Post $post) {
        // Check if the authenticated user is the owner of the post
        try { $this->authorize('edit', $post); 
        }catch (AuthorizationException $e) {
            if($request->groupname !== null){
                return redirect()->route('group', $request->groupname)
                ->with('error', 'You are not authorized to update this post.');
            }
            return redirect()->route('home')->with('error', 'You are not authorized to update this post.');
        }

        
        if($request->topics !== null){
            $request->topics = explode(',', $request->topics[0]);
            //detach the general topic  because we are inserting specific topics
            $post->topics()->detach(1);
        }


        if(count($request->remove_topics) !== null){
            $request->remove_topics = explode(',', $request->remove_topics[0]);
        }

        // Validate the input
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:10000', 
            'topics.*' => 'nullable|string',
            'remove_topics.*' => 'nullable|string'
        ]);

        // Update the post message
        $post->update([
            'message' => $request->message,
        ]);

        
        //removes the topics from the post
        if($request->topics !== null){
            foreach($request->remove_topics as $topic){
                if($topic == ""){
                    continue;
                }
                $post->topics()->detach($topic);
            }
        }

        //insert the topics to the post
        foreach($request->topics as $topic){
            if($topic == ""){
                continue;
            }
            $post->topics()->attach($topic);
        }

        //attach the general topic if there are no topics in the post after the update
        if($post->topics()->count() == 0){
            $post->topics()->attach(1);
        }


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
            if($request->groupname !== null){
                return redirect()->route('group', $request->groupname)
                ->with('error', 'You can only upload a maximum of 4 files.');
            }
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
        if($request->groupname !== null){
            return redirect()->route('group', $request->groupname)
            ->with('success', 'Group post updated successfully!');
        }
        return redirect()->route('home')->with('success', 'Post updated successfully!');
    }

    /**
     * Deletes a post.
     */
    /**
     * Deletes a post.
     */
    public function destroy(Post $post) {
        if (!$post) {
            return redirect()->route('home')->with('success', 'Post already deleted!');
        }

        // Check if the authenticated user is authorized to delete the post
        try { 
            $this->authorize('delete', $post); 
        } catch (AuthorizationException $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to delete this post.']);
            }
            return redirect()->route('home')->with('error', 'You are not authorized to delete this post.');
        }

        // Delete associated media files from storage
        $mediaArray = Media::where('postid', $post->postid)->get();
        foreach ($mediaArray as $media) {
            if (Storage::exists('public/' . $media->path)) {
                Storage::delete('public/' . $media->path);
            }
        }

        // Delete media entries from the database
        $post->media()->delete();
        
        // Delete the post itself
        $post->delete();

        // Handle AJAX response
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Post deleted successfully!']);
        }

        // Redirect to the group page if the post has a groupid
        if ($post->groupid) {
            $group = Group::where('groupid', $post->groupid)->first();
            if ($group) {
                return redirect()->route('group', ['groupname' => $group->groupname])->with('success', 'Post deleted successfully!');
            }
        }

        // Redirect to home page if no group is associated
        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }

}
