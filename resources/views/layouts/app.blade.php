<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        @vite('resources/css/app.css')
        @vite('resources/js/app.js')
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src="{{ url('js/app.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/admin.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/comment.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/group.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/home.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/posts.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/profile.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/search.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/groupList.js') }}" defer></script>
        <script type="text/javascript" src="{{ url('js/notifications.js') }}" defer></script>
        <script src="https://js.pusher.com/7.0/pusher.min.js" defer></script>

    </head>
    <body class="flex flex-col min-h-screen overscroll-none overflow-x-hidden">
        <main class = "flex flex-col grow lg:flex-row bg-white ">
            @include('partials.navigation_menu')
            @include('partials.addPostTopics')
            <section id="content" class= "grow flex flex-col mb-16 lg:mb-0 lg:ml-52">
                @yield('content')
                @yield('scripts')
            </section>
        </main>   
    </body>
</html>