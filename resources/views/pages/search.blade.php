    @extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full">
            <header id="search-header" class="w-full max-w-screen-lg pt-4 bg-white rounded-lg shadow-md items-center sticky top-0 z-10 bg-opacity-40">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center text-gray-500 hover:text-gray-700 mr-4 pl-2">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>

                    <form action="{{ route('search') }}" method="GET" id="search-form" class="w-full max-w-3xl mx-auto">
                        <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded-3xl p-2.5 w-full shadow-md focus:outline-none">
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
            <section id="search-results" class="p-6 max-w-xl w-full bg-slate-500 rounded-xl shadow-lg mt-2">
                @if(empty($query))
                    <p class="text-white">Please insert a search term.</p>
                @elseif($category == 'posts'  && !$posts->isEmpty())
                    @include('partials.searchPosts', ['posts' => $posts])
                @elseif($category == 'users' && !$users->isEmpty())
                    @include('partials.searchUsers', ['users' => $users])
                @elseif($category == 'groups' && !$groups->isEmpty())
                    @include('partials.searchGroups', ['groups' => $groups])
                @elseif (!empty($query))
                    <p class="text-white">No results matched your search.</p>
                @endif
            </section>
        </div>
    @endsection