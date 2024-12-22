@extends('layouts.app')

@section('content')
<section class = "grow flex flex-col justify-center items-center" id = "login" >
    <header class = "py-12">
        <h2 class= "text-3xl font-bold">Login</h2>
    </header>
    <div id="messageContainer" class="fixed top-6 flex items-center z-40">
        <!--Used to append messages with JS -->
    </div>
    <div class="fixed top-6 flex items-center z-50">
    @if ($errors->has('email'))
            <div class = "self-center alert rounded max-w-full p-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 z-10">             
                {{ $errors->first('email') }}         
            </div>
        @elseif($errors->has('password'))
            <div class = "self-center alert max-w-full p-4 bg-blue-100 text-blue-800 border shadow-md text-center border-blue-300 z-10">             
                {{ $errors->first('password')}}          
            </div>
        @endif 
        @if (session('success'))
            <div class = "self-center alert max-w-full p-4 bg-blue-100 text-blue-800 border shadow-md text-center border-blue-300 z-10">
                        {{ session('success') }}
            </div>
        @endif
    </div>
    <div class = "grow flex items-center justify-center mt-[-180px]">
        <form method="POST" action="{{ route('login') }}" class = "max-w-xl grid-start-1 bg-slate-100 shadow-md rounded px-8 pt-6 pb-8 my-4" >
            @csrf

            <label class="font-medium text-lg" for="email">E-mail</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
           

            <label class="font-medium text-lg" for="password" >Password</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="password" type="password" name="password" required>
            

            <label>
                <input class = "my-4 w-4 h-4 rounded accent-sky-900" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>
            
            <div class = "flex flex-row justify-evenly my-4">
                <a class="bg-sky-800 rounded w-32 py-2 text-center shadow text-white font-bold shadow-sky-900 hover:shadow-lg" href="{{ route('register') }}">Register</a>
                <button class = "bg-sky-800 rounded w-32 py-2  text-white font-bold shadow shadow-sky-900 hover:shadow-lg" type="submit">
                    Login
                </button>
            </div>
            <a class="font-medium underline" href="{{route('forgotPassword')}}">Forgot password?</a>

        </form>
    </div>
</section>
@endsection