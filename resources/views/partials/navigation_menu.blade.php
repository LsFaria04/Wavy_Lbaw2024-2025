<div id = "navigation-menu" class= "flex justify-items-center flex-col w-1/5 max-w-80 bg-sky-900 p-4 text-white">
    <header class =" flex items-center">
            <h1><a href="{{ url('/home') }}" >Wavy</a></h1>
    </header>
    <nav class = "grow  pt-40">
        <ul class = "flex flex-col gap-20">
            <li class = "test-white flex items-center cursor-pointer"><a href = "{{ route('home')}}">Home</a></li>
            <li class = "test-white flex items-center cursor-pointer"><a>Messages</a></li>
            <li class = "test-white flex items-center cursor-pointer"><a>Notifications</a></li>
            @if(Auth::check())
                <li class = "test-white flex items-center cursor-pointer"><a href = "{{ route('profile')}}">Profile</a></li>
            @else
                <li class = "test-white flex items-center cursor-pointer"><a href = "{{ route('login') }}">Login</a></li>
            @endif
        
        </ul>
    </nav>

</div>