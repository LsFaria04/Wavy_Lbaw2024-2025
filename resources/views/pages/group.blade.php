@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white" id="groupPage">
        <!-- Group Info Section (Sticky Header) -->
        <header id="group-header" class="w-full pt-6 shadow-md flex flex-col bg-white sticky top-0 z-10 backdrop-blur">
            <!-- Group Name & Description -->
            <div class="text-center mb-4">
                <h1 id="group-name" class="text-2xl font-bold text-gray-800">{{ $group->groupname }}</h1>
                <p class="text-gray-500 mt-2">{{ $group->description ?? 'No description available.' }}</p>
            </div>

            <!-- Edit/Delete Buttons STILL TO-DO -->
            @auth
                @if(auth()->id() === $group->owner || Auth::user()->isadmin)
                    <div class="mt-4 flex items-center space-x-2 w-full">
                        <button 
                            class="w-1/2 px-4 py-2 font-bold bg-gray-800 text-white rounded-2xl"
                            onclick="toggleEditGroupMenu()">
                            Edit Group
                        </button>
                        <button 
                            onclick="toggleDeleteGroupMenu()" 
                            class="w-1/2 px-4 py-2 bg-red-600 text-white rounded-2xl hover:bg-red-800">
                            Delete Group
                        </button>
                    </div>
                @endif
            @endauth

            <!-- Tab Selection Buttons -->
            <nav class="flex justify-around mt-4">
                <button id="tab-posts" data-tab="group-posts" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900 border-sky-900 text-sky-900">Posts</button>
                <button id="tab-members" data-tab="group-members" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">Members</button>
            </nav>
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

        <!-- Content Tabs -->
        <div class="flex flex-col w-full max-w-full bg-white shadow-md p-6 mt-4" id="group-tab-content">
            <!-- Content Section (starts with the posts) -->
            @if(($group->visibilitypublic === false && !Auth::check())  || ($group->visibilitypublic === false && !Auth::user()->isadmin))
                <div class="flex justify-center items-center h-32">
                    <p class="text-gray-600 text-center">Group is private.</p>
                </div>
            @else
                <div id="group-posts" class="tab-content">
                    <p class="text-gray-600 text-center">Not implemented yet.</p>
                </div>
            @endif
        </div>
@endsection
