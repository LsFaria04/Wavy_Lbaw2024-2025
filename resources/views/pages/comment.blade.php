@extends('layouts.app')
@section('content')
    <section id="timeline" class="flex flex-col px-6 max-w-full w-full bg-white rounded-xl shadow-lg mx-auto">
        @include('partials.comment', ['comment' => $comment])
        @if(Auth::check() && !Auth()->user()->isadmin)
            <!-- Comment Creation Form -->
            <div class="addComment mb-6 p-4 bg-white rounded-xl shadow-md">
                <h1 class="text-xl font-bold text-black pb-2">{{ Auth::user()->username }}</h1>
                <form action="{{ route('comments.storeSubcomment') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                    @csrf
                    <!-- Hidden field to pass commentid -->
                    <input type="hidden" name="commentid" value="{{ $comment->commentid }}">

                    <div class="flex items-start">
                        <div>
                            <!-- Profile image can be placed here if needed -->
                        </div>
                        
                        <div class="flex-1">
                            <textarea id="message" name="message" rows="2" class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none block" placeholder="Write a comment..."></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 relative">
                            <label for="image" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                                    <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <span>Attach files (Optional)</span>
                            </label>

                            <input type="file" name="media[]" id="image" class="hidden" multiple onchange="updateFileList()">
                            
                            <ul id="fileDisplay" class="items-center gap-2 hidden">
                                <!-- File names appended here by JavaScript -->
                            </ul>
                        </div>
                        
                        <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Comment</button>
                    </div>       
                </form>
            </div>
        @endif
        <div class="comments mt-6">
            <h3 class="text-lg font-bold mb-4">Comments</h3>
            @forelse ($subComments as $comment)
                @include('partials.comment', ['comment' => $comment])
            @empty
                <p class="text-gray-500">No comments yet.</p>
            @endforelse
        </div>
    </section>
@endsection
