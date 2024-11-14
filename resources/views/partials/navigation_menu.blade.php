<div id = "navigation-menu">
    <header>
            <h1><a href="{{ url('/home') }}">Wavy</a></h1>
    </header>
    <nav>
        <ul>
            <li><a href = "{{ route('home')}}">Home</a></li>
            <li><a>Messages</a></li>
            <li><a>Notifications</a></li>
            @if(Auth::check())
                <li><a href = "{{ route('profile')}}">Profile</a></li>
            @else
                <li><a href = "{{ route('login') }}">Login</a></li>
            @endif
        
        </ul>
    </nav>
</div>