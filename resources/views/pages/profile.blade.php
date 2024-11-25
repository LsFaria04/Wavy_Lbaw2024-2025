@extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full max-w-full bg-white">
            <!-- Profile Top Section -->
            <header id="profile-header" class="w-full max-w-full p-4 shadow-md flex items-center sticky top-0 z-10 backdrop-blur">
                <a href="{{ url()->previous() }}" class="flex items-center text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 id = "profile-username" class="text-xl font-bold text-gray-800">{{ $user->username }}</h1>
            </header>

        <!-- Edit Profile Messages -->
        @if (session('success'))
            <div class="absolute self-center alert w-full max-w-full p-4 mb-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 rounded-lg z-10">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="absolute self-center alert w-full max-w-full p-4 mb-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 rounded-lg z-10">
                {{ session('error') }}
            </div>
        @endif

        <!-- Background Section -->
        <div class="w-full max-w-full relative bg-gray-300 h-48 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center">
                <!-- Background Image To Add -->
            </div>
        </div>

        <!-- Profile Info Section -->
        <div class="w-full max-w-full relative bg-white shadow-md">
            <div class="absolute -top-16 left-4 w-32 h-32 bg-gray-200 rounded-full border-4 border-white overflow-hidden">
                <!-- Profile Image To Add -->
            </div>

            <!-- Edit Profile only visible if Account owner -->
            <div class="pt-20 px-6 pb-4">
                <h1 class="text-2xl font-bold">{{ $user->username }}</h1>
                <p class="text-gray-500 mt-2">{{ $user->bio ?? 'No bio available.' }}</p>
                @if(auth()->id() === $user->userid || Auth::user()->isadmin)
                    <button 
                        class="absolute top-0 right-0 mt-4 mr-4 px-4 py-2 font-bold bg-gray-800 text-white rounded-2xl"
                        onclick="toggleEditMenu()">
                        Edit Profile
                    </button>
                @endif
            </div>

            <nav class="flex justify-around">
                <button id="tab-posts" data-tab="user-posts" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900  border-sky-900 text-sky-900">Posts</button>
                <button id="tab-comments" data-tab="user-comments" class="tab-btn flex-1 text-center py-3 text-sm font-semibold  border-b-2 hover:text-sky-900">Comments</button>
                <button id="tab-likes" data-tab="user-likes" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">Likes</button>
            </nav>
        </div>

            <!-- Edit Profile Menu -->
            <div id="edit-profile-menu" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>
                    <form action="{{ route('profile.update', $user->userid) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="username" name="username" value="{{ $user->username }}" class="mt-1 block w-full p-2 border rounded-md" required>
                        </div>
                        <div class="mb-4">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full p-2 border rounded-md">{{ $user->bio }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="visibilitypublic" class="block text-sm font-medium text-gray-700">Profile Visibility</label>
                            <select id="visibilitypublic" name="visibilitypublic" class="mt-1 block w-full p-2 border rounded-md">
                                <option value="1" {{ $user->visibilitypublic ? 'selected' : '' }}>Public</option>
                                <option value="0" {{ !$user->visibilitypublic ? 'selected' : '' }}>Private</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-2xl hover:bg-gray-600" onclick="toggleEditMenu()">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-sky-700 text-white rounded-2xl hover:bg-sky-900">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Content Tabs -->
            <div class="flex flex-col w-full max-w-full bg-white shadow-md pl-6 pr-6 pt-4" id = "profile-tab-content">
                <!-- Content Section (starts with the posts) -->
                    @if($posts->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No posts found for this user.</p>
                        </div>
                    @else
                        @each('partials.post', $posts, 'post')
                    @endif
            
                <!--
                Comments Section
                <section id="user-comments" class="tab-content hidden">
                    @if(empty($comments))
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No comments found for this user.</p>
                        </div>
                    @else
                        @foreach($comments as $comment)
                            <div class="mb-4 p-4 bg-white rounded-md shadow">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800">{{ $comment->user->username }}</h3>

                                    @if($comment->parentcommentid)
                                        <p class="text-sm hover:text-sky-900">
                                            <strong>Replying to:</strong>
                                            {{ $comment->parentcomment->user->username }}
                                        </p>
                                    @else
                                        <p class="text-sm hover:text-sky-900">
                                            <strong>Replying to:</strong>
                                            {{ $comment->post->user->username }}
                                        </p>
                                    @endif 
                                </div>
                                <span class="text-sm text-gray-500">{{ $comment->createddate->diffForHumans() }}</span>
                                <p class="mt-2 text-gray-700">{{ $comment->message }}</p>
                            </div>
                        @endforeach
                    @endif
                </section>
                <section id="user-likes" class="tab-content hidden">
                    <div class="flex justify-center items-center h-32">
                        <p class="text-gray-600 text-center">Likes TO-DO.</p>
                    </div>
                </section> -->
            </div>
        </div>
    @endSection