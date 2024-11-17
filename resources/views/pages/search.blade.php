    @extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full">

            <!-- Form -->
            <header class="w-full max-w-xl mb-6">
                <form action="{{ route('search') }}" method="GET" id="search-form" class="w-full max-w-3xl mx-auto">
                    <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded p-2 w-full">
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
                @elseif($category == 'posts' && !$posts->isEmpty())
                    <h3 class="font-bold text-xl mb-4">Posts</h3>
                    @foreach($posts as $post)
                        <div class="post mb-4 p-4 bg-white rounded-md shadow-sm">
                            <div class="post-header mb-2">
                                <h3 class="font-bold">{{ $post->user->username }}</h3>
                                <span class="text-gray-500 text-sm">{{ $post->createddate }}</span>
                            </div>
                            <div class="post-body mb-2">
                                <p>{{ $post->message }}</p>
                            </div>
                        </div>
                    @endforeach
                @elseif($category == 'users' && !$users->isEmpty())
                    <h3 class="font-bold text-xl mb-4">Users</h3>
                    @foreach($users as $user)
                        <div class="user mb-4 p-4 bg-white rounded-md shadow-sm">
                            <div class="user-header mb-2">
                                <h3 class="font-bold">{{ $user->username }}</h3>
                            </div>
                            <div class="user-body mb-2">
                                <p>{{ $user->bio }}</p>
                            </div>
                        </div>
                    @endforeach
                @elseif($category == 'groups' && !$groups->isEmpty())
                    <h3 class="font-bold text-xl mb-4">Groups</h3>
                    @foreach($groups as $group)
                        <div class="group mb-4 p-4 bg-white rounded-md shadow-sm">
                            <div class="group-header mb-2">
                                <h3 class="font-bold">{{ $group->groupname }}</h3>
                            </div>
                            <div class="group-body mb-2">
                                <p>{{ $group->description }}</p>
                            </div>
                        </div>
                    @endforeach
                @elseif (!empty($query))
                    <p class="text-white">No results matched your search.</p>
                @endif
            </section>
        </div>

        @section('scripts')
            <script>
                
                function changeCategory(category) {
                    document.querySelector('input[name="category"]').value = category;
                    document.getElementById('search-form').submit();
                }
            </script>
        @endsection

    @endsection
