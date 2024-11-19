<aside class= "flex flex-col gap-10 items-center w-14 h-14 max-h-screen bottom-12 bg-sky-900 p-4 text-white fixed top-0 right-0 lg:bottom-0 overflow-scroll transition-all ease-in duration-300 " id = "context-menu">
    <button class = "self-start" onclick = "contextMenuOperation()">
        <svg  xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="25" height="25">
            <path d="M10.482,19.5a1.5,1.5,0,0,1-1.06-.439L4.836,14.475a3.505,3.505,0,0,1,0-4.95L9.422,4.939a1.5,1.5,0,0,1,2.121,2.122L6.957,11.646a.5.5,0,0,0,0,.708l4.586,4.585A1.5,1.5,0,0,1,10.482,19.5Z" fill = "currentColor"/>
            <path d="M17.482,19.5a1.5,1.5,0,0,1-1.06-.439l-6-6a1.5,1.5,0,0,1,0-2.122l6-6a1.5,1.5,0,1,1,2.121,2.122L13.6,12l4.939,4.939A1.5,1.5,0,0,1,17.482,19.5Z" fill = "currentColor"/>
        </svg>
    </button>
    @if(Route::currentRouteName() == 'home')
    <div class = "w-full flex flex-col items-center px-5">
        <h3 class = "text-xl font-bold">My Groups</h3>
        <ul class = "pt-10 w-full flex flex-col gap-10 h-64 font-semibold text-center overflow-scroll">
            <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white hover:bg-sky-600 ">Mock group 1</li>
            <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white hover:bg-sky-600 ">Mock group 2</li>
            <li class = "bg-sky-700 rounded p-2 cursor-pointer shadow hover:shadow-xl shadow-white hover:bg-sky-600 ">Mock group 3</li>
        </ul>
        <button class = "mt-10 w-full p-2 rounded font-semibold bg-gray-700  shadow hover:shadow-xl shadow-white hover:bg-gray-600">Create group</button>
    </div>

    @elseif(Route::currentRouteName() == 'profile')
    <div class = "w-full flex flex-col items-center px-5">
        <h3 class = "text-xl font-bold">My Topics</h3>
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
        <button class = "mt-10 w-full p-2 rounded font-semibold bg-gray-700  shadow hover:shadow-xl shadow-white hover:bg-gray-600">Create group</button>
    </div>
    @endif
</aside>

