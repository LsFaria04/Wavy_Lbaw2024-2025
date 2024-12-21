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
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200" id="all-notifications-tab">
                    All Notifications
                </button>
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200" id="comments-tab">
                    Comments
                </button>
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200" id="likes-tab">
                    Likes
                </button>
                <button class="tab-btn px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200" id="follows-tab">
                    Follows
                </button>
            </div>

            <div id="notifications-content">
                <!-- All Notifications Section -->
                <div class="notifications-section" id="all-notifications-content">
                    @if($notifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No notifications available.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="flex-1">
                                        @if(isset($notification->comment) && isset($notification->comment->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->comment->user->username }} commented:
                                                <span class="italic text-gray-600">"{{ Str::limit($notification->comment->message, 50) }}"</span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->comment->post->postid]) }}" class="text-blue-600 hover:underline">
                                                    On post: "{{ Str::limit($notification->comment->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @elseif(isset($notification->like) && isset($notification->like->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->like->user->username }} liked your post
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->like->post->postid]) }}" class="text-blue-600 hover:underline">
                                                    "{{ Str::limit($notification->like->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @elseif(isset($notification->follow) && isset($notification->follow->follower))
                                            <div class="text-sm font-semibold text-gray-800">
                                            <a href="{{ route('profile', ['username' => $notification->follow->follower->username]) }}" 
                                                class="text-blue-600 hover:underline">
                                                {{ $notification->follow->follower->username }}
                                            </a>
                                                @if($notification->follow->state === \App\Models\Follow::STATE_PENDING)
                                                    requested to follow you
                                                @elseif($notification->follow->state === \App\Models\Follow::STATE_ACCEPTED)
                                                    started following you
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $notification->date->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Comments Section -->
                <div class="notifications-section hidden" id="comments-content">
                    @if($commentNotifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No comment notifications available.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($commentNotifications as $notification)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="flex-1">
                                        @if(isset($notification->comment) && isset($notification->comment->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->comment->user->username }} commented:
                                                <span class="italic text-gray-600">"{{ Str::limit($notification->comment->message, 50) }}"</span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->comment->post->postid]) }}" class="text-blue-600 hover:underline">
                                                    On post: "{{ Str::limit($notification->comment->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $notification->date->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Likes Section -->
                <div class="notifications-section hidden" id="likes-content">
                    @if($likeNotifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No like notifications yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($likeNotifications as $notification)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg shadow-sm">
                                    <div class="flex-1">
                                        @if(isset($notification->like) && isset($notification->like->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->like->user->username }} liked your post
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->like->post->postid]) }}" class="text-blue-600 hover:underline">
                                                    "{{ Str::limit($notification->like->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">
                                            {{ $notification->date->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                                                        
                <!-- Follows Section -->
                <div class="notifications-section hidden" id="follows-content">
                    @if($followNotifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No follow notifications yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($followNotifications as $notification)
                                @if(isset($notification->follow) && isset($notification->follow->follower))
                                    <div class="flex items-center p-4 bg-gray-50 rounded-lg shadow-sm">
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold text-gray-800">
                                                <a href="{{ route('profile', ['username' => $notification->follow->follower->username]) }}" 
                                                    class="text-blue-600 hover:underline">
                                                    {{ $notification->follow->follower->username }}
                                                </a>
                                                @if($notification->follow->state === \App\Models\Follow::STATE_PENDING)
                                                    requested to follow you
                                                @elseif($notification->follow->state === \App\Models\Follow::STATE_ACCEPTED)
                                                    started following you
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $notification->date->diffForHumans() }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@endauth
