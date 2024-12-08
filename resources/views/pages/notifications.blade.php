@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center w-full max-w-full bg-white">
        <header id="notifications-header" class="w-full max-w-full p-4 shadow-md flex items-center sticky top-0 z-10 backdrop-blur">
            <h1 class="text-xl font-bold text-gray-800">Notifications</h1>
        </header>

        <div class="w-full max-w-full p-6">
            @if($notifications->isEmpty())
                <div class="flex justify-center items-center h-32">
                    <p class="text-gray-600 text-center">Nenhuma notificação disponível.</p>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach($notifications as $notification)
                        <li>
                            @if(isset($notification->post_id))
                                <a href="{{ route('posts.show', ['id' => $notification->post_id]) }}">
                                    {{ $notification->message }}
                                </a>
                            @else
                                <span>Post not available</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
