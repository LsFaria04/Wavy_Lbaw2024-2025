<!-- Add Topic To post -->
<div id="addPostTopics" class = "w-full h-full fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden z-40">
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
            <input id="topicsPostSearch" type="search" autocomplete="off"  name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search Topics" class="border rounded-3xl p-2.5 pl-5 w-full my-2 focus:outline-none border-gray-300">
        </form>
        <div id="postTopicsList" class="border-[1px] rounded border-gray-300 h-60 overflow-y-scroll mb-4">
            <ul class = "topicList flex flex-col justify-center items-center"></ul>
            <button onclick="loadMorePostTopics()" class= "flex w-full justify-center items-center">
                <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Show More</span>
            </button>
        </div>
    </div>
</div>