<div id="follows" class = "w-full h-full fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden z-20">
    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
        <header class = "grid grid-cols-3 justify-center w-full max-w-full mb-4">
            <button onclick = "toggleFollowList()" class="col-start-1 col-span-1 justify-self-start">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h2 class=" col-start-2  text-2xl self-center font-bold text-nowrap">My Follows</h2>  
        </header>
        <div id="followsList" class="border-[1px] rounded border-gray-300 h-60 overflow-y-scroll mb-4">
            <ul class = "followsList flex flex-col justify-center items-center"></ul>
        </div>
    </div>
</div>