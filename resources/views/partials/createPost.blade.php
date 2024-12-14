<div class="addPost p-4 bg-white border-b border-gray-300 w-full max-w-full">
    <h1 class="text-xl font-bold text-black pb-2">{{ Auth::user()->username }}</h1>
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
        @csrf
        @isset($group)
            <input type="hidden" name="groupid" value="{{ $group->groupid }}">
        @endif
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
                <label for="topic-0" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                        <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span>Topics (Optional)</span>
                </label>

                <button type = "button" class = "hidden" id="topic-0" onclick = "toggleAddPostTopics(0, false)"></button>
                <input type="hidden" id="topicInput-0" name="topics[]" value="[]" multiple>
                
                <ul id="topicDisplay-0" class="items-center gap-2 hidden">
                    <!-- topic names appended here by JavaScript -->
                </ul>
            </div>
            
            <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Post</button>
        </div>       
    </form>
</div>

