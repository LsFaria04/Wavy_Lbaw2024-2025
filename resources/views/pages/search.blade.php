@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center w-full bg-white" id="searchPage">
        <div id="messageContainer" class="fixed top-6 flex items-center z-40">
            <!--Used to append messages with JS -->
        </div>

        <header id="search-header" class="w-full max-w-full pt-4 shadow-md items-center sticky top-0 z-10 backdrop-blur">
            <div class="flex items-center">

                <form action="{{ route('search') }}" method="GET" id="search-form" class="w-full max-w-5xl mx-auto">
                    <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded-3xl p-2.5 pl-5 w-full shadow-md focus:outline-none">
                    <input type="hidden" name="category" value="{{ old('category', $category ?? 'posts') }}">
                </form>
            </div>

            <!-- Category Buttons -->
            <nav class="category-buttons flex justify-around mt-2">
                <button type="button" data-category="posts" class="category-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 {{ $category === 'posts' ? 'border-sky-900 text-sky-900' : 'hover:text-sky-900' }}" onclick="changeCategory('posts')">Posts</button>
                <button type="button" data-category="users" class="category-btn flex-1 text-center py-3 text-sm font-semibold  border-b-2 {{ $category === 'users' ? 'border-sky-900 text-sky-900' : 'hover:text-sky-900' }}" onclick="changeCategory('users')">Users</button>
                <button type="button" data-category="groups" class="category-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 {{ $category === 'groups' ? 'border-sky-900 text-sky-900' : 'hover:text-sky-900' }}" onclick="changeCategory('groups')">Groups</button>
            </nav>
        </header>
        
        <!-- Search Results -->
        <section id="search-results" class="flex flex-col justify-items-center w-full max-w-full bg-white shadow-md pl-6 pr-6 pt-4">
            @if(empty($query))
                <div class="flex justify-center items-center h-32">
                    <p class="text-gray-600 text-center">Please insert a search term.</p>
                </div>
            @elseif($category == 'posts'  && !$posts->isEmpty())
                <button id="filter" class = "flex flex-row gap-2 self-start p-4" onclick = "toggleFilters()" >
                    Filter
                    <svg id="Layer_1" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                        <path d="m14.5 24a1.488 1.488 0 0 1 -.771-.214l-5-3a1.5 1.5 0 0 1 -.729-1.286v-5.165l-5.966-7.3a4.2 4.2 0 0 1 -1.034-2.782 4.258 4.258 0 0 1 4.253-4.253h13.494a4.254 4.254 0 0 1 3.179 7.079l-5.926 7.303v8.118a1.5 1.5 0 0 1 -1.5 1.5zm-3.5-5.35 2 1.2v-6a1.5 1.5 0 0 1 .335-.946l6.305-7.767a1.309 1.309 0 0 0 .36-.884 1.255 1.255 0 0 0 -1.253-1.253h-13.494a1.254 1.254 0 0 0 -.937 2.086l6.346 7.765a1.5 1.5 0 0 1 .338.949z"/>
                    </svg> 
                </button>
                @each('partials.post', $posts, 'post')
            @elseif($category == 'users' && !$users->isEmpty())
                <button id="filter" class = "flex flex-row gap-2 self-start p-4" onclick = "toggleFilters()" >
                    Filter
                    <svg id="Layer_1" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                        <path d="m14.5 24a1.488 1.488 0 0 1 -.771-.214l-5-3a1.5 1.5 0 0 1 -.729-1.286v-5.165l-5.966-7.3a4.2 4.2 0 0 1 -1.034-2.782 4.258 4.258 0 0 1 4.253-4.253h13.494a4.254 4.254 0 0 1 3.179 7.079l-5.926 7.303v8.118a1.5 1.5 0 0 1 -1.5 1.5zm-3.5-5.35 2 1.2v-6a1.5 1.5 0 0 1 .335-.946l6.305-7.767a1.309 1.309 0 0 0 .36-.884 1.255 1.255 0 0 0 -1.253-1.253h-13.494a1.254 1.254 0 0 0 -.937 2.086l6.346 7.765a1.5 1.5 0 0 1 .338.949z"/>
                    </svg> 
                </button>
                @include('partials.searchUsers', ['users' => $users])
            @elseif($category == 'groups' && !$groups->isEmpty())
                <button id="filter" class = "flex flex-row gap-2 self-start p-4" onclick = "toggleFilters()" >
                    Filter
                    <svg id="Layer_1" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                        <path d="m14.5 24a1.488 1.488 0 0 1 -.771-.214l-5-3a1.5 1.5 0 0 1 -.729-1.286v-5.165l-5.966-7.3a4.2 4.2 0 0 1 -1.034-2.782 4.258 4.258 0 0 1 4.253-4.253h13.494a4.254 4.254 0 0 1 3.179 7.079l-5.926 7.303v8.118a1.5 1.5 0 0 1 -1.5 1.5zm-3.5-5.35 2 1.2v-6a1.5 1.5 0 0 1 .335-.946l6.305-7.767a1.309 1.309 0 0 0 .36-.884 1.255 1.255 0 0 0 -1.253-1.253h-13.494a1.254 1.254 0 0 0 -.937 2.086l6.346 7.765a1.5 1.5 0 0 1 .338.949z"/>
                    </svg> 
                </button>
                @include('partials.searchGroups', ['groups' => $groups])
            @elseif (!empty($query))
                <div id="filter" class="flex justify-center items-center h-32">
                    <p class="text-gray-600 text-center">No results matched your search.</p>
                </div>
            @endif
            
        </section>
        @include('partials.reportForm')
        @include('partials.filterMenu')
        @include('partials.imageDetail')
    </div>
@endsection 