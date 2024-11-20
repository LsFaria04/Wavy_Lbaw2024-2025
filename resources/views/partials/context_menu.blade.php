<aside class = "flex fixed lg:sticky top-0 right-0 w-0 bottom-12 max-h-screen transition-all ease-in duration-300" id = "context-menu">
    <button class = "absolute right-56 z-10 top-2 translate-x-56 bg-sky-950 shadow rounded-3xl p-2 transition-all ease-in duration-300 overflow-scroll rotate-180" onclick = "contextMenuOperation()">
        <svg class= "fill-white rotate-180" xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="25" height="25">
            <path d="M10.482,19.5a1.5,1.5,0,0,1-1.06-.439L4.836,14.475a3.505,3.505,0,0,1,0-4.95L9.422,4.939a1.5,1.5,0,0,1,2.121,2.122L6.957,11.646a.5.5,0,0,0,0,.708l4.586,4.585A1.5,1.5,0,0,1,10.482,19.5Z"/>
            <path d="M17.482,19.5a1.5,1.5,0,0,1-1.06-.439l-6-6a1.5,1.5,0,0,1,0-2.122l6-6a1.5,1.5,0,1,1,2.121,2.122L13.6,12l4.939,4.939A1.5,1.5,0,0,1,17.482,19.5Z"/>
        </svg>
    </button>
    <div class=" grow flex flex-col gap-10 items-center max-h-screen  bg-sky-900 p-4 text-white lg:bottom-0 overflow-y-scroll overflow-x-hidden text-nowrap">
    @if(Route::currentRouteName() == 'home')
        <div class = "w-full flex flex-col items-center px-5">
            <h3 class = "text-xl font-bold">My Groups</h3>
            <ul class = "pt-10 w-full flex flex-col gap-10 h-64 font-semibold text-center overflow-scroll">
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white hover:bg-sky-600 ">Mock group 1</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white hover:bg-sky-600 ">Mock group 2</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white hover:bg-sky-600 ">Mock group 3</li>
            </ul>
            <button class = "mt-10 w-full p-2 rounded font-semibold bg-gray-700  shadow hover:shadow-xl shadow-white hover:bg-gray-600 overflow-x-hidden">Create group</button>
        </div>

    @elseif(Route::currentRouteName() == 'profile')
        <div class = "w-full flex flex-col items-center px-5">
            <h3 class = "text-xl font-bold ">My Topics</h3>
            <ul class = "pt-10 w-full flex flex-col gap-10 h-64 font-semibold text-center overflow-scroll">
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock Topic 1</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock Topic 2</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock Topic 3</li>
            </ul>
        </div>
        <div class = "w-full flex flex-col items-center px-5">
            <h3 class = "text-xl font-bold">My Groups</h3>
            <ul class = "pt-10 w-full flex flex-col gap-10 h-64 font-semibold text-center overflow-scroll">
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock Group 1</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock Group 2</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock Group 3</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock Group 3</li>
            </ul>
            <button class = "mt-10 w-full p-2 rounded font-semibold bg-gray-700  shadow hover:shadow-xl shadow-white hover:bg-gray-600 overflow-x-hidden">Create group</button>
        </div>
    @else
        <div class = "w-full flex flex-col items-center px-5">
            <h3 class = "text-xl font-bold">Trending People</h3>
            <ul class = "pt-10 w-full flex flex-col gap-10 h-64 font-semibold text-center overflow-scroll">
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock user 1</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock user 2</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock user 3</li>
            </ul>
        </div>
        <div class = "w-full flex flex-col items-center px-5">
            <h3 class = "text-xl font-bold">Trending Topics</h3>
            <ul class = "pt-10 w-full flex flex-col gap-10 h-64 font-semibold text-center overflow-scroll">
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock topic 1</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock topic 2</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock topic 3</li>
                <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white">Mock topic 3</li>
            </ul>
            <button class = "mt-10 w-full p-2 rounded font-semibold bg-gray-700  shadow hover:shadow-xl shadow-white hover:bg-gray-600 overflow-x-hidden">Create group</button>
        </div>
    @endif
    </div>
</aside>

