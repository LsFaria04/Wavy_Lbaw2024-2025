@extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full max-w-full bg-white">
            <!-- Profile Top Section -->
            <header id="profile-header" class="w-full max-w-full p-4 flex items-center sticky top-0 z-10 backdrop-blur shadow">
                <h1 id = "profile-username" class="text-xl font-bold text-gray-800">{{ $user->username }}</h1>
            </header>

        <!-- Edit Profile Messages -->
        <div class="fixed top-6 flex items-center z-50">
            @if (session('error'))
                <div class = "self-center alert w-full max-w-full p-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 z-10">             
                    {{ session('error')}}          
                </div>
            @elseif(session('success'))
                <div class = "self-center alert w-full max-w-full p-4 bg-blue-100 text-blue-800 border shadow-md text-center border-blue-300 z-10">             
                    {{ session('success')}}          
                </div>
            @endif 
        </div>
        <div id="messageContainer" class="fixed top-6 flex items-center z-40">
            <!--Used to append messages with JS -->
        </div>


        <!-- Background Section -->
        <div class="w-full max-w-full relative bg-gray-300 h-48 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center">
                @php
                    $filePath = null;
                    foreach($user->profilepicture as $pic)
                    if(Str::contains($pic, 'banner')) {
                        $filePath = asset('storage/' . $pic->path);
                    }
                    
                @endphp
                @if($filePath !== null)
                    <img  src="{{ $filePath }}" alt="Image" class=" h-full w-full object-cover rounded-md mb-2 mx-auto" >
                @endif
            </div>
        </div>

        <!-- Profile Info Section -->
        <div class="w-full max-w-full relative bg-white shadow">
            <div class="absolute -top-16 left-4 w-32 h-32 bg-gray-200 rounded-full border-4 border-white overflow-hidden">
                <!-- Profile Image To Add -->
                @php
                foreach($user->profilepicture as $pic)
                    if(Str::contains($pic, 'profile')) {
                        $filePath = asset('storage/' . $pic->path);
                    }
                @endphp
                @if($filePath)
                    <img  src="{{ $filePath }}" alt="Image" class=" h-full w-full object-cover rounded-md mb-2 mx-auto" >
                @endif
            </div>

            <!-- Edit Profile only visible if Account owner -->
            <div class="pt-20 px-6 pb-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">{{ $user->username }}</h1>
                    @auth
                        @if (!Auth::user()->isadmin && auth()->id() !== $user->userid)
                            <button id="follow-btn" 
                                    data-userid="{{ $user->userid }}" 
                                    data-follow-status="{{ $followStatus }}"
                                    data-is-private="{{ !$user->visibilitypublic ? 'true' : 'false' }}"
                                    class="px-4 py-1.5 font-semibold text-white rounded-2xl
                                        @if ($followStatus === 'Accepted') bg-red-500 hover:bg-red-700 
                                        @elseif ($followStatus === 'Pending') bg-yellow-500 hover:bg-yellow-700 
                                        @else bg-sky-700 hover:bg-sky-900 @endif">
                                @if ($followStatus === 'Accepted')
                                    Unfollow
                                @elseif ($followStatus === 'Pending')
                                    Pending Request
                                @elseif (!$user->visibilitypublic)
                                    Request to Follow
                                @else
                                    Follow
                                @endif
                            </button>
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
                        @if (!Auth::user()->isadmin)
                            <button
                                onclick = "toggleMyTopics()"
                                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                                My Topics
                            </button>
                        @endif
                        @if (Auth::user()->isadmin)
                            <button
                                id = "profileBan"
                                onclick = "showBanAdminMenu({{$user->userid}}, '{{$user->state}}')"
                                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                                {{$user->state === 'suspended' ? 'Unban account' : 'Ban account'}}
                            <button>   
                        @endif
                        @if (!Auth::user()->isadmin)
                            <button
                                onclick = "toggleFollowList()"
                                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                                My Follows
                            </button>
                        @endif
                        @if (!Auth::user()->isadmin)
                            <button
                                onclick = "toggleFollowerList()"
                                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                                My Followers
                            </button>
                        @endif
                        @if (!Auth::user()->isadmin)
                            <button
                                onclick = "toggleFollowRequests()"
                                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                                Follow Requests
                            </button>
                        @endif
                        <button 
                            onclick="toggleConfirmationModal()" 
                            class="w-full px-4 py-2 text-left text-sm text-red-600 hover:text-red-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                            Delete Account
                        </button>
                    </div>
                @endif
                @endauth
            </div>

            <div class = "px-6 pb-4 flex flex-row gap-4">
                <div class = "flex flex-row gap-2">
                    <p id = "followers_count" class = "font-semibold">{{$user->followers_count}}</p>
                    <p class = " text-gray-500">Followers</p>  
                </div>
                <div class = "flex flex-row gap-2 ">
                    <p id = "following_count" class = "font-semibold">{{$user->follows_count}}</p>
                    <p class = " text-gray-500">Following</p>   
                </div>
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
                    <form action="{{ route('profile.update', $user->userid) }}" method="POST" enctype="multipart/form-data">
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
                        <div class="mb-4">
                            <label for="profilePic" class="cursor-pointer flex flex-row text-sm font-medium text-gray-700">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                    <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <span>Profile Picture</span>
                            </label>
                            <div id="profilePicDisplay" class="flex-col gap-2">
                                <!-- New files to add appended via JS -->
                            </div>
                            <input class = "hidden" type="file" id="profilePic" name = "profilePic" onchange = "updateFileProfile(false)">
                        </div>
                        <div class="mb-4">
                            <label for="bannerPic" class="cursor-pointer flex flex-row text-sm font-medium text-gray-700">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                    <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <span>Banner Picture</span>
                            </label>
                            <div id="bannerPicDisplay" class="flex-col gap-2">
                                <!-- New files to add appended via JS -->
                            </div>
                            <input class = "hidden" type="file" id="bannerPic" name = "bannerPic" onchange = "updateFileProfile(true)" >
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" class="px-4 py-2 w-20 bg-gray-700 text-white font-semibold rounded-3xl hover:bg-gray-800" onclick="toggleEditMenu()">Cancel</button>
                            <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Content Tabs -->
            <div class="flex flex-col w-full max-w-full bg-white" id = "profile-tab-content">
                <!-- Content Section (starts with the posts) -->
                    @if((($user->visibilitypublic === false && !Auth::check())  || ($user->visibilitypublic === false && ($followStatus === "not-following" || $followStatus === "Pending"))) && ($user->userid != auth()->id()))
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
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_2" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="20" height="20">
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
                        <span>Show More</span>
                    </button>
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
                        <span>Show More</span>
                    </button>
                    </div>
            </div>
        </div>

   <div id="croppModal" class = "hidden w-full fixed inset-0 bg-black bg-opacity-50  items-center justify-center">
        <div class="flex flex-col content-evenly flex-wrap place-content-evenly bg-white w-full lg:w-[500px] lg:h-[550px] max-w-screen max-h-screen rounded-xl shadow-lg">
            <h3 class = "font-semibold text-xl ml-4 my-2">Image Preview</h3>
            <div  id = "croppPreview" class = "p-4 w-[500px] h-[400px] overflow-hidden rounded-lg">
                <img id = "image" alt="Image preview" src = "storage/app/public/images/xAUDHa0TJjgx4P46o7PRmeil1E8HiP4pimYBhLBC" class = "rounded-xl">
            </div>
            <button onclick = "closeImagePreview()" class = "ml-4 my-2 px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800" >Done</button>
        </div>
   </div> 
        @include('partials.reportForm')
        @include('partials.admin.banMenu')
        @include('partials.imageDetail')
        @include('partials.followRequests')
        @include('partials.followsList')
        @include('partials.followersList')
    @endSection
