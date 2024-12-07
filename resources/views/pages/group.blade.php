@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center w-full max-w-full bg-white" id="groupPage" data-groupid="{{ $group->groupid }}" data-ownerid="{{ $group->ownerid }}">
    <!-- Group Info Section (Sticky Header) -->
    <header id="group-header" class="w-full pt-6 shadow-md flex flex-col bg-white sticky top-0 z-10">
        <div class="text-center mb-4">
            <h1 id="group-name" class="text-2xl font-bold text-gray-800">{{ $group->groupname }}</h1>
            <p class="text-gray-500 mt-2">{{ $group->description ?? 'No description available.' }}</p>
        </div>
    
        @auth
            @if (!$group->members->contains(Auth::user()) && !Auth::user()->isadmin)
                <div class="flex justify-center mt-4">
                    <button id="ask-to-join-btn" class="px-4 py-2 bg-sky-700 text-white font-semibold rounded-md hover:bg-sky-800">
                        Ask to Join
                    </button>
                </div>
            @elseif ($group->members->contains(Auth::user()) && auth()->id() !== $group->ownerid && !Auth::user()->isadmin)
                <div class="flex justify-center mt-4">
                    <button type="button" onclick="openExitGroupMenu()" class="px-4 py-2 bg-red-700 text-white font-semibold rounded-md hover:bg-red-800">
                        Exit Group
                    </button>
                </div>                
            @elseif(auth()->id() === $group->ownerid || Auth::user()->isadmin)
                <div class="absolute top-0 right-0 mt-4 mr-4 flex items-center space-x-2">
                    <!-- Edit Group Button -->
                    <button 
                        class="px-4 py-2 font-bold bg-gray-800 text-white rounded-2xl"
                        onclick="toggleEditGroupMenu()">
                        Edit Group
                    </button>

                    <!-- Dropdown Trigger 
                    <button onclick="toggleDropdown()" class="focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 hover:text-gray-600" fill="black" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm0 5.25a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm0 5.25a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                        </svg>
                    </button> -->
                </div>

                <!-- Dropdown Menu
                <div id="dropdownMenu" class="hidden absolute top-16 right-4 w-40 bg-white border border-gray-200 rounded-md shadow-lg transition duration-300 ease-in-out">
                    <button 
                        onclick="toggleConfirmationModal()" 
                        class="w-full px-4 py-2 text-left text-sm text-red-600 hover:text-red-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-md">
                        Delete Account
                    </button>
                </div> -->
            @endif
        @endauth
    
        @if(($group->visibilitypublic == true || ($group->visibilitypublic === false && (Auth::user()->isadmin || $group->members->contains(Auth::user())))))
            <nav class="flex justify-around mt-4">
                <button id="tab-posts" data-tab="group-posts" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900 border-sky-900 text-sky-900">Posts</button>
                <button id="tab-members" data-tab="group-members" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">Members</button>
                @auth
                    @if (auth()->id() === $group->ownerid || Auth::user()->isadmin)
                        <button id="tab-invitations" data-tab="group-invitations" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">Invitations</button>
                        <button id="tab-requests" data-tab="group-requests" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">Join Requests</button>
                    @endif
                @endauth
            </nav>
        @endif
    </header>    

    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="alert w-full p-4 mb-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 rounded-lg z-10">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert w-full p-4 mb-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 rounded-lg z-10">
            {{ session('error') }}
        </div>
    @endif

    @auth
        @if ($group->members->contains(Auth::user()))
            <div class="addPost px-6 pb-6 mt-4 bg-white rounded-xl shadow-md flex flex-col w-full max-w-full" id="post-form">
                <h1 class="text-xl font-bold text-black pb-2">{{ Auth::user()->username }}</h1>
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="groupid" value="{{ $group->groupid }}">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <textarea id="message" name="message" rows="2" class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none block" placeholder="What's on your mind?"></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 relative">
                            <label for="image" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                    <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <span>Attach Media (Optional)</span>
                            </label>

                            <input type="file" name="media[]" id="image" class="hidden" multiple onchange="updateFileList()">
                            
                            <ul id="fileDisplay" class="items-center gap-2 hidden">
                                <!-- File names appended here by JavaScript -->
                            </ul>
                        </div>
                        
                        <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Post</button>
                    </div>       
                </form>            
            </div>
        @endif
    @endauth

    <!-- Edit Group Menu -->
    <div id="edit-group-menu" class="fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4">Edit Group</h2>
            <form action="{{ route('group.update', $group->groupid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="groupname" class="block text-sm font-medium text-gray-700">Group Name</label>
                    <input type="text" id="groupname" name="groupname" value="{{ $group->groupname }}" class="mt-1 block w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full p-2 border rounded-md">{{ $group->description }}</textarea>
                </div>
                <div class="mb-4">
                    <label for="visibilitypublic" class="block text-sm font-medium text-gray-700">Group Visibility</label>
                    <select id="visibilitypublic" name="visibilitypublic" class="mt-1 block w-full p-2 border rounded-md">
                        <option value="1" {{ $group->visibilitypublic ? 'selected' : '' }}>Public</option>
                        <option value="0" {{ !$group->visibilitypublic ? 'selected' : '' }}>Private</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-2xl hover:bg-gray-600" onclick="toggleEditGroupMenu()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-sky-700 text-white rounded-2xl hover:bg-sky-900">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Exit Group Confirmation Menu -->
    <div id="exitGroupMenu" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-20">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
            <h2 class="text-xl font-semibold text-gray-900">Leave Group</h2>
            <p class="mt-4 text-sm text-gray-600">Are you sure you want to leave this group? You will need to rejoin to regain access.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button id="cancelExitButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
                    Cancel
                </button>
                <form action="{{ route('group.leave', $group->groupid) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="confirmExitButton" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
                        Leave
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Member Confirmation Menu -->
    <div id="removeMemberMenu" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-20">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
            <h2 class="text-xl font-semibold text-gray-900">Remove Member</h2>
            <p id="removeMemberMessage" class="mt-4 text-sm text-gray-600"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button id="cancelRemoveButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
                    Cancel
                </button>
                <form id="removeMemberForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
                        Remove
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Content Tabs -->
    <div class="flex flex-col w-full max-w-full bg-white shadow-md p-6 mt-4" id="group-tab-content">

        <!-- Content Section (starts with the posts) -->
        @if(($group->visibilitypublic === false && !(Auth::id() === $group->ownerid || Auth::user()->isadmin || $group->members->contains(Auth::user()))))
            <div class="flex justify-center items-center h-32">
                <p class="text-gray-600 text-center">Group is private.</p>
            </div>
        @elseif($group->posts->isEmpty())
            <div class="flex justify-center items-center h-32">
                <p class="text-gray-600 text-center">No posts found for this group.</p>
            </div>
        @else
            @each('partials.post', $posts, 'post')
        @endif
    </div>
</div>
@endsection
