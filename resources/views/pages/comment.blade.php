@extends('layouts.app')
@section('content')
    <section id="timeline" class="flex flex-col px-6 max-w-full w-full bg-white rounded-xl shadow-lg mx-auto">
        @include('partials.comment', ['comment' => $comment])
        <div class="comments mt-6">
            <h3 class="text-lg font-bold mb-4">Comments</h3>
            @forelse ($subComments as $comment)
                @include('partials.comment', ['comment' => $comment])
            @empty
                <p class="text-gray-500">No comments yet.</p>
            @endforelse
        </div>
    </section>
@endsection
