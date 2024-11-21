@foreach($posts as $post)
    <div class="post mb-4 p-4 bg-white rounded-md shadow-md">
        <div class="post-header mb-2">
            <h3 class="font-bold">
                <a href="{{ route('profile', $post->user->username) }}" class="text-black hover:text-sky-900">
                    {{ $post->user->username }}
                </a>
            </h3>
            <span class="text-gray-500 text-sm">{{ $post->createddate }}</span>
        </div>
        <div class="post-body mb-2">
            <p>{{ $post->message }}</p>
        </div>
    </div>
@endforeach
