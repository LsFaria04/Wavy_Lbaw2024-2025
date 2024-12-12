@extends('layouts.app')
@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white" id="homePage">

        @if (session('error'))
            <div class = "absolute self-center alert w-full max-w-full p-4 mb-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 rounded-lg z-10">             
                {{ session('error')}}          
            </div>
        @elseif(session('success'))
            <div class = "absolute self-center alert w-full max-w-full p-4 mb-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 rounded-lg z-10">             
                {{ session('success')}}          
            </div>
        @endif 
        <section id="timeline" class="flex flex-col max-w-full w-full bg-white shadow-lg mx-auto">
            @if(Auth::check() && !Auth()->user()->isadmin)
                <div class="addPost p-4 bg-white border-b border-gray-300 w-full max-w-full">
                    <h1 class="text-xl font-bold text-black pb-2">{{ Auth::user()->username }}</h1>
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                        @csrf
                        <div class="flex items-start">
                            <div>
                                <!-- If we want a layout like twitter we can put the profile image here -->
                            </div>
                            
                            <div class="flex-1">
                                <textarea id="message" name="message" rows="2" class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none block" placeholder="What's on your mind?"></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 relative">
                                <label for="image" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                        <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <span>Attach Media (Optional)</span>
                                </label>

                                <input type="file" name="media[]" id="image" class="hidden" multiple onchange="updateFileList()">
                                
                                <ul id="fileDisplay" class="items-center gap-2 hidden">
                                    <!-- File names appended here by JavaScript -->
                                </ul>
                            </div>
                            <div class="flex items-center gap-2 relative">
                                <label for="topic" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                        <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <span>Topics (Optional)</span>
                                </label>

                                <button type = "button" class = "hidden" id="topic" onclick = "toggleAddPostTopics()"></button>
                                <input type="hidden" id="topicInput" name="topics[]" value="[]" multiple>
                                
                                <ul id="topicDisplay" class="items-center gap-2 hidden">
                                    <!-- topic names appended here by JavaScript -->
                                </ul>
                            </div>
                            
                            <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Post</button>
                        </div>       
                    </form>
                </div>

                <!-- Add Topic To post -->
                <div id="addTopics" class = "w-full h-full fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden z-20">
                    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                        <header class = "grid grid-cols-3 justify-center w-full max-w-full mb-4">
                            <button onclick = "toggleAddPostTopics()" class="col-start-1 col-span-1 justify-self-start">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <h2 class=" col-start-2  text-2xl self-center font-bold text-nowrap">Add a Topic</h2>  
                        </header>
                        <form onsubmit="searchPostTopics(event)">
                            <input id="topicsSearch" type="search" autocomplete="off"  name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Topics" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
                        </form>
                        <div id="postTopicsList" class="border-[1px] rounded border-gray-300 h-60 overflow-y-scroll mb-4">
                            <ul class = "topicList flex flex-col justify-center items-center"></ul>
                            <button onclick="loadMorePostTopics()" class= "flex w-full justify-center items-center">
                                <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <p>Show More</p>
                            <button>
                            </div>
                    </div>
                </div>

            @endif
            @if($posts->isEmpty())
                <p>No posts found.</p>
            @else
                @each('partials.post', $posts, 'post')
            @endif
        </section>
    </div>

@endsection
