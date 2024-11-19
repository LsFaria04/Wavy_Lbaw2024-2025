@extends('layouts.app')

@section('content')
<section class = "grow flex flex-col justify-center items-center" id = "login" >
    <header>
        <h1 class= "text-3xl font-bold">Login<h1>
    </header>
    <div class = "grow flex items-center justify-center">
        <form method="POST" action="{{ route('login') }}" class = "max-w-xl grid-start-1 bg-slate-100 shadow-md rounded px-8 pt-6 pb-8 my-4" >
            {{ csrf_field() }}

            <label class="font-medium text-lg" for="email">E-mail</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
           

            <label class="font-medium text-lg" for="password" >Password</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="password" type="password" name="password" required>
            

            <label>
                <input class = "my-4 w-4 h-4 rounded accent-sky-900" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>
            
            <div class = "flex flex-row justify-evenly mt-4">
                <a class="bg-sky-800 rounded w-32 py-2 text-center shadow text-white font-bold shadow-sky-900 hover:shadow-lg" href="{{ route('register') }}">Register</a>
                <button class = "bg-sky-800 rounded w-32 py-2  text-white font-bold shadow shadow-sky-900 hover:shadow-lg"type="submit">
                    Login
                </button>
            </div>
            <div class="mt-4">
            @if ($errors->has('email'))
                <p class= "text-red-900 font-extrabold">
                {{ $errors->first('email') }}
                </p>
            @endif
            @if ($errors->has('password'))
                <p class="text-red-900 font-extrabold">
                    {{ $errors->first('password') }}
                </p>
            @endif
            @if (session('success'))
                    <p class="text-green-900 font-extrabold">
                        {{ session('success') }}
                    </p>
                @endif
            </div>
        </form>
    </div>
</section>
@endsection