<nav>
    <ul>
        <li><a>Home</a></li>
        <li><a>Messages</a></li>
        <li><a>Notifications</a></li>
        @if(Auth::check())
            <li><a>Profile</a></li>
        @else
            <li><a href = "{{ url('/login') }}">Login</a></li>
        @endif
       
    </ul>
</nav>