@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center w-full py-8 bg-gray-100">

    <header class="w-full max-w-5xl mb-8 px-4">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-6">Administration</h1>
        <nav class="flex justify-around bg-blue-600 text-white py-3 rounded-lg shadow-lg">
            <button class="tab-btn px-6 py-2 font-semibold rounded-lg hover:bg-blue-500 transition"
                onclick="showSectionAdmin('posts')">Manage Posts</button>
            <button class="tab-btn px-6 py-2 font-semibold rounded-lg hover:bg-blue-500 transition"
                onclick="showSectionAdmin('users')">Manage Users</button>
        </nav>
    </header>

    <!--Posts -->
    <section id="posts" class="admin-section tab-section max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Posts</h2>
        
        <div class="admin-tools mb-4 flex flex-wrap gap-4 items-center">
            <input type="text" placeholder="Search posts..." 
                class="input-field flex-grow p-2 border border-gray-300 rounded-lg" id="search-posts" />
        </div>

        <div id="posts-container">
            @include('../partials.admin.posts-table', ['posts' => $posts])
        </div>
    </section>

    <!--Users-->
    <section id="users" class="admin-section tab-section hidden max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Users</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <button id="createUserBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create User
            </button>
        </div>

        <div class="admin-tools mb-4 flex flex-wrap gap-4 items-center">
            <input type="text" placeholder="Search users..." 
                class="input-field flex-grow p-2 border border-gray-300 rounded-lg" id="search-users" />
        </div>
        <div id = "users-container">
            @include('../partials.admin.users-table', ['users' => $users])

        </div>
    </section>
</div>


<div id="createUserMenu" class="hidden fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <form id="createUserForm" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-gray-700 font-medium">Nome de Utilizador:</label>
                <input type="text" id="username" name="username"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="email" class="block text-gray-700 font-medium">Email:</label>
                <input type="email" id="email" name="email"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-medium">Password:</label>
                <input type="password" id="password" name="password"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <button type="button" id="cancelCreateUserBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </button>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create
            </button>
        </form>
    </div>
</div>
@endsection