<div id = "imageDetail" class = "max-w-screen w-full h-full fixed inset-0 bg-black bg-opacity-50  items-center justify-center hidden z-20">
    <div class = "flex flex-col items-center justify-center bg-white w-full lg:w-[500px] lg:h-[500px] max-w-screen max-h-screen rounded-xl">
        <header class = " w-full max-w-full">
            <button onclick = "toggleImageDetails()" class="ml-4 mt-4">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </header>
        <div class = "p-4 w-[500px] max-w-full h-[450px] max-h-full overflow-hidden rounded-lg flex items-center justify-center">
            <img id = "detailImg" src ="" alt = "postImageDetail" class = "min-w-[200px] max-w-full max-h-full rounded-xl">
        </div>
    </div>
</div>