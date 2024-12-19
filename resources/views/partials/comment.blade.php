<div class="comment p-4 bg-white cursor-pointer border-b border-gray-300 w-full max-w-full">
    <div class="comment-header mb-2 flex justify-between items-center">
        <div>
            <h3 class="font-bold">
                <a href="{{ $comment->user->state === 'deleted' ? '#' : route('profile', $comment->user->username) }}" class="text-black hover:text-sky-900">
                    {{ $comment->user->state === 'deleted' ? 'Deleted User' : $comment->user->username }}
                </a>
            </h3>
            <span class="text-gray-500 text-sm">{{ $comment->createddate->diffForHumans() }}</span>
        </div>
        @auth
            @if(auth()->id() === $comment->userid || Auth::user()->isadmin) 
                <div id= "commentOptions" class="flex items-center gap-2">
                    <button type="button" onclick="toggleEditComment('{{ $comment->commentid }}'); event.stopPropagation();" class="text-gray-500 hover:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="black" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.6" d="M10.973 1.506a18.525 18.525 0 00-.497-.006A4.024 4.024 0 006.45 5.524c0 .43.095.865.199 1.205.054.18.116.356.192.527v.002a.75.75 0 01-.15.848l-4.937 4.911a.871.871 0 000 1.229.869.869 0 001.227 0L7.896 9.31a.75.75 0 01.847-.151c.17.079.35.139.529.193.34.103.774.198 1.204.198A4.024 4.024 0 0014.5 5.524c0-.177-.002-.338-.006-.483-.208.25-.438.517-.675.774-.32.345-.677.696-1.048.964-.354.257-.82.512-1.339.512-.396 0-.776-.156-1.059-.433L9.142 5.627a1.513 1.513 0 01-.432-1.06c0-.52.256-.985.514-1.34.27-.37.623-.727.97-1.046.258-.237.529-.466.78-.675zm-2.36 9.209l-4.57 4.59a2.37 2.37 0 01-3.35-3.348l.002-.001 4.591-4.568a6.887 6.887 0 01-.072-.223 5.77 5.77 0 01-.263-1.64A5.524 5.524 0 0110.476 0 12 12 0 0112 .076c.331.044.64.115.873.264a.92.92 0 01.374.45.843.843 0 01-.013.625.922.922 0 01-.241.332c-.26.257-.547.487-.829.72-.315.26-.647.535-.957.82a5.947 5.947 0 00-.771.824c-.197.27-.227.415-.227.457 0 .003 0 .006.003.008l1.211 1.211a.013.013 0 00.008.004c.043 0 .19-.032.46-.227.253-.183.532-.45.826-.767.284-.308.56-.638.82-.95.233-.28.463-.565.72-.823a.925.925 0 01.31-.235.841.841 0 01.628-.033.911.911 0 01.467.376c.15.233.22.543.262.87.047.356.075.847.075 1.522a5.524 5.524 0 01-5.524 5.525c-.631 0-1.221-.136-1.64-.263a6.969 6.969 0 01-.222-.071z"/>
                        </svg>
                    </button>

                    <form action="{{ route('comments.destroy', $comment->commentid) }}" method="POST" id="deleteCommentForm-{{ $comment->commentid }}">
                        @csrf
                        <button type="button" onclick="openDeleteCommentMenu('{{ $comment->commentid }}'); event.stopPropagation();" class="text-red-500 hover:text-red-700 ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
            @else
            <div id= "commentOptions" class="flex items-center gap-2">
                <button type="button" onclick="event.stopPropagation(); toggleReportForm('{{ $post->postid }}', 'post');" class="text-gray-500 hover:text-black">
                    Report
                </button>
            </div>
            @endif
        @endauth
    </div>

    <!-- Delete Confirmation Menu -->
    <div id="deleteCommentMenu" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-20">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
            <h2 class="text-xl font-semibold text-gray-900">Delete Comment</h2>
            <p class="mt-4 text-sm text-gray-600">Are you sure you want to delete this comment? This action cannot be undone.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button id="cancelCommentButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
                    Cancel
                </button>
                <button id="confirmCommentButton" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div class="comment-body mb-2 cursor-pointer max-w-screen-lg" id="comment-content-{{ $comment->commentid }}">
        <p>{{ $comment->message }}</p>

        <!-- Loop through media files associated with the comment -->
        @php
            Log::info($comment->media);
        @endphp
        <div class="comment-media mt-4 grid grid-cols-2 gap-4">
            @foreach ($comment->media as $media)
                @php
                    $filePath = asset('storage/' . $media->path);
                    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                @endphp

                @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                    <button onclick = "toggleImageDetails('{{$filePath}}')"><img src="{{ $filePath }}" alt="Image" class="max-w-full max-h-96  object-cover rounded-md mb-2 mx-auto "></button>
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
    
    <div class="comment-interactions flex items-center gap-4 mt-4">
        <div class="comment-likes flex items-center gap-2">
            <button type="button" class="flex items-center text-gray-500 hover:text-red-600 group" 
                    onclick="likeComment('{{ $comment->commentid }}', event)">
                <!-- No like -->
                <svg id="heart-empty-{{ $comment->commentid }}" viewBox="0 0 24 24" aria-hidden="true" 
                    class="h-5 w-5 fill-gray-500 hover:fill-red-600 group-hover:fill-red-600 {{ $comment->liked ? 'hidden' : '' }}">
                    <g>
                        <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                    </g>
                </svg>
                
                <!-- Yes like -->
                <svg id="heart-filled-{{ $comment->commentid }}" viewBox="0 0 24 24" aria-hidden="true" 
                    class="h-5 w-5 fill-red-600 group-hover:fill-red-600 {{ $comment->liked ? '' : 'hidden' }}">
                    <g>
                        <path d="M20.884 13.19c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                    </g>
                </svg>
                
                <span id="like-count-{{ $comment->commentid }}" 
                    class="ml-1 group-hover:text-red-600">
                    {{ $comment->comment_likes_count ?? 0 }}
                </span>            
            </button>
        </div>

        <!-- Comment Button -->
        <div class="comment-comments flex items-center gap-2">
            <button 
                type="button" 
                class="flex items-center text-gray-500 hover:text-sky-600 group" 
                onclick="toggleSubcommentForm('{{ $comment->commentid }}')">
                
                <!-- Comment Icon -->
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    id="comment-icon-{{ $comment->commentid }}" 
                    class="h-5 w-5 fill-gray-500 group-hover:fill-sky-600 transition duration-200 ease-in-out" 
                    viewBox="0 0 24 24" 
                    fill="currentColor">
                    <path d="M12 2C6.477 2 2 6.067 2 10.5c0 1.875.656 3.625 1.844 5.094l-1.308 3.922c-.19.57.474 1.065.997.736l3.875-2.325A9.435 9.435 0 0012 19c5.523 0 10-4.067 10-8.5S17.523 2 12 2zm0 2c4.418 0 8 3.067 8 6.5S16.418 17 12 17c-1.173 0-2.292-.232-3.318-.656a1 1 0 00-.97.035l-2.898 1.739.835-2.501a1 1 0 00-.176-.964A7.36 7.36 0 014 10.5C4 7.067 7.582 4 12 4z" />
                </svg>
                
                <!-- Comment Count -->
                <span id="comment-count-{{ $comment->commentid }}" 
                    class="ml-1 group-hover:text-sky-600 transition duration-200 ease-in-out">
                    {{ $comment->subcomments_count ?? 0 }}
                </span>
            </button>
        </div>
    </div>
    <div class="subcomments mt-4 pl-4 border-l border-gray-200">
    <!-- Loop through subcomments -->
    @foreach ($comment->subcomments as $subcomment)
        @include('partials.comment', ['comment' => $subcomment])
    @endforeach
    </div>
    <!-- Hidden form for adding subcomment -->
    <div id="subComment-form-{{ $comment->commentid }}" class="addComment mt-4 p-4 bg-gray-50 rounded-xl shadow-md border hidden">
        <form id="subCommentForm" action="{{ route('comments.storeSubcomment') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="parent_comment_id" value="{{ $comment->commentid }}">

            <!-- Text Area -->
            <textarea id="message" name="message" rows="3"
                    class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none resize-none placeholder-gray-400 text-gray-700 text-sm"
                    placeholder="Write your comment here..."></textarea>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <label for="image" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                            <path d="M19.828 11.244L12.707 18.364C10.755 20.317 7.589 20.317 5.636 18.364C3.684 16.411 3.684 13.246 5.636 11.293L12.472 4.458C13.774 3.156 15.884 3.156 17.186 4.458C18.488 5.759 18.488 7.87 17.186 9.172L10.361 15.996C9.71 16.647 8.655 16.647 8.004 15.996C7.353 15.345 7.353 14.29 8.004 13.639L14.226 7.418" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span class="text-sm">Attach Media</span>
                    </label>
                    <input type="file" name="media[]" id="image" class="hidden" multiple onchange="updateFileList()">
                </div>

                <button type="submit" class="px-6 py-2 bg-sky-700 text-white font-semibold rounded-xl hover:bg-sky-800 text-sm">
                    Comment
                </button>
            </div>

            <ul id="fileDisplay" class="text-sm text-gray-500 mt-2 hidden">
                <!-- File names appended dynamically -->
            </ul>
        </form>
    </div>
    @auth
        @if(auth()->id() === $comment->userid || Auth::user()->isadmin) 
            <!-- Edit Section in comment.blade.php -->
            <div id="edit-comment-{{ $comment->commentid }}" class="edit-comment-form hidden mt-4 bg-white rounded-xl shadow-md p-4" onclick="event.stopPropagation();">
                <form action="{{ route('comments.update', $comment->commentid) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4" data-comment-id="{{ $comment->commentid }}">
                    @csrf
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700">Edit Message</label>
                        <textarea id="message" name="message"
                            class="mt-1 block w-full p-4 border rounded-xl focus:ring-2 focus:ring-sky-700 shadow-sm outline-none" 
                            placeholder="Edit your message" 
                            style="resize: vertical; min-height: 200px;">{{ $comment->message }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Edit Media</label>

                        <label for="image-{{ $comment->commentid }}" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black mt-2">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span>Attach new file</span>
                        </label>
                    
                        <div id="fileDisplay-{{ $comment->commentid }}" class="flex-col items-center gap-2 text-gray-500 hover:text-black mt-2 {{ $comment->media->isEmpty() ? 'hidden' : '' }}">
                            @foreach ($comment->media as $mediaItem)
                                <div class="flex items-center gap-2" id="file-{{ $mediaItem->mediaid }}">
                                    <span class="text-sm text-gray-500">{{ basename($mediaItem->path) }}</span>
                                    <button type="button" onclick="removeFileEdit('{{ $comment->commentid }}', '{{ $mediaItem->mediaid }}'); event.stopPropagation();" class="text-sm text-red-500 hover:text-red-700">Remove</button>
                                </div>
                            @endforeach
                            <div id="newFiles-{{ $comment->commentid }}" class="flex-col gap-2">
                                <!-- New files to add appended via JS -->
                            </div>
                        </div>
                        <input type="file" name="media[]" id="image-{{ $comment->commentid }}" class="hidden" onchange="updateFileNameEditComment('{{ $comment->commentid }}')" multiple>
                        <input type="hidden" name="remove_media" id="removeMedia-{{ $comment->commentid }}" value="[]">
                    </div>

                    <button type="submit" class="p-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Update</button>
                </form>
            </div>
        @endif
    @endauth

</div>
