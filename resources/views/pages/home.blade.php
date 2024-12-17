@extends('layouts.app')
@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white" id="homePage">

        @if (session('error'))
            <div class = "absolute self-center alert w-full max-w-full p-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 z-10">             
                {{ session('error')}}          
            </div>
        @elseif(session('success'))
            <div class = "absolute self-center alert w-full max-w-full p-4 bg-green-100 text-green-800 border shadow-md text-center border-green-300 z-10">             
                {{ session('success')}}          
            </div>
        @endif 
        <div id="messageContainer" class="fixed top-6 flex items-center">
            <!--Used to append messages with JS -->
        </div>
        
        <section id="timeline" class="flex flex-col max-w-full w-full bg-white shadow-lg mx-auto">
            @if(Auth::check() && !Auth()->user()->isadmin)
               @include('partials.createPost')
            @endif
            @if($posts->isEmpty())
                <p>No posts found.</p>
            @else
                @each('partials.post', $posts, 'post')
            @endif
        </section>
        @include('partials.addPostTopics')
        @include('partials.reportForm')
    </div>

@endsection
