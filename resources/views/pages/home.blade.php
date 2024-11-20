@extends('layouts.app')
@section('content')
    <div class="flex flex-col items-center w-full">
        <header class="w-full max-w-xl mb-6">
            <form action="{{ route('search') }}" method="GET" id="search-form">
                <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded p-2 w-full">
            </form>
        </header>

        <section id="timeline" class="p-6 max-w-xl w-full bg-slate-500 rounded-xl shadow-lg mx-auto">
            @auth
                <div class="addPost mb-6 p-4 bg-white rounded-md shadow-sm">
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="3" class="mt-1 block w-full p-2 border rounded-md" placeholder="Write something..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Upload Image (optional)</label>
                            <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full p-2 border rounded-md">
                        </div>

                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Post</button>
                        </div>
                    </form>
                </div>
            @endauth
            @if($posts->isEmpty())
                <p>No posts found.</p>
            @else
                @each('partials.post', $posts, 'post')
            @endif
        </section>
    </div>

@endsection
