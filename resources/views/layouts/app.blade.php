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
        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body>
        <main>
            <header>
                <h1><a href="{{ url('/home') }}">Wavy!</a></h1>
                <form>
                    <input type="search" name = "search bar" placeholder = "Search">
                </form>
            </header>
            @include('partials.navigation_menu')
            <section id="content">
                @yield('content')
            </section>
            @include('partials.context_menu')
            <footer>
                <p>Wavy</p>
                <ul>
                    <li><a href = "" >Help</a></li>
                    <li><a href = "" >About Us</a></li>
                    <li><a href = "" >Contacts</a></li>
                    <li><a href = ""> Features</a></li>
                </ul>
            </footer>
        </main>
    </body>
</html>