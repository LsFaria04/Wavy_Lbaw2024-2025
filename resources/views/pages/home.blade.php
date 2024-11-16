@extends('layouts.app')

@section('content')
    <header class="w-3/4 max-w-100 justify-self-center">
        <form>
            <input type="search" name="search" placeholder="Search">
        </form>
    </header>

    <section id="timeline">
        <!-- Check if there are posts -->
        @if($posts->isEmpty())
            <p>No posts found.</p>
        @else
            <!-- Loop through posts and display them -->
            @foreach($posts as $post)
                <div class="post">
                    <div class="post-header">
                        <h3>{{ $post->user->username }}</h3> <!-- Assuming user has a 'username' field -->
                        <span>{{ $post->createdDate }}</span> <!-- Assuming 'createdDate' is the correct column -->
                    </div>
                    <div class="post-body">
                        <p>{{ $post->message }}</p> <!-- Display the message of the post -->
                    </div>
                    <div class="post-footer">
                        <span>Visibility: {{ $post->visibilityPublic ? 'Public' : 'Private' }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </section>
@endsection
