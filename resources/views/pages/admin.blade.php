@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center w-full py-8 bg-gray-100">

    <header class="w-full max-w-5xl mb-8 px-4">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-6">Administration</h1>
        <nav class="flex justify-around bg-blue-600 text-white py-3 rounded-lg shadow-lg">
            <button class="px-6 py-2 font-semibold rounded-lg hover:bg-blue-500 transition"
                onclick="showSectionAdmin('users')">Users</button>
            <button class="px-6 py-2 font-semibold rounded-lg hover:bg-blue-500 transition"
                onclick="showSectionAdmin('topics')">Topics</button>
            <button class="px-6 py-2 font-semibold rounded-lg hover:bg-blue-500 transition"
                onclick="showSectionAdmin('reports')">Reports</button>
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
    <div id="messageContainer" class="fixed top-6 z-50 flex items-center">
        <!--Used to append messages with JS -->
    </div>

    <section id="users" class="admin-section flex flex-col tab-section max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Users</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <button id="createUserBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create User
            </button>
        </div>
        <table id="users-table" class = "flex flex-col self-center items-center w-full my-4">
            <!-- filled with JS -->
        </table>
    </section> 
    <section id="topics" class="hidden flex-col admin-section tab-section max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Topics</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <button id="createTopicBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Topic
            </button>
            <form class= "m-0" onsubmit="searchAdmin(event,'topics')">
                <input id="topicsAdminSearch" type="search" autocomplete="off"  name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Topics" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
            </form>
        </div>
        <table id="topics-table" class = "flex flex-col self-center items-center w-full my-4">
            <!-- filled with JS -->
        </table>
    </section>
    <section id="reports" class="hidden flex-col admin-section tab-section max-w-5xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Reports</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <form class= "m-0" onsubmit="searchAdmin(event,'reports')">
                <input id="reportsAdminSearch" type="search" autocomplete="off"  name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Reports" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
            </form>
        </div>
        <table id="reports-table" class = "table-auto self-center w-full my-4">
            <!-- filled with JS -->
        </table>
    </section>
</div>

@include("partials.admin.deleteMenu")
@include("partials.admin.createUserMenu")
@include("partials.admin.createTopicMenu")

@endsection