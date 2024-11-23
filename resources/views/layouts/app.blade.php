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
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src="{{ url('js/app.js') }}" defer></script>
    </head>
    <body class="flex flex-col min-h-screen overscroll-none overflow-x-hidden">
        <main class = "flex flex-col grow lg:flex-row bg-white ">
            @include('partials.navigation_menu')
            <section id="content" class= "grow flex flex-col mb-16 lg:mb-0">
                @yield('content')
                @yield('scripts')
            </section>
            @include('partials.search_menu')
            
        </main>
        
    </body>
</html>