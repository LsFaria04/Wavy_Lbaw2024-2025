<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Media;
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
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,avi,mp3,wav,ogg|max:2048', // Allow up to 20MB
            'postid' => 'required|exists:posts,postid', // Ensure the post exists
        ]);

        // Check if the post already has 4 media items
        $existingMediaCount = Media::where('postid', $request->postid)->count();
        if ($existingMediaCount >= 4) {
            return redirect()->route('home')->with('error', 'This post already has the maximum of 4 media items.');
        }

        // Store the uploaded file in the public storage folder
        $path = $request->file('file')->store('media', 'public');  // This saves the file to 'storage/app/public/media'

        Media::create([
            'userid' => Auth::id(),
            'postid' => $request->postid,  // Associating media with a specific post
            'commentid' => null,  // Optional: Add comment media later
            'path' => $path,  // Storing the path to the media file
        ]);

        return redirect()->route('home')->with('success', 'Media uploaded successfully!');
    }

}
