<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Stores a new media file (image, video, audio, etc.) associated with a post.
     */
    public function store(Request $request)
    {
        // Validate the input: Ensure the file is present and it's of an acceptable media type
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,avi,mp3,wav,ogg|max:20480', // Allow up to 20MB
            'postid' => 'required|exists:posts,postid', // Ensure the post exists
        ]);

        // Check if the post already has 4 media items
        $existingMediaCount = Media::where('postid', $request->postid)->count();
        if ($existingMediaCount >= 4) {
            return redirect()->route('home')->with('error', 'This post already has the maximum of 4 media items.');
        }

        // Store the uploaded file in the public storage folder
        $path = $request->file('file')->store('media', 'public');  // This saves the file to 'storage/app/public/media'

        // Now, let's create the Media record
        Media::create([
            'userid' => Auth::id(),
            'postid' => $request->postid,  // Associating media with a specific post
            'commentid' => null,  // Optional: If you want to add comment-based media later
            'path' => $path,  // Storing the path to the media file
        ]);

        // Return a response or redirect after storing the media
        return redirect()->route('home')->with('success', 'Media uploaded successfully!');
    }

    /**
     * Delete a media item.
     */
    public function destroy(Media $media)
    {
        // Ensure the user is authorized to delete the media
        if ($media->userid != Auth::id()) {
            return redirect()->route('home')->with('error', 'You are not authorized to delete this media.');
        }

        // Delete the actual media file from storage
        if (Storage::exists('public/'. $media->path)){
            Storage::delete('public/'. $media->path);
        }

        // Delete the record from the database
        $media->delete();

        return redirect()->route('home')->with('success', 'Media deleted successfully!');
    }
}
