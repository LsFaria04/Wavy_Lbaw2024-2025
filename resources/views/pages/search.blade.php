    @extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full">
            <header class="w-3/4 mb-6 self-center md:w-full md:max-w-xl p-2">
                <form action="{{ route('search') }}" method="GET" id="search-form" class="w-full max-w-3xl mx-auto">
                    <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded p-2 w-full shadow-md focus:outline-none">
                    <input type="hidden" name="category" value="{{ old('category', $category ?? 'posts') }}">
                </form>
            </header>
            <!-- Buttons -->
            <div class="category-buttons my-4">
            <button type="button" class="ca tegory-btn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" data-category="posts" onclick="changeCategory('posts')">Posts</button>
            <button type="button" class="category-btn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" data-category="users" onclick="changeCategory('users')">Users</button>
            <button type="button" class="category-btn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" data-category="groups" onclick="changeCategory('groups')">Groups</button>
            </div>

            <!-- Search Results -->
            <section id="search-results" class="p-6 max-w-xl w-full bg-slate-500 rounded-xl shadow-lg">
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
