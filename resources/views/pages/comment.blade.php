@extends('layouts.app')
@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white" id="commentPage">

        <!-- Flash Messages -->
        @if (session('error'))
            <div class="absolute self-center alert w-full max-w-full p-4 mb-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 rounded-lg z-10">             
                {{ session('error') }}          
            </div>
        @elseif(session('success'))
            <div class="absolute self-center alert w-full max-w-full p-4 mb-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 rounded-lg z-10">             
                {{ session('success') }}          
            </div>
        @endif 

        <!-- Comment Content -->
        <section id="commentContent" class="flex flex-col px-6 pt-6 max-w-full w-full bg-white rounded-xl shadow-lg mx-auto">
            @include('partials.comment', ['comment' => $comment])

            <!-- Add Sub-comment Section -->
            @if(Auth::check() && !Auth()->user()->isadmin)
                <div class="addSubcomment mt-8 mb-6 p-4 bg-gray-50 rounded-xl shadow-md border">
                    <form id="commentForm" action="{{ route('comments.storeSubcomment') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                        @csrf
                        <!-- Hidden field to pass commentid -->
                        <input type="hidden" name="commentid" value="{{ $comment->commentid }}">

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
                                    <span class="text-sm">Attach files</span>
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
                </div>
            @endif

            <!-- Existing Sub-comments Section -->
            <div class="subcomments mt-6">
                <h3 class="text-lg font-bold mb-4">Comments</h3>
                @forelse ($subComments as $subcomment)
                    @include('partials.comment', ['comment' => $subcomment])
                @empty
                    <p class="text-gray-500 mb-4">No comments yet.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
