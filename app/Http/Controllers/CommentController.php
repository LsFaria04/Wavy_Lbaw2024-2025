<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Comment;
use App\Models\Media;


class CommentController extends Controller
{


    public function create(Request $request)
    {
        Comment::create([
            'userid' => $request->userid,
            'message' => $request->message,
            'postid' => $request->postid,
            'createddate' => $request->createddate,
        ]);
    }
    /**
     * Gets the comments created by user (By username)
     */
    function getUserCommentsByUsername(Request $request, $username){
        
        $user = User::where('username', $username)->firstOrFail();
        $comments = Comment::with('post', 'post.user','parentComment', 'parentComment.user', 'user')->where('userid', $user->userid)->orderBy('createddate', 'desc')->paginate(10);

        for($i = 0;$i < sizeof($comments); $i++){
            $comments[$i]->createddate = $comments[$i]->createddate->diffForHumans();
        }

        
        if($request->ajax()){
            return response()->json($comments);
        }

        return $comments;
    }

    public function show($id)
    {
        // Fetch the main comment and its related data
        $comment = Comment::with(['user', 'post', 'media', 'parentComment.user'])->findOrFail($id);
    
        // Fetch all sub-comments for the given comment ID
        $subComments = Comment::with(['user', 'media'])
            ->where('parentcommentid', $id)
            ->orderBy('createddate', 'asc')
            ->get();
    
        // Pass both the main comment and its sub-comments to the view
        return view('pages.comment', compact('comment', 'subComments'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:8192', 
        ]);

        // Check if the user is authorized to create a comment
        if ($request->user()->cannot('create', Comment::class)) {
            return redirect()->route('posts.show',$request->postid)->with('error', 'You cannot create a comment!');
        }

        $postId = $request->input('postid'); // Or $request->postid
        // Initialize image path variable
        $mediaPath = null;
    
        // Create the comment
        $comment = comment::create([
            'userid' => Auth::id(),
            'message' => $request->message,
            'postid' => $postId,
            'createddate' => now(),
        ]);

        $comment->save();

        if ($request->hasFile('media')) {
            // Log the array of uploaded files
            Log::info('Media files uploaded:', $request->file('media'));
        
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    // Log the individual file details
                    Log::info('File name:', ['name' => $file->getClientOriginalName()]);
                    Log::info('File size:', ['size' => $file->getSize()]);
                    Log::info('File mime type:', ['type' => $file->getMimeType()]);
        
                    // Process the file as usual
                    $mediaPath = $file->store('images', 'public');
        
                    Media::create([
                        'commentid' => $comment->commentid,
                        'userid' => NULL,
                        'path' => $mediaPath,
                    ]);
                } else {
                    Log::error('File is invalid:', ['name' => $file->getClientOriginalName()]);
                    return redirect()->route('posts.show', $request->postid)->with('error', 'Could not upload the file!');
                }
            }
        } else {
            Log::warning('No media files uploaded.');
        }
    
        return redirect()->route('posts.show',$request->postid)->with('success', 'Comment created successfully!');
    }

    public function storeSubcomment(Request $request)
    {
        // Validate input
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:8192', 
        ]);
    
        // Check if the user is authorized to create a comment
        if ($request->user()->cannot('create', Comment::class)) {
            return redirect()->route('comments.show',$request->commentid)->with('error', 'You cannot create a comment!');
        }
    
        // Initialize image path variable
        $mediaPath = null;
    
        // Create the comment
        $comment = comment::create([
            'userid' => Auth::id(),
            'message' => $request->message,
            'postid' => NULL,
            'parentcommentid' => $request->commentid,
            'createddate' => now(),
        ]);

        $comment->save();
        
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
                        'commentid' => $comment->commentid, // Associate media with this comment
                        'userid' => NULL, 
                        'postid' => NULL,
                        'path' => $mediaPath, // Store the image path
                    ]);
                }
                else{
                    return redirect()->route('comments.show',$request->commentid)->with('error', 'Could not upload the file!');
                }
            }
        }
    
        return redirect()->route('comments.show',$request->commentid)->with('success', 'Comment created successfully!');
    }


    public function update(Request $request, Comment $comment)
    {
        // Check if the authenticated user is the owner of the comment
        try { $this->authorize('edit', $comment); 
        }catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to update this comment.');
        }

        // Validate the input
        $request->validate([
            'message' => 'required|string|max:255',
            'media.*' => 'nullable|mimes:jpeg,png,jpg,gif,mp4,avi,mov,mp3,wav,ogg|max:10000', 
        ]);

        // Update the comment message
        $comment->update([
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

        // Get current media count attached to the comment
        $currentMediaCount = $comment->media()->count();

        // Check if there are already 4 or more files attached to the comment
        $mediaCount = $request->hasFile('media') ? count($request->file('media')) : 0;

        if ($currentMediaCount + $mediaCount > 4) {
            return redirect()->route('home', $comment->commentid)->with('error', 'You can only upload a maximum of 4 files.');
        }

        // Handle new file uploads
        if ($request->hasFile('media')) {
            Log::info("files have arrived");
            foreach ($request->file('media') as $file) {
                $mediaPath = $file->store('images', 'public');

                // Create new media record for the comment
                Media::create([
                    'commentid' => $comment->commentid,
                    'userid' => NULL, // Assuming the media belongs to the authenticated user
                    'postid' => NULL,
                    'path' => $mediaPath,
                ]);
            }
        }
        if ($comment->postid != NULL) return redirect()->route('posts.show', $comment->postid)->with('success', 'Comment updated successfully!');
        else return redirect()->route('comments.show', $comment->parentcommentid)->with('success', 'Comment updated successfully!');
    }

    /**
     * Deletes a comment.
     */
    public function destroy(Comment $comment) {
        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Comment not found.'], 404);
        }

        // Check if the authenticated user is the owner of the comment
        try { $this->authorize('delete', $comment); 
        }catch (AuthorizationException $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to delete this comment.']);
            }
            return redirect()->route('home')->with('error', 'You are not authorized to delete this comment.');
        }

        $mediaArray = Media::where('commentid', $comment->commentid)->get();
        foreach($mediaArray as $media){
            if (Storage::exists('public/'. $media->path)){
                Storage::delete('public/'. $media->path);
            }
        }

        $comment->media()->delete();
        
        // Delete the comment
        $comment->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Comment deleted successfully!']);
        }
        
        if ($comment->postid != NULL){
            return redirect()->route('posts.show',$comment->postid)->with('success', 'Comment deleted successfully!');
        }
        else return redirect()->route('comments.show',$comment->parentcommentid)->with('success', 'Comment deleted successfully!');
    }
}
