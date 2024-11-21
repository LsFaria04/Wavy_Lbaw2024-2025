@extends('layouts.app')
@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white">
        @if (session('error'))
            <div class = "absolute self-center alert w-full max-w-full p-4 mb-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 rounded-lg z-10">             
                {{ session('error')}}          
            </div>
        @elseif(session('success'))
            <div class = "absolute self-center alert w-full max-w-full p-4 mb-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 rounded-lg z-10">             
                {{ session('success')}}          
            </div>
        @endif 
        <section id="timeline" class="p-6 max-w-full w-full bg-white rounded-xl shadow-lg mx-auto">
            @if(Auth::check() && !Auth()->user()->isadmin)
                <div class="addPost mb-6 p-4 bg-white rounded-xl shadow-md">
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">What's on your mind?</label>
                            <textarea id="message" name="message" rows="3" class="mt-1 block w-full p-4 border rounded-xl shadow-sm outline-none" placeholder="Write something..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Upload Image (optional)</label>
                            <input type="file" name="media" id="image"  class="mt-1 block w-full p-3 border rounded-xl shadow-sm">
                        </div>

                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-3xl hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Post</button>
                        </div>
                    </form>
                </div>
            @endif
            @if($posts->isEmpty())
                <p>No posts found.</p>
            @else
                @each('partials.post', $posts, 'post')
            @endif
        </section>
    </div>

@endsection
