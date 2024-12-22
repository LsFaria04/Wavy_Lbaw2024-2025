@extends('layouts.app')

@auth
@section('content')
<div class="flex flex-col items-center w-full max-w-full bg-white">
    <!-- Notifications header -->
    <header id="notifications-header" class="w-full max-w-full pt-4 shadow-md sticky top-0 z-10 backdrop-blur">
        <div class="flex justify-center items-center">
            <h2 class="text-xl font-bold text-gray-800">Notifications</h2>
        </div>

        <!-- Tabs for different notification types -->
        <nav class="tabs flex justify-around mt-4">
            <button type="button" id="all-notifications-tab" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900  border-sky-900 text-sky-900">
                All Notifications
            </button>
            <button type="button" id="comments-tab" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">
                Comments
            </button>
            <button type="button" id="likes-tab" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">
                Likes
            </button>
            <button type="button" id="follows-tab" class="tab-btn flex-1 text-center py-3 text-sm font-semibold border-b-2 hover:text-sky-900">
                Follows
            </button>
        </nav>
    </header>

            <div id="notifications-content" class = "flex flex-col w-full max-w-full">
                <!-- All Notifications Section -->
                <div class="notifications-section w-full" id="all-notifications-content">
                    @if($notifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No notifications available.</p>
                        </div>
                    @else
                        <div>
                            @foreach($notifications as $notification)
                                <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border-b border-gray-300">
                                    <div class="flex-1">
                                        @if(isset($notification->comment) && isset($notification->comment->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->comment->user->username }} 
                                                @if($notification->comment->parentcommentid)
                                                    replied to a comment:
                                                @else
                                                    commented:
                                                @endif
                                                <span class="italic text-gray-600">"{{ Str::limit($notification->comment->message, 50) }}"</span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->comment->post->postid]) }}" class="text-sky-600 hover:underline">
                                                    On post: "{{ Str::limit($notification->comment->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @elseif(isset($notification->like) && isset($notification->like->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->like->user->username }} liked your post
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->like->post->postid]) }}" class="text-sky-600 hover:underline">
                                                    "{{ Str::limit($notification->like->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @elseif(isset($notification->follow) && isset($notification->follow->follower))
                                            <div class="text-sm font-semibold text-gray-800">
                                            <a href="{{ route('profile', ['username' => $notification->follow->follower->username]) }}" 
                                                class="text-sky-600 hover:underline">
                                                {{ $notification->follow->follower->username }}
                                            </a>
                                                @if($notification->follow->state === \App\Models\Follow::STATE_PENDING)
                                                    requested to follow you
                                                @elseif($notification->follow->state === \App\Models\Follow::STATE_ACCEPTED)
                                                    followed you
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($notification->date)->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Comments Section -->
                <div class="notifications-section hidden w-full" id="comments-content">
                    @if($commentNotifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No comment notifications available.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($commentNotifications as $notification)
                                <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border-b border-gray-300">
                                    <div class="flex-1">
                                        @if(isset($notification->comment) && isset($notification->comment->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->comment->user->username }} 
                                                @if($notification->comment->parentcommentid)
                                                    replied to a comment:
                                                @else
                                                    commented:
                                                @endif
                                                <span class="italic text-gray-600">"{{ Str::limit($notification->comment->message, 50) }}"</span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->comment->post->postid]) }}" class="text-sky-600 hover:underline">
                                                    On post: "{{ Str::limit($notification->comment->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($notification->date)->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Likes Section -->
                <div class="notifications-section hidden w-full" id="likes-content">
                    @if($likeNotifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No like notifications yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($likeNotifications as $notification)
                                <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border-b border-gray-300">
                                    <div class="flex-1">
                                        @if(isset($notification->like) && isset($notification->like->post))
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $notification->like->user->username }} liked your post
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <a href="{{ route('posts.show', ['id' => $notification->like->post->postid]) }}" class="text-sky-600 hover:underline">
                                                    "{{ Str::limit($notification->like->post->message, 50) }}"
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($notification->date)->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                                                        
                <!-- Follows Section -->
                <div class="notifications-section hidden w-full" id="follows-content">
                    @if($followNotifications->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600">No follow notifications yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($followNotifications as $notification)
                                @if(isset($notification->follow) && isset($notification->follow->follower))
                                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm border-b border-gray-300">
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold text-gray-800">
                                                <a href="{{ route('profile', ['username' => $notification->follow->follower->username]) }}" 
                                                    class="text-sky-600 hover:underline">
                                                    {{ $notification->follow->follower->username }}
                                                </a>
                                                @if($notification->follow->state === \App\Models\Follow::STATE_PENDING)
                                                    requested to follow you
                                                @elseif($notification->follow->state === \App\Models\Follow::STATE_ACCEPTED)
                                                    followed you
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($notification->date)->diffForHumans() }}
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
