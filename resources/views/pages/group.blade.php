@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center w-full max-w-full bg-white" id="groupPage" data-groupid="{{ $group->groupid }}" data-ownerid="{{ $group->ownerid }}">
    <!-- Group Info Section (Sticky Header) -->
    <header id="group-header" class="w-full pt-6 shadow flex flex-col bg-white sticky top-0 z-10">
        <div class="text-center mb-4">
            <h1 id="group-name" class="text-3xl font-extrabold text-gray-800">{{ $group->groupname }}</h1>
            <p class="text-gray-500 mt-2">{{ $group->description ?? 'No description available.' }}</p>
        </div>
    
        @auth
            <div class="flex items-center justify-center mt-2 gap-2">
                @if (!$group->members->contains(Auth::user()) && !Auth::user()->isadmin)
                    <button id="ask-to-join-btn" class="px-6 py-2 bg-sky-700 text-white font-medium rounded-lg hover:bg-sky-900">
                        Ask to Join
                    </button>
                @elseif ($group->members->contains(Auth::user()) && auth()->id() !== $group->ownerid && !Auth::user()->isadmin)
                    <button type="button" onclick="openExitGroupMenu()" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700">
                        Exit Group
                    </button>
                @elseif(auth()->id() === $group->ownerid || Auth::user()->isadmin)
                    <button id="invite-users-btn" class="px-5 py-2 bg-sky-700 text-white font-medium rounded-lg hover:bg-sky-800">
                        Invite Users
                    </button>
                    <button class="px-6 py-2 bg-gray-800 text-white font-medium rounded-lg hover:bg-black" onclick="toggleEditGroupMenu()">
                        Edit Group
                    </button>
                    <button type="button" onclick="openDeleteGroupMenu()" class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700">
                        Delete Group
                    </button>
                @endif
            </div>
        @endauth
    
        @if(($group->visibilitypublic || Auth::user()->isadmin || $group->members->contains(Auth::user())))
            <nav class="flex w-full justify-around mt-4">
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

    <!-- Edit Group Menu -->
    <div id="edit-group-menu" class="fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden z-20">
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
                    <button type="button" class="px-4 py-2 w-20 bg-gray-700 text-white font-semibold rounded-3xl hover:bg-gray-800" onclick="toggleEditGroupMenu()">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for inviting users -->
    <div id="invite-modal" class="fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden z-50">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
            <header class="grid grid-cols-3 justify-center w-full max-w-full mb-4">
                <button id="close-invite-modal" class="text-gray-500 hover:text-gray-700 col-start-1 col-span-1 justify-self-start">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button> 
                <h2 class="col-start-2  text-2xl self-center font-bold text-nowrap">Invite Users</h3>
                </header>
            <input
                type="text"
                id="user-search"
                placeholder="Search for a user..."
                class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300"
            />
            <div id="search-results" class="max-h-64 overflow-y-auto pb-3">
                <!-- Search results will be dynamically injected here -->
            </div>
            <button id="send-invite" class="px-4 py-2 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800 disabled:opacity-50" disabled>
                Send Invite
            </button>
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

    <!-- Delete Group Confirmation Menu -->
    <div id="deleteGroupMenu" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-20">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
            <h2 class="text-xl font-semibold text-gray-900">Delete Group</h2>
            <p class="mt-4 text-sm text-gray-600">
                Are you sure you want to delete this group? This action cannot be undone.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button id="cancelDeleteButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
                    Cancel
                </button>
                <form action="{{ route('groups.delete', $group->groupid) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="confirmDeleteButton" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
                        Delete
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

    @auth
        @if ($group->members->contains(Auth::user()))
            <div id="addPostSection" class="w-full">
                @include('partials.createPost', ["group" => $group])
            </div>
        @endif
    @endauth

    <!-- Content Tabs -->
    <div class="flex flex-col w-full max-w-full h-full max-h-full bg-white" id="group-tab-content">
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
            @foreach ($posts as $post )
                @include('partials.post', ['post' => $post, 'group' => $group])
            @endforeach
            
            @include('partials.reportForm')
            @include('partials.imageDetail')
        @endif
        @include('partials.addPostTopics')
    </div>
</div>
@endsection
