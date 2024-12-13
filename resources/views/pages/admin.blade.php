@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center w-full py-8 bg-gray-100">

    <header class="w-full max-w-5xl mb-8 px-4">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-6">Administration</h1>
        <nav class="flex justify-around bg-blue-600 text-white py-3 rounded-lg shadow-lg">
            <button class="px-6 py-2 font-semibold rounded-lg hover:bg-blue-500 transition"
                onclick="showSectionAdmin('users')">Manage Users</button>
            <button class="tab-btn px-6 py-2 font-semibold rounded-lg hover:bg-blue-500 transition"
                onclick="showSectionAdmin('topics')">Manage Topics</button>
        </nav>
    </header>

    @if (session('success'))
            <div class="absolute self-center alert w-full max-w-full p-4 mb-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 rounded-lg z-10">
                {{ session('success') }}
            </div>
    @endif

    @if (session('error'))
            <div class="absolute self-center alert w-full max-w-full p-4 mb-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 rounded-lg z-10">
                {{ session('error') }}
            </div>
    @endif
    <!--
    <section id="posts" class="admin-section tab-section max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Posts</h2>
        
        <div class="admin-tools mb-4 flex flex-wrap gap-4 items-center">
            <input type="text" placeholder="Search posts..." 
                class="input-field flex-grow p-2 border border-gray-300 rounded-lg" id="search-posts" />
        </div>

        <div id="posts-container">
            
        </div>
    </section>
-->
    <section id="users" class="admin-section tab-section max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Users</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <button id="createUserBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create User
            </button>
        </div>
<!--
        <div class="admin-tools mb-4 flex flex-wrap gap-4 items-center">
            <input type="text" placeholder="Search users..." 
                class="input-field flex-grow p-2 border border-gray-300 rounded-lg" id="search-users" />
        </div>
        <div id = "users-container">
            

        </div> 
-->
    </section> 

    <section id="topics" class="hidden admin-section tab-section max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Topics</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <button id="createTopicBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Topic
            </button>
        </div>
        <table id="topics-table">

        </table>
    </section>
</div>


<div id="createUserMenu" class="hidden fixed inset-0 items-center justify-center z-50 bg-gray-900 bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <form action = "{{route('admin.users.create')}}"id="createUserForm" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-gray-700 font-medium">Username:</label>
                <input type="text" id="create-username" name="username"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="email" class="block text-gray-700 font-medium">Email:</label>
                <input type="email" id="create-email" name="email"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-medium">Password:</label>
                <input type="password" id="create-password" name="password"
                    class="w-full border border-gray-300 rounded p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password:</label>
                <input type="password" id="create-password_confirmation" name="password_confirmation"
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