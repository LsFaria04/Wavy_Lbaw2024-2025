@extends('layouts.app')

@auth
@section('content')
    <div class="w-full max-w-4xl mx-auto bg-white">
        <!-- Notifications header -->
        <header id="notifications-header" class="w-full p-4 shadow-md flex items-center sticky top-0 z-10 backdrop-blur bg-white">
            <h1 class="text-xl font-bold text-gray-800">Notifications</h1>
        </header>

        <div class="p-6">
            <!-- Tabs for different notification types -->
            <div class="tabs mb-6 flex space-x-6 text-sm font-medium">
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200">
                    All Notifications
                </button>
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200">
                    Comments
                </button>
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200 disabled">
                    Likes
                </button>
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200 disabled">
                    Follows
                </button>
            </div>

            @if($notifications->isEmpty())
                <div class="flex justify-center items-center h-32">
                    <p class="text-gray-600">No notifications available.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($notifications->filter(function($notification) {return isset($notification->comment);}) as $notification)  <!-- only gets comment notificiations --> 
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg shadow-sm">
                            <!-- Notification message and post link -->
                            <div class="flex-1">
                                @if(isset($notification->comment) && isset($notification->comment->post))
                                    <div class="text-sm font-semibold text-gray-800">
                                        {{ $notification->comment->user->username }} commented:
                                        <span class="italic text-gray-600">"{{ Str::limit($notification->comment->message, 50) }}"</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <a href="{{ route('posts.show', ['id' => $notification->comment->post->postid]) }}" class="text-blue-600 hover:underline">On post: "{{ Str::limit($notification->comment->post->message, 50) }}"</a>
                                    </div>
                                @else
                                    <div class="text-gray-600 text-sm">Post not available.</div>
                                @endif
                            </div>

                            <!-- Notification timestamp -->
                            <div class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($notification->date)->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
@endauth