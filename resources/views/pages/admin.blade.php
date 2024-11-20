@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center w-full">

        <header class="w-full max-w-xl mb-6">
            <h1 class="text-3xl font-bold text-center mb-4">Admin Page</h1>
            <nav class="flex justify-around bg-gray-800 text-white p-3 rounded-lg">
            <button class="tab-btn px-4 py-2 text-white rounded-md hover:bg-blue-600" onclick="showSectionAdmin('posts')">Manage Posts</button>
            <button class="tab-btn px-4 py-2 text-white rounded-md hover:bg-blue-600" onclick="showSectionAdmin('users')">Manage Users</button>
            </nav>
        </header>
        <!-- Posts Section -->
        <section id="posts" class="w-full max-w-3xl p-6 mb-6 bg-slate-500 rounded-xl shadow-lg tab-section">
            <h2 class="text-xl font-semibold mb-4 text-white">Posts</h2>
            <div id="posts-list" class="space-y-4">
                @forelse($posts as $post)
                    <div class="bg-white p-4 rounded-md shadow-lg">
                        <h3 class="text-xl font-semibold">{{ $post->message }}</h3>
                        <p class="text-gray-700">Posted by: {{ $post->user->username }}</p>
                        <p class="text-gray-500">Created on: {{ $post->createddate->format('d M Y, H:i') }}</p>
                    </div>
                @empty
                    <p class="text-white">No posts available.</p>
                @endforelse
            </div>
        </section>

        <!-- Users Section -->
        <section id="users" class="w-full max-w-3xl p-6 mb-6 bg-slate-500 rounded-xl shadow-lg tab-section hidden">
            <h2 class="text-xl font-semibold mb-4 text-white">Users</h2>
            <div id="users-list" class="space-y-4">
                @forelse($users as $user)
                    <div class="bg-white p-4 rounded-md shadow-lg">
                        <h3 class="text-xl font-semibold">{{ $user->username }}</h3>
                        <p class="text-gray-700">{{ $user->email }}</p>
                    </div>
                @empty
                    <p class="text-white">No users available.</p>
                @endforelse
            </div>
        </section>

    </div>
@endsection
