@extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full max-w-full bg-white">
            <!-- Profile Top Section -->
            <header id="profile-header" class="w-full max-w-full p-4 flex items-center sticky top-0 z-10 backdrop-blur shadow">
                <!-- <a href="{{ url()->previous() }}" class="flex items-center text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a> -->
                <h1 id = "profile-username" class="text-xl font-bold text-gray-800">{{ $user->username }}</h1>
            </header>

        <!-- Edit Profile Messages -->
        @if (session('success'))
            <div class="absolute self-center alert w-full max-w-full p-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 z-10">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="absolute self-center alert w-full max-w-full p-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 z-10">
                {{ session('error') }}
            </div>
        @endif
        <div id="messageContainer" class="fixed top-6 flex items-center z-40">
            <!--Used to append messages with JS -->
        </div>


        <!-- Background Section -->
        <div class="w-full max-w-full relative bg-gray-300 h-48 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center">
                <!-- Background Image To Add -->
            </div>
        </div>

        <!-- Profile Info Section -->
        <div class="w-full max-w-full relative bg-white shadow">
            <div class="absolute -top-16 left-4 w-32 h-32 bg-gray-200 rounded-full border-4 border-white overflow-hidden">
                <!-- Profile Image To Add -->
            </div>

            <!-- Edit Profile only visible if Account owner -->
            <div class="pt-20 px-6 pb-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">{{ $user->username }}</h1>
                        @auth
                            @if (auth()->id() !== $user->userid && !Auth::user()->isadmin)
                                <form action="{{ route('follow', ['userid' => $user->userid]) }}" method="POST">
                                    @csrf
                                    <button id="follow-btn" data-userid="{{ $user->userid }}" type="submit" class="px-4 py-1.5 font-semibold text-white rounded-2xl hover:bg-sky-900
                                        @if ($followStatus === 'Accepted') bg-red-500 
                                        @elseif ($followStatus === 'Pending') bg-yellow-500 
                                        @else bg-sky-700 @endif">
                                        @if ($followStatus === 'Accepted')
                                            Unfollow
                                        @elseif ($followStatus === 'Pending')
                                            Pending Request
                                        @else
                                            Follow
                                        @endif
                                    </button>
                                </form>
                            @endif
                        @endauth
                </div>

                <p class="text-gray-500 mt-2">{{ $user->bio ?? 'No bio available.' }}</p>

                @auth
                @if(auth()->id() === $user->userid || Auth::user()->isadmin)
                    <div class="absolute top-0 right-0 mt-4 mr-4 flex items-center space-x-2">
                        <!-- Edit Profile Button -->
                        <button 
                            class="px-4 py-2 font-bold bg-gray-800 text-white rounded-2xl"
                            onclick="toggleEditMenu()">
                            Edit Profile
                        </button>

                        <!-- Dropdown Trigger -->
                        <button onclick="toggleDropdown()" class="focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 hover:text-gray-600" fill="black" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm0 5.25a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm0 5.25a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu" class="hidden absolute top-16 right-4 w-40 bg-white border border-gray-200 rounded-md shadow-lg transition duration-300 ease-in-out">
                        <button
                            onclick = "toggleMyTopics()"
                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                            My Topics
                        <button>
                        <button 
                            onclick="toggleConfirmationModal()" 
                            class="w-full px-4 py-2 text-left text-sm text-red-600 hover:text-red-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                            Delete Account
                        </button>
                    </div>
                @endif
                @endauth
            </div>

            <nav class="flex justify-around">
                <button id="tab-posts" data-tab="user-posts" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900  border-sky-900 text-sky-900">Posts</button>
                <button id="tab-comments" data-tab="user-comments" class="tab-btn flex-1 text-center py-3 text-sm font-semibold  border-b-2 hover:text-sky-900">Comments</button>
                <button id="tab-likes" data-tab="user-likes" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">Likes</button>
            </nav>
        </div>

            <!-- Edit Profile Menu -->
            <div id="edit-profile-menu" class="fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden">
                <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>
                    <form action="{{ route('profile.update', $user->userid) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="username" name="username" value="{{ $user->username }}" class="mt-1 block w-full p-2 border rounded-md" required autocomplete="username">
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
            <div class="flex flex-col w-full max-w-full bg-white" id = "profile-tab-content">
                <!-- Content Section (starts with the posts) -->
                    @if((($user->visibilitypublic === false && !Auth::check())  || ($user->visibilitypublic === false && !Auth::user()->isadmin)) && ($user->userid != auth()->id()))
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">Account is private.</p>
                        </div>
                    @elseif($posts->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No posts found for this user.</p>
                        </div>
                    
                    @else
                        @each('partials.post', $posts, 'post')
                    @endif
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center hidden">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Are you sure you want to delete the account?</h2>
                
                <!-- Form for Deleting Profile -->
                <form action="{{ route('profile.delete', $user->userid) }}" method="POST" id="deleteProfileForm">
                    @csrf
                    @method('DELETE')

                    <!-- Hidden username field for accessibility -->
                    <input type="hidden" name="username" value="{{ $user->username }}" autocomplete="username">
                    
                    <!-- Password input section will only appear if the user is the owner -->
                    <div id="passwordForm" class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Enter your password</label>
                        <input type="password" id="password" name="password" class="mt-1 block w-full p-2 border rounded-md" required autocomplete="current-password">
                        <p id="passwordError" class="text-sm text-red-600 hidden">Incorrect password. Please try again.</p>
                    </div>
                    
                    <div class="flex justify-end space-x-2">
                        <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-2xl hover:bg-gray-600" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-2xl hover:bg-red-800" onclick="confirmDeleteProfile()">Delete</button>
                    </div>
                </form>
            </div>
        </div>

        <!--topics menu-->
        <div id="myTopics" class = "fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <header class = "grid grid-cols-3 justify-center w-full max-w-full mb-4">
                    <button onclick = "toggleMyTopics()" class="col-start-1 col-span-1 justify-self-start">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <h2 class=" col-start-2  text-2xl self-center font-bold text-nowrap">My Topics</h2>  
                    <button onclick = "toggleAddTopics()" class="justify-self-end mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="20" height="20">
                            <g>
                                <path d="M480,224H288V32c0-17.673-14.327-32-32-32s-32,14.327-32,32v192H32c-17.673,0-32,14.327-32,32s14.327,32,32,32h192v192   c0,17.673,14.327,32,32,32s32-14.327,32-32V288h192c17.673,0,32-14.327,32-32S497.673,224,480,224z" fill= "currentColor"/>
                            </g>
                        </svg>
                    </button> 
                </header>
                <form onsubmit="searchMyTopics(event)">
                    <input id="myTopicsSearch" type="search" autocomplete="off" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Topics" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
                </form>
                <section id="myTopicsList" class="border-[1px] rounded border-gray-300 h-60 overflow-y-scroll mb-4">
                    <ul class = "topicList flex flex-col justify-center items-center"></ul>
                    <button onclick="loadMoreTopics(true,false,'')" class= "flex w-full justify-center items-center">
                        <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <p>Show More</p>
                    <button>
                </section>
            </div>
        </div>

        <!-- Add Topic -->
        <div id="addTopics" class = "fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <header class = "grid grid-cols-3 justify-center w-full max-w-full mb-4">
                    <button onclick = "toggleAddTopics()" class="col-start-1 col-span-1 justify-self-start">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <h2 class=" col-start-2  text-2xl self-center font-bold text-nowrap">Add a Topic</h2>  
                </header>
                <form onsubmit="searchTopics(event)">
                    <input id="topicsSearch" type="search" autocomplete="off"  name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Topics" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
                </form>
                <div id="topicsList" class="border-[1px] rounded border-gray-300 h-60 overflow-y-scroll mb-4">
                    <ul class = "topicList flex flex-col justify-center items-center"></ul>
                    <button onclick="loadMoreTopics(false,false,'')" class= "flex w-full justify-center items-center">
                        <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <p>Show More</p>
                    <button>
                    </div>
            </div>
        </div>
        @include('partials.addPostTopics')
        @include('partials.reportForm')
    @endSection
