@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center w-full">
    <!-- Profile Header -->
    <header class="w-full max-w-xl mb-6 p-6 bg-white rounded-xl shadow-md">
        <div class="flex items-center">
            <!-- User's Avatar -->
            <div class="w-20 h-20 rounded-full bg-gray-300 flex-shrink-0">
                <!-- Placeholder for user's avatar -->
            </div>
            <!-- User Information -->
            <div class="ml-4">
                <h1 class="text-2xl font-bold">{{ $user->username }}</h1>
                <p class="text-gray-500">{{ $user->bio ?? 'No bio available.' }}</p>
            </div>
        </div>
    </header>

    <!-- User's Posts Section -->
    <section id="user-posts" class="p-6 max-w-xl w-full bg-slate-500 rounded-xl shadow-lg">
        <h2 class="text-xl foant-bold mb-4">Posts</h2>
        @if($user->posts->isEmpty())
            <p>No posts found for this user.</p>
        @else
            @foreach($user->posts as $post)
                <div class="post mb-4 p-4 bg-white rounded-md shadow-sm">
                    <div class="post-header mb-2">
                        <h3 class="font-bold">{{ $user->username }}</h3>
                        <span class="text-gray-500 text-sm">{{ $post->createddate }}</span>
                    </div>
                    <div class="post-body mb-2">
                        <p>{{ $post->message }}</p>
                    </div>
                    <div class="post-footer text-sm text-gray-600">
                        <span>Visibility: {{ $post->visibilitypublic ? 'Public' : 'Private' }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </section>
</div>
@endSection