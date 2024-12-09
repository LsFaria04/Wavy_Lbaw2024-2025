<div class="post mb-4 p-4 bg-white rounded-md shadow cursor-pointer" onclick="window.location.href='{{ route('posts.show', $post->postid) }}'">
    <div class="post-header mb-2 flex justify-between items-center">
        <div>
            <h3 class="font-bold">
                <a href="{{ $post->user->state === 'deleted' ? '#' : route('profile', $post->user->username) }}" class="text-black hover:text-sky-900">
                    {{ $post->user->state === 'deleted' ? 'Deleted User' : $post->user->username }}
                </a>
            </h3>
            <span class="text-gray-500 text-sm">{{ $post->createddate->diffForHumans() }}</span>
        </div>
        @auth
            @if(auth()->id() === $post->userid || Auth::user()->isadmin) 
                <div id= "postOptions" class="flex items-center gap-2">
                    <button type="button" onclick="toggleEditPost('{{ $post->postid }}'); event.stopPropagation();" class="text-gray-500 hover:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="black" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.6" d="M10.973 1.506a18.525 18.525 0 00-.497-.006A4.024 4.024 0 006.45 5.524c0 .43.095.865.199 1.205.054.18.116.356.192.527v.002a.75.75 0 01-.15.848l-4.937 4.911a.871.871 0 000 1.229.869.869 0 001.227 0L7.896 9.31a.75.75 0 01.847-.151c.17.079.35.139.529.193.34.103.774.198 1.204.198A4.024 4.024 0 0014.5 5.524c0-.177-.002-.338-.006-.483-.208.25-.438.517-.675.774-.32.345-.677.696-1.048.964-.354.257-.82.512-1.339.512-.396 0-.776-.156-1.059-.433L9.142 5.627a1.513 1.513 0 01-.432-1.06c0-.52.256-.985.514-1.34.27-.37.623-.727.97-1.046.258-.237.529-.466.78-.675zm-2.36 9.209l-4.57 4.59a2.37 2.37 0 01-3.35-3.348l.002-.001 4.591-4.568a6.887 6.887 0 01-.072-.223 5.77 5.77 0 01-.263-1.64A5.524 5.524 0 0110.476 0 12 12 0 0112 .076c.331.044.64.115.873.264a.92.92 0 01.374.45.843.843 0 01-.013.625.922.922 0 01-.241.332c-.26.257-.547.487-.829.72-.315.26-.647.535-.957.82a5.947 5.947 0 00-.771.824c-.197.27-.227.415-.227.457 0 .003 0 .006.003.008l1.211 1.211a.013.013 0 00.008.004c.043 0 .19-.032.46-.227.253-.183.532-.45.826-.767.284-.308.56-.638.82-.95.233-.28.463-.565.72-.823a.925.925 0 01.31-.235.841.841 0 01.628-.033.911.911 0 01.467.376c.15.233.22.543.262.87.047.356.075.847.075 1.522a5.524 5.524 0 01-5.524 5.525c-.631 0-1.221-.136-1.64-.263a6.969 6.969 0 01-.222-.071z"/>
                        </svg>
                    </button>

                    <form action="{{ route('posts.destroy', $post->postid) }}" method="POST" id="deleteForm-{{ $post->postid }}">
                        @csrf
                        <button type="button" onclick="openDeleteMenu('{{ $post->postid }}'); event.stopPropagation();" class="text-red-500 hover:text-red-700 ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    <!-- Delete Confirmation Menu -->
    <div id="deleteMenu" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-20">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
            <h2 class="text-xl font-semibold text-gray-900">Delete Post</h2>
            <p class="mt-4 text-sm text-gray-600">Are you sure you want to delete this post? This action cannot be undone.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button id="cancelButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
                    Cancel
                </button>
                <button id="confirmButton" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div class="post-body mb-2 cursor-pointer" id="post-content-{{ $post->postid }}" onclick="window.location.href='{{ route('posts.show', $post->postid) }}'">
        <p>{{ $post->message }}</p>

        <!-- Loop through media files associated with the post -->
        <div class="post-media mt-4 grid grid-cols-2 gap-4" onclick="event.stopPropagation();">
            @foreach ($post->media as $media)
                @php
                    $filePath = asset('storage/' . $media->path);
                    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                @endphp

                @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ $filePath }}" alt="Image" class="max-w-full max-h-96  object-cover rounded-md mb-2 mx-auto ">
                @elseif (in_array($fileExtension, ['mp4', 'avi', 'mov']))
                    <video controls class="max-w-full max-h-96 object-cover rounded-md mb-2 mx-auto">
                        <source src="{{ $filePath }}" type="video/{{ $fileExtension }}">
                        Your browser does not support the video tag.
                    </video>
                @elseif (in_array($fileExtension, ['mp3', 'wav', 'ogg']))
                    <audio controls class="max-w-full mb-2">
                        <source asrc="{{ $filePath }}" type="audio/{{ $fileExtension }}">
                        Your browser does not support the audio element.
                    </audio>
                @else
                    <p class="text-gray-500">Unsupported media type</p>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Like Button -->
    <div class="post-likes flex items-center gap-2 mt-4">
        <button 
            type="button" 
            class="flex items-center text-gray-500 hover:text-red-600" 
            onclick="likePost('{{ $post->postid }}'); event.stopPropagation();">
            
            <!-- No like -->
            <svg 
                id="heart-empty-{{ $post->postid }}" 
                viewBox="0 0 24 24" 
                aria-hidden="true" 
                class="h-5 w-5 fill-gray-500 hover:fill-red-600">
                <g>
                    <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                </g>
            </svg>
            
            <!-- Yes like xd -->
            <svg 
                id="heart-filled-{{ $post->postid }}" 
                viewBox="0 0 24 24" 
                aria-hidden="true" 
                class="h-5 w-5 fill-red-600 hidden">
                <g>
                    <path d="M20.884 13.19c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                </g>
            </svg>
            
            <span id="like-count-{{ $post->postid }}" class="ml-1">0</span>
        </button>
    </div>



    @auth
        @if(auth()->id() === $post->userid || Auth::user()->isadmin) 
            <!-- Edit Section in post.blade.php -->
            <div id="edit-post-{{ $post->postid }}" class="edit-post-form hidden mt-4 bg-white rounded-xl shadow-md p-4">
                <form action="{{ route('posts.update', $post->postid) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4" data-post-id="{{ $post->postid }}">
                    @csrf
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700">Edit Message</label>
                        <textarea name="message"
                            class="mt-1 block w-full p-4 border rounded-xl focus:ring-2 focus:ring-sky-700 shadow-sm outline-none" 
                            placeholder="Edit your message" 
                            style="resize: vertical; min-height: 200px;">{{ $post->message }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Edit Media</label>

                        <label for="image-{{ $post->postid }}" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black mt-2">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span>Attach new file</span>
                        </label>
                    
                        <div id="fileDisplay-{{ $post->postid }}" class="flex-col items-center gap-2 text-gray-500 hover:text-black mt-2 {{ $post->media->isEmpty() ? 'hidden' : '' }}">
                            @foreach ($post->media as $mediaItem)
                                <div class="flex items-center gap-2" id="file-{{ $mediaItem->mediaid }}">
                                    <span class="text-sm text-gray-500">{{ basename($mediaItem->path) }}</span>
                                    <button type="button" onclick="removeFileEdit('{{ $post->postid }}', '{{ $mediaItem->mediaid }}')" class="text-sm text-red-500 hover:text-red-700">Remove</button>
                                </div>
                            @endforeach
                            <div id="newFiles-{{ $post->postid }}" class="flex-col gap-2">
                                <!-- New files to add appended via JS -->
                            </div>
                        </div>
                        <input type="file" name="media[]" id="image-{{ $post->postid }}" class="hidden" onchange="updateFileNameEdit('{{ $post->postid }}')" multiple>
                        <input type="hidden" name="remove_media" id="removeMedia-{{ $post->postid }}" value="[]">
                    </div>

                    <button type="submit" class="p-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Update</button>
                </form>
            </div>
        @endif
    @endauth
</div>
