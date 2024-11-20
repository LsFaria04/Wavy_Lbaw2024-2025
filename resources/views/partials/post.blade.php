<div class="post mb-4 p-4 bg-white rounded-md shadow-sm">
    <div class="post-header mb-2">
        <h3 class="font-bold">
            <a href="{{ route('profile', $post->user->username) }}" class="text-black hover:text-sky-900">
                {{ $post->user->username }}
            </a>
        </h3>
        <span class="text-gray-500 text-sm">{{ $post->createddate->diffForHumans() }}</span>
    </div>
    <div class="post-body mb-2">
        <p>{{ $post->message }}</p>

        @auth
            @if(auth()->id() === $post->userid) <!-- Only show edit button for the post owner -->
                <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 mt-4" onclick="toggleEditPost({{ $post->postid }})">
                    Edit Post
                </button>

                <!-- Edit Post Form (hidden by default) -->
                <div id="edit-post-{{ $post->postid }}" class="edit-post-form hidden mt-4">
                    <form action="{{ route('posts.update', $post->postid) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="3" class="mt-1 block w-full p-2 border rounded-md" placeholder="Edit your message">{{ $post->message }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Upload Image (optional)</label>
                            <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full p-2 border rounded-md">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Post</button>
                    </form>
                </div>
            @endif
        @endauth
    </div>
</div>

@section('scripts')
    <script>
        function toggleEditPost(postid) {
            const editForm = document.getElementById(`edit-post-${postid}`);
            editForm.classList.toggle('hidden'); 
        }
    </script>
@endsection
