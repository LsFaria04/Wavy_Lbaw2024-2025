@extends('layouts.app')

@section('content')
<section class = "grow flex flex-col justify-center items-center " id = "register">
  <header class = "py-12">
        <h1 class= "text-3xl font-bold">Register<h1>
  </header>
    @if ($errors->has('email'))
    <div class = "mt-8 self-center alert rounded max-w-full p-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 z-10">             
        {{ $errors->first('email') }}         
    </div>
  @elseif($errors->has('password'))
    <div class = "mt-8 self-center alert max-w-full p-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 z-10">             
        {{ $errors->first('password')}}          
    </div>
  @endif 
  @if (session('success'))
    <div class = "mt-8 self-center alert max-w-full p-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 z-10">
                {{ session('success') }}
    </div>
    @endif
  <div class = "grow flex items-center justify-center">
    <form method="POST" action="{{ route('register') }}" class = "max-w-xl grid-start-1 bg-slate-100 shadow-md rounded px-8 pt-6 pb-8 my-4" >
        {{ csrf_field() }}

        <label class="font-medium text-lg">Name</label>
        <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>

        <label class="font-medium text-lg">E-Mail Address</label>
        <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="email" type="email" name="email" value="{{ old('email') }}" required>

        <label class="font-medium text-lg">Password</label>
        <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="password" type="password" name="password" required>

        <label class="font-medium text-lg">Confirm Password</label>
        <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="password-confirm" type="password" name="password_confirmation" required>

        <div class = "flex flex-row justify-evenly mt-4">
          <button  class = "bg-sky-800 rounded w-32 py-2  text-white font-bold shadow shadow-sky-900 hover:shadow-lg" type="submit">
            Register
          </button>
          <a  class = "bg-sky-800 rounded w-32 py-2  text-white text-center font-bold shadow shadow-sky-900 hover:shadow-lg" href="{{ route('login') }}">Login</a>
        </div>

    </form>
    
  </div>
</section>
@endsection