@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center w-full">

        <header class="w-full max-w-xl mb-6">
            <h1 class="text-3xl font-bold text-center mb-4">Admin Page</h1>
            <nav class="flex justify-around bg-gray-800 text-white p-3 rounded-lg">
            <button class="tab-btn px-4 py-2 text-white rounded-md hover:bg-blue-600" onclick="showSectionAdmin('posts')">Manage Posts</button>
            <button class="tab-btn px-4 py-2 text-white rounded-md hover:bg-blue-600" onclick="showSectionAdmin('users')">Manage Users</button>
            </nav>
        </header>

        <!-- Posts Section -->
        <section id="posts" class="w-full max-w-3xl p-6 mb-6 bg-slate-500 rounded-xl shadow-lg tab-section">
            <h2 class="text-xl font-semibold mb-4 text-white">Manage Posts</h2>

            <!-- Search and Filter -->
            <div class="flex justify-between mb-4">
                <input type="text" placeholder="Search posts..." class="p-2 rounded-md" id="search-posts" />
                <div class="flex items-center space-x-2">
                    <select id="filter-posts" class="p-2 rounded-md">
                        <option value="">Filter by Visibility</option>
                        <option value="1">Public</option>
                        <option value="0">Private</option>
                    </select>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Search</button>
                </div>
            </div>

            <!-- Posts Table -->
            <table class="min-w-full text-white">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Post</th>
                        <th class="px-4 py-2">User</th>
                        <th class="px-4 py-2">Date Created</th>
                        <th class="px-4 py-2">Visibility</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="posts-list">
                    @foreach($posts as $post)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ Str::limit($post->message, 30) }}</td>
                            <td class="px-4 py-2">{{ $post->user->username }}</td>
                            <td class="px-4 py-2">{{ $post->createddate->format('d M Y') }}</td>
                            <td class="px-4 py-2">
                                <span class="{{ $post->visibilitypublic ? 'bg-green-500' : 'bg-red-500' }} text-white px-2 py-1 rounded-md">
                                    {{ $post->visibilitypublic ? 'Public' : 'Private' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-md" onclick="editPost({{ $post->postid }})">Edit</button>
                                <button class="bg-red-600 text-white px-4 py-2 rounded-md" onclick="deletePost({{ $post->postid }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <span>Showing 1 to 10 of {{ $posts->count() }} posts</span>
                <div class="space-x-2">
                    <button class="px-4 py-2 bg-gray-600 text-white rounded-md">Previous</button>
                    <button class="px-4 py-2 bg-gray-600 text-white rounded-md">Next</button>
                </div>
            </div>
        </section>

        <!-- Users Section -->
        <section id="users" class="w-full max-w-3xl p-6 mb-6 bg-slate-500 rounded-xl shadow-lg tab-section hidden">
            <h2 class="text-xl font-semibold mb-4 text-white">Manage Users</h2>

            <!-- Search and Filter -->
            <div class="flex justify-between mb-4">
                <input type="text" placeholder="Search users..." class="p-2 rounded-md" id="search-users" />
                <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Search</button>
            </div>

            <!-- Users Table -->
            <table class="min-w-full text-white">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-list">
                    @foreach($users as $user)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $user->username }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">
                                <span class="{{ $user->isadmin ? 'bg-blue-500' : 'bg-gray-500' }} text-white px-2 py-1 rounded-md">
                                    {{ $user->isadmin ? 'Admin' : 'User' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="{{ $user->state == 1 ? 'bg-green-500' : 'bg-red-500' }} text-white px-2 py-1 rounded-md">
                                    {{ $user->state == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-md" onclick="editUser({{ $user->userid }})">Edit</button>
                                <button class="bg-red-600 text-white px-4 py-2 rounded-md" onclick="deleteUser({{ $user->userid }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <span>Showing 1 to 10 of {{ $users->count() }} users</span>
                <div class="space-x-2">
                    <button class="px-4 py-2 bg-gray-600 text-white rounded-md">Previous</button>
                    <button class="px-4 py-2 bg-gray-600 text-white rounded-md">Next</button>
                </div>
            </div>
        </section>

    </div>
@endsection
