@extends('layouts.app')
@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white" id="postPage">

        <!-- Flash Messages -->
        <div class="fixed top-6 flex items-center z-50">
            @if (session('error'))
                <div class = "self-center alert w-full max-w-full p-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 z-10">             
                    {{ session('error')}}          
                </div>
            @elseif(session('success'))
                <div class = "self-center alert w-full max-w-full p-4 bg-blue-100 text-blue-800 border shadow-md text-center border-blue-300 z-10">             
                    {{ session('success')}}          
                </div>
            @endif 
        </div>
        <div id="messageContainer" class="fixed top-6 flex items-center">
            <!--Used to append messages with JS -->
        </div>

        <!-- Post Content -->
        <section id="postContent" class="flex flex-col max-w-full w-full bg-white mx-auto">
            @include('partials.post', ['post' => $post])

            <input type="hidden" name="postidForJs" value="{{ $post->postid }}">

            <!-- Add Comment Section -->
                <div class="addComment p-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold mb-4">Comments</h3>
                    @if(Auth::check() && !Auth()->user()->isadmin)
                    <form id="commentForm" action="{{ route('comments.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                        @csrf
                        <input type="hidden" name="postid" value="{{ $post->postid }}">

                        <!-- Text Area -->
                        <textarea id="message" name="message" rows="3" 
                                  class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none resize-none placeholder-gray-400 text-gray-700 text-sm"
                                  placeholder="Write your comment here..."></textarea>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <!-- Attach Media -->
                            <div class="flex items-center gap-2">
                                <label for="image" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                                        <path d="M19.828 11.244L12.707 18.364C10.755 20.317 7.589 20.317 5.636 18.364C3.684 16.411 3.684 13.246 5.636 11.293L12.472 4.458C13.774 3.156 15.884 3.156 17.186 4.458C18.488 5.759 18.488 7.87 17.186 9.172L10.361 15.996C9.71 16.647 8.655 16.647 8.004 15.996C7.353 15.345 7.353 14.29 8.004 13.639L14.226 7.418" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <span class="w-20 sm:w-full text-sm">Attach Media</span>
                                </label>
                                <input type="file" name="media[]" id="image" class="hidden" multiple onchange="updateFileList()">
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="px-6 py-2 bg-sky-700 text-white font-semibold rounded-xl hover:bg-sky-800 text-sm">
                                Comment
                            </button>
                        </div>

                        <!-- File Display -->
                        <ul id="fileDisplay" class="text-sm text-gray-500 mt-2 hidden">
                            <!-- File names appended dynamically -->
                        </ul>
                    </form>
                    @endif
                </div>
            <!-- Existing Comments Section -->
            <section id="comments">
                @forelse ($post->comments as $comment)
                    @include('partials.comment', ['comment' => $comment])
                @empty
                    <p class="text-gray-500 mb-4">No comments yet.</p>
                @endforelse
            </div>
        </section>
        @include('partials.addPostTopics')
        @include('partials.reportForm')
        @include('partials.imageDetail')
    </div>
@endsection
