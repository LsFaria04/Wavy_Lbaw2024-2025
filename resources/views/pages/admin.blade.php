@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center w-full bg-white">

    <header class="w-full  mb-2 mt-8 shadow">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-6">Administration</h1>
        <nav class="flex w-full justify-around mt-4">
            <button class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900 border-sky-900 text-sky-900"
                onclick="showSectionAdmin('users')">Users</button>
            <button class=" tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900"
                onclick="showSectionAdmin('topics')">Topics</button>
            <button class=" tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900"
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

    <section id="users" class="admin-section flex flex-col tab-section w-full bg-white p-6">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Users</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <form class= "m-0" onsubmit="searchAdmin(event,'users')">
                <input id="usersAdminSearch" type="search" autocomplete="off"  name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Users" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
            </form>
            <button id="createUserBtn" class="px-4 py-2 w-30 bg-sky-700 text-white font-semibold rounded-2xl hover:bg-sky-800">
                Create User
            </button>
        </div>
        <table id="users-table" class = "table-auto self-center items-center w-full my-4">
            <tr class = "shadow font-medium">
                <th class = "w-1/2 text-start px-4 py-2" >Username</th>
                <th class = "w-1/2 text-start px-4 py-2" >State</th>
                <th></th>
            </tr>
            @foreach ($users as $user )
                <tr id = 'User-{{$user->userid}}' class = "shadow font-medium">
                    <td class="w-1/2 px-4 py-2 text-gray-700">
                        <a class = "max-w-20 sm:max-w-40 truncate ..." href = '/profile/{{$user->username}}'>
                          {{$user->username}}
                        </a>
                    </td>
                    <td  class="userState w-1/2 max-w-40 px-4 py-2 text-gray-700">{{$user->state}}</td>
                    <td class="px-4 py-2 self-end flex flex-row justify-between items-center">
                        <button onclick = "showBanAdminMenu({{$user->userid}})" class="banButton text-center w-16 px-1 py-1 bg-slate-100 hover:bg-slate-200  rounded-2xl focus:outline-none">{{$user->state === 'active' ? "Ban" : "Unban"}}</button>
                        <form action="../profile/{{$user->userid}}/delete" method="POST" id="deleteForm-{{$user->userid}}" class = "flex items-center">
                            @csrf
                            <button type="button" onclick="showDeleteAdminMenu({{$user->userid}}, 'users')" class="text-red-500 hover:text-red-700 ml-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </td>

                </tr>
                
            @endforeach
            <!-- filled with JS -->
        </table>
        @if($users->hasMorePages())
                <button class = "flex w-full justify-center items-center" onclick = "loadMoreAdminContent('users')" id = 'showMore'>
                    <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <p>Show More</p>
                </button>
        @endif
    </section> 
    <section id="topics" class="hidden flex-col admin-section tab-section w-full bg-white p-6">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Manage Topics</h2>
        
        <div class="mb-4 flex justify-between items-center">
            <form class= "m-0" onsubmit="searchAdmin(event,'topics')">
                <input id="topicsAdminSearch" type="search" autocomplete="off"  name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Topics" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
            </form>
            <button id="createTopicBtn" class="px-4 py-2 w-30 bg-sky-700 text-white font-semibold rounded-2xl hover:bg-sky-800">
                Create Topic
            </button>
            
        </div>
        <table id="topics-table" class = "flex flex-col self-center items-center w-full my-4">
            <!-- filled with JS -->
        </table>
    </section>
    <section id="reports" class="hidden flex-col admin-section tab-section w-full bg-white p-6">
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
@include("partials.admin.reasonDetails")
@include("partials.admin.banMenu")

@endsection