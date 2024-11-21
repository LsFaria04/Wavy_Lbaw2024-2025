<div class="post mb-4 p-4 bg-white rounded-md shadow">
    <div class="post-header mb-2 flex justify-between items-center">
        <div>
            <h3 class="font-bold">
                <a href="{{ route('profile', $post->user->username) }}" class="text-black hover:text-sky-900">
                    {{ $post->user->username }}
                </a>
            </h3>
            <span class="text-gray-500 text-sm">{{ $post->createddate->diffForHumans() }}</span>
        </div>
        @auth
            @if(auth()->id() === $post->userid) 
                <div class="flex items-center">
                    <form action="{{ route('posts.destroy', $post->postid) }}" method="POST" onsubmit="return confirmDelete()">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>
    <div class="post-body mb-2">
        <p>{{ $post->message }}</p>

        <!-- Loop through media files associated with the post -->
        <div class="post-media mt-4">
        @foreach ($post->media as $media)
            @php
                $filePath = asset('storage/' . $media->path);
                $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            @endphp

            @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                <img src="{{ $filePath }}" alt="Image" class="w-full h-auto rounded-md mb-2">
            @elseif (in_array($fileExtension, ['mp4', 'avi', 'mov']))
                <video controls class="w-full h-auto rounded-md mb-2">
                    <source src="{{ $filePath }}" type="video/{{ $fileExtension }}">
                    Your browser does not support the video tag.
                </video>
            @elseif (in_array($fileExtension, ['mp3', 'wav', 'ogg']))
                <audio controls class="w-full mb-2">
                    <source src="{{ $filePath }}" type="audio/{{ $fileExtension }}">
                    Your browser does not support the audio element.
                </audio>
            @else
                <p class="text-gray-500">Unsupported media type</p>
            @endif
        @endforeach
        </div>

        @auth
            @if(auth()->id() === $post->userid) 
                <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-3xl hover:bg-gray-600 mt-4" onclick="toggleEditPost({{ $post->postid }})">
                    Edit Post
                </button>
                <div id="edit-post-{{ $post->postid }}" class="edit-post-form hidden mt-4">
                    <form action="{{ route('posts.update', $post->postid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="3" class="mt-1 block w-full p-2 border rounded-md" placeholder="Edit your message">{{ $post->message }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Upload Image (optional)</label>
                            <input type="file" name="image" id="image" class="mt-1 block w-full p-2 border rounded-md">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Post</button>
                    </form>
                </div>
            @endif
        @endauth
    </div>
</div>
