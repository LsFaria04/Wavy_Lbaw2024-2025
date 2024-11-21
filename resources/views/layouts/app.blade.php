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
       <link href="{{ url('css/app.css') }}" rel="stylesheet"> 
        @vite('resources/css/app.css')
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body class="flex flex-col min-h-screen">
        <main class = "flex flex-col grow lg:flex-row bg-white">
            @include('partials.navigation_menu')
            <section id="content" class= "grow flex flex-col pt-2">
                <header class="w-3/4 mb-6 self-center md:w-full md:max-w-xl">
                    <form action="{{ route('search') }}" method="GET" id="search-form" class="w-full max-w-3xl mx-auto">
                        <input type="text" name="q" value="{{ old('q', $query ?? '') }}" placeholder="Search..." class="border rounded p-2 w-full shadow-md focus:outline-0">
                        <input type="hidden" name="category" value="{{ old('category', $category ?? 'posts') }}">
                    </form>
                </header>
                @yield('content')
                @yield('scripts')
            </section>
            @include('partials.context_menu')
            
        </main>
        
    </body>
</html>