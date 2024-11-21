<div id = "search-menu" class= "hidden lg:flex lg:flex-col lg:w-14 lg:max-h-screen bg-sky-900 p-4 text-white text-nowrap fixed lg:top-0 lg:sticky transition-w ease-in duration-300">
    <header class = "flex items-center justify-between overflow-hidden">
            <button class="mr-2 rotate-180" onclick = "searchMenuOperation()">
                <svg class= "fill-white rotate-180" xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="25" height="25">
                    <path d="M10.482,19.5a1.5,1.5,0,0,1-1.06-.439L4.836,14.475a3.505,3.505,0,0,1,0-4.95L9.422,4.939a1.5,1.5,0,0,1,2.121,2.122L6.957,11.646a.5.5,0,0,0,0,.708l4.586,4.585A1.5,1.5,0,0,1,10.482,19.5Z"/>
                    <path d="M17.482,19.5a1.5,1.5,0,0,1-1.06-.439l-6-6a1.5,1.5,0,0,1,0-2.122l6-6a1.5,1.5,0,1,1,2.121,2.122L13.6,12l4.939,4.939A1.5,1.5,0,0,1,17.482,19.5Z"/>
                </svg>
            </button>

            <form id="search-bar" action="{{ route('search') }}" method="GET" class="hidden w-full mx-auto">
                <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded p-2 w-full bg-white text-black shadow-md focus:outline-none">
                <input type="hidden" name="category" value="{{ old('category', $category ?? 'posts') }}">
            </form>
    </header>

    <nav class = "grow lg:pt-20">
        <ul class="flex justify-center flex-row lg:flex-col gap-14 sm:gap-16 overflow-scroll">
            <li id="search-icon" class="items-center justify-center ">
                <a class = "flex flex-row items-center gap-3" href = "{{ route('search')}}">
                    <svg class="min-w-[20px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="white" stroke="white" stroke-width="0.5">
                        <path d="M18,10a8,8,0,1,0-3.1,6.31l6.4,6.4,1.41-1.41-6.4-6.4A8,8,0,0,0,18,10Zm-8,6a6,6,0,1,1,6-6A6,6,0,0,1,10,16Z"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</div>

<div id="search-ball" 
    class="lg:hidden fixed top-4 right-4 w-12 h-12 bg-sky-900 rounded-full flex items-center justify-center shadow-md">
    <a href="{{ route('search') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="white" stroke="white" stroke-width="0.5">
            <path d="M18,10a8,8,0,1,0-3.1,6.31l6.4,6.4,1.41-1.41-6.4-6.4A8,8,0,0,0,18,10Zm-8,6a6,6,0,1,1,6-6A6,6,0,0,1,10,16Z"/>
        </svg>
    </a>
</div>
