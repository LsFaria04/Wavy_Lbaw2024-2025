<!-- resources/views/pages/groups.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center w-full bg-white" id="groupsPage">
        <header id="groups-header" class="w-full max-w-full pt-4 shadow-md items-center sticky top-0 z-10 backdrop-blur">
            <div class="flex items-center">
                <form action="{{ route('groupList') }}" method="GET" id="searchGroup-form" class="w-full max-w-5xl mx-auto">
                    <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search for groups..." class="border rounded-3xl p-2.5 pl-5 w-full shadow-md focus:outline-none">
                    <input type="hidden" name="category" value="{{ old('category', $category ?? '') }}">
                </form>
            </div>

            <!-- Category Buttons -->
            <nav class="category-buttons flex justify-around mt-2">
                @if(Auth::check()) 
                    <button type="button" data-category="your-groups" class="category-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 {{ $category === 'your-groups' ? 'border-sky-900 text-sky-900' : 'hover:text-sky-900' }}" onclick="changeGroupCategory('your-groups')">Your Groups</button>
                @endif
                <button type="button" data-category="search-groups" class="category-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 {{ $category === 'search-groups' ? 'border-sky-900 text-sky-900' : 'hover:text-sky-900' }}" onclick="changeGroupCategory('search-groups')">Search Groups</button>
            </nav>
        </header>

        <div id="create-group-menu" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Create Group</h2>
                <form action="{{ route('group.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="groupname" class="block text-sm font-medium text-gray-700">Group Name</label>
                        <input type="text" id="groupname" name="groupname" class="mt-1 block w-full p-2 border rounded-md" required>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full p-2 border rounded-md"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="visibilitypublic" class="block text-sm font-medium text-gray-700">Group Visibility</label>
                        <select id="visibilitypublic" name="visibilitypublic" class="mt-1 block w-full p-2 border rounded-md">
                            <option value="1">Public</option>
                            <option value="0">Private</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-2xl hover:bg-gray-600" onclick="toggleCreateGroupMenu()">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-2xl hover:bg-green-900">Create</button>
                    </div>
                </form>
            </div>
        </div>        
        
        <!-- Groups Results -->
        <section id="group-results" class="flex flex-col justify-items-center w-full max-w-full bg-white shadow-md pl-6 pr-6 pt-4">
            @auth
                <button id="create-group-btn" 
                        class="px-4 py-2 w-40 justify-center bg-green-700 text-white rounded-md hover:bg-green-800"
                        onclick="toggleCreateGroupMenu()">
                    Create Group
                </button>
            @endauth
            @if($category == 'your-groups')
                @if(Auth::user()->groups->isEmpty())
                    <div class="flex justify-center items-center h-32">
                        <p class="text-gray-600 text-center">You are not a part of any groups yet.</p>
                    </div>
                @elseif($userGroups->isEmpty())
                    <div class="flex justify-center items-center h-32">
                        <p class="text-gray-600 text-center">No groups found matching your search.</p>
                    </div>
                @else
                    @include('partials.searchGroups', ['groups' => $userGroups])
                @endif
            @elseif($category == 'search-groups')
                @if(empty($query))
                    <div class="flex justify-center items-center h-32">
                        <p class="text-gray-600 text-center">Please enter a search term to find groups.</p>
                    </div>
                @else
                    @if($searchGroups->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No groups found matching your search.</p>
                        </div>
                    @else
                        @include('partials.searchGroups', ['groups' => $searchGroups])
                    @endif
                @endif
            @endif
        </section>
    </div>
@endsection
