<div id = "search-menu" class= "lg:flex lg:flex-col lg:w-60 lg:max-h-screen bg-sky-900 p-4 text-white text-nowrap fixed lg:top-0 lg:sticky transition-w ease-in duration-300">
    <header class = "flex items-center justify-between overflow-hidden">
        <form id="search-bar" action="{{ route('search') }}" method="GET" class="w-full mx-auto">
            <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded-3xl py-1 pl-5 w-full bg-white text-black shadow-md focus:outline-none">
        </form>
    </header>

    <nav class = "grow lg:pt-14">
        <ul class="flex justify-center flex-row lg:flex-col gap-14 sm:gap-16 overflow-scroll">
        </ul>
    </nav>
</div>

<div id="search-ball" 
    class="lg:hidden fixed top-1.5 right-4 w-12 h-12 bg-sky-900 rounded-full flex items-center justify-center shadow-md z-20 bg-opacity-90">
    <a href="{{ route('search') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="white" stroke="white" stroke-width="0.5">
            <path d="M18,10a8,8,0,1,0-3.1,6.31l6.4,6.4,1.41-1.41-6.4-6.4A8,8,0,0,0,18,10Zm-8,6a6,6,0,1,1,6-6A6,6,0,0,1,10,16Z"/>
        </svg>
    </a>
</div>
