@extends('layouts.app')
@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white" id="homePage">

        <div class="fixed top-6 flex items-center z-50">
        @if (session('error'))
            <div class = "self-center alert w-full max-w-full p-4 bg-red-100 text-red-800 border shadow-md text-center border-red-300 z-10">             
                {{ session('error')}}          
            </div>
        @elseif(session('success'))
            <div class = "self-center alert w-full max-w-full p-4 bg-blue-100 text-blue-800 border shadow-md text-center border-blue-300 z-10">             
                {{ session('success')}}          
            </div>
        @endif 
        </div>
        <div id="messageContainer" class="fixed top-6 flex items-center z-50">
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
        @include('partials.reportForm')
        @include('partials.imageDetail')
    </div>

@endsection
