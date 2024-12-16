@extends('layouts.app')
@section('content')
<section class = "grow flex flex-col justify-center items-center" id = "recoverPassword" >
    <header>
        <h1 class= "text-3xl font-bold">Password recovery<h1>
    </header>
    <div id="messageContainer" class="flex items-center mt-6">
        <!--Used to append messages with JS -->
    </div>
    <div id="recoveryContainer" class = "grow flex items-center justify-center">

        <!-- Email form-->
        <form id = "recoveryEmail" class = "flex flex-col max-w-xl grid-start-1 bg-slate-100 shadow-md rounded px-8 pt-6 pb-8 my-4" >
            <p class="mb-4">Insert your email account to receive a verification token</p>
            <label class="font-medium text-lg" for="email">E-mail</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            <button onclick ="passwordRecovery()" type="button" class="self-center bg-sky-800 rounded w-32 py-2  text-white font-bold shadow shadow-sky-900 hover:shadow-lg">Send</button>
        </form>

        <!-- Token form -->
        <form id = "recoveryToken" class = "hidden flex-col max-w-xl grid-start-1 bg-slate-100 shadow-md rounded px-8 pt-6 pb-8 my-4" >
            <p class="mb-4">Please insert the token that was sent to you by email and the new password</p>
            
            <label class="font-medium text-lg" for="email">Token</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="token" type="text" name="token" required autofocus>
            
            <label class="font-medium text-lg">Password</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="password" type="password" name="password" required>
    
            <label class="font-medium text-lg">Confirm Password</label>
            <input class="shadow appearance-none border rounded w-full my-4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline shadow-sky-900" id="password-confirm" type="password" name="password_confirmation" required>

            <button onclick = "tokenCheck()" type="button" class="self-center bg-sky-800 rounded w-32 py-2  text-white font-bold shadow shadow-sky-900 hover:shadow-lg">Send</button>
        </form>

    </div>
</section>
@endsection