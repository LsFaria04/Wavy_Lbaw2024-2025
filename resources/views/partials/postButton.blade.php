@auth
@if (!Auth()->user()->isadmin)
<!-- Modal Overlay and Form -->
<div id="modalContainer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-30">
    <div id="modalPostForm" class="addButtonPost p-4 bg-white border-b border-gray-300 w-full max-w-lg rounded-lg relative">
        <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-black">
            &times;
        </button>
        <h1 class="text-xl font-bold text-black pb-2">{{ Auth::user()->username }}</h1>
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf
            @isset($group)
                <input type="hidden" name="groupid" value="{{ $group->groupid }}">
            @endif
            <div class="flex items-start">
                <div class="flex-1">
                    <textarea id="message27" name="message" rows="2" class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none block" placeholder="What's on your mind?" required></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <div class = "flex flex-col gap-4">
                <div class="flex items-center sm:gap-2 relative">
                    <label for="imageButton" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-7 sm:h-7">
                            <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span class = "w-20 sm:w-full text-sm sm:text-base">Attach Media (Optional)</span>
                    </label>

                    <input type="file" name="media[]" id="imageButton" class="hidden" multiple onchange="updateFileButtonList()">
                    
                    <ul id="buttonFileDisplay" class="items-center gap-2 hidden">
                        <!-- File names appended here by JavaScript -->
                    </ul>
                </div>
                <div class="flex items-center sm:gap-2 relative">
                    <label for="topic-1" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-7 sm:h-7">
                            <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span class = "w-20 sm:w-full text-sm sm:text-base">Topics (Optional)</span>
                    </label>

                    <button type = "button" class = "hidden" id="topic-1" onclick = "toggleAddPostTopics(1, false)"></button>
                    <input type="hidden" id="topicInput-1" name="topics[]" value="[]">
                    
                    <ul id="topicDisplay-1" class="items-center gap-2 hidden">
                        <!-- topic names appended here by JavaScript -->
                    </ul>
                </div>
            </div>
                <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Post</button>
            </div>       
        </form>
    </div>
</div>
@endif
@endauth