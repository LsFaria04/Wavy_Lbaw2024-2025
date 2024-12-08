<!-- resources/views/pages/groups.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center w-full bg-white" id="groupsPage">
        <header id="groups-header" class="w-full max-w-full pt-4 shadow-md items-center sticky top-0 z-10 backdrop-blur">
            <div class="flex items-center">
                <form action="{{ route('groupList') }}" method="GET" id="search-form" class="w-full max-w-5xl mx-auto">
                    <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search for groups..." class="border rounded-3xl p-2.5 pl-5 w-full shadow-md focus:outline-none">
                    <input type="hidden" name="category" value="{{ old('category', $category ?? 'your-groups') }}">
                </form>
            </div>

            <!-- Category Buttons -->
            <nav class="category-buttons flex justify-around mt-2">
                <button type="button" data-category="your-groups" class="category-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 {{ $category === 'your-groups' ? 'border-sky-900 text-sky-900' : 'hover:text-sky-900' }}" onclick="changeGroupCategory('your-groups')">Your Groups</button>
                <button type="button" data-category="search-groups" class="category-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 {{ $category === 'search-groups' ? 'border-sky-900 text-sky-900' : 'hover:text-sky-900' }}" onclick="changeGroupCategory('search-groups')">Search Groups</button>
            </nav>
        </header>
        
        <!-- Groups Results -->
        <section id="group-results" class="flex flex-col justify-items-center w-full max-w-full bg-white shadow-md pl-6 pr-6 pt-4">
            @if($category == 'your-groups')
                @if($userGroups->isEmpty())
                    <div class="flex justify-center items-center h-32">
                        <p class="text-gray-600 text-center">You are not a part of any groups yet.</p>
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
                            <p class="text-gray-600 text-center">No groups matched your search.</p>
                        </div>
                    @else
                        @include('partials.searchGroups', ['groups' => $searchGroups])
                    @endif
                @endif
            @endif
        </section>
    </div>
@endsection
