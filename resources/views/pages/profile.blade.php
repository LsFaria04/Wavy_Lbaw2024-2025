    @extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full">
            <!-- Profile Top Section -->
            <header class="w-full max-w-3xl p-4 bg-white rounded-lg shadow-md flex items-center">
                <a href="{{ route('home') }}" class="flex items-center text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800">{{ $user->username }}</h1>
            </header>

            <!-- Background Section -->
            <div class="w-full max-w-3xl relative bg-gray-300 h-48 rounded-lg overflow-hidden">
                <div class="absolute inset-0 bg-cover bg-center">
                    <!-- Background Image -->
                </div>
            </div>

            <!-- Profile Info Section -->
            <div class="w-full max-w-3xl relative bg-white rounded-lg shadow-md">
                <div class="absolute -top-16 left-4 w-32 h-32 bg-gray-200 rounded-full border-4 border-white overflow-hidden">
                    <!-- Profile Image -->
                </div>

                <div class="pt-20 px-6 pb-4">
                    <h1 class="text-2xl font-bold">{{ $user->username }}</h1>
                    <p class="text-gray-500 mt-2">{{ $user->bio ?? 'No bio available.' }}</p>
                </div>
                <nav class="flex justify-around border-b">
                    <button id="tab-posts" data-tab="user-posts" class="tab-btn flex-1 text-center py-3 text-sm font-semibold text-gray-700 border-b-2 hover:text-blue-600 text-blue-600 border-blue-600">Posts</button>
                    <button id="tab-comments" data-tab="user-comments" class="tab-btn flex-1 text-center py-3 text-sm font-semibold text-gray-700 border-b-2 hover:text-blue-600">Comments</button>
                    <button id="tab-likes" data-tab="user-likes" class="tab-btn flex-1 text-center py-3 text-sm font-semibold text-gray-700 border-b-2 hover:text-blue-600">Likes</button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="w-full max-w-3xl bg-white rounded-lg shadow-md mt-4 p-6">
                <!-- Posts Section -->
                <section id="user-posts" class="tab-content">
                    @if($user->posts->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No posts found for this user.</p>
                        </div>
                    @else
                        @foreach($user->posts as $post)
                            <div class="mb-4 p-4 bg-gray-100 rounded-lg shadow">
                                <h3 class="font-bold text-gray-800">{{ $post->user->username }}</h3>
                                <span class="text-sm text-gray-500">{{ $post->createddate }}</span>
                                <p class="mt-2 text-gray-700">{{ $post->message }}</p>
                            </div>
                        @endforeach
                    @endif
                </section>

                <!-- Comments Section -->
                <section id="user-comments" class="tab-content hidden">
                    @if($user->comments->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No comments found for this user.</p>
                        </div>
                    @else
                        @foreach($user->comments as $comment)
                            <div class="mb-4 p-4 bg-gray-100 rounded-lg shadow">
                                <h3 class="font-bold text-gray-800">{{ $comment->username }}</h3>
                                <span class="text-sm text-gray-500">{{ $comment->createdDate }}</span>
                                <p class="mt-2 text-gray-700">{{ $comment->message }}</p>
                            </div>
                        @endforeach
                    @endif
                </section>

                <!-- Likes Section -->
                <section id="user-likes" class="tab-content hidden">
                    <div class="flex justify-center items-center h-32">
                        <p class="text-gray-600 text-center">Likes TO-DO.</p>
                    </div>
                </section>
            </div>
        </div>

        @section('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const buttons = document.querySelectorAll('.tab-btn');
                    const sections = document.querySelectorAll('.tab-content');

                    buttons.forEach(button => {
                        button.addEventListener('click', () => {
                            const targetTab = button.dataset.tab;

                            // Toggle active button
                            buttons.forEach(btn => {
                                btn.classList.remove('text-blue-600', 'border-blue-600');
                            });
                            button.classList.add('text-blue-600', 'border-blue-600');

                            // Toggle visible content
                            sections.forEach(section => {
                                if (section.id === targetTab) {
                                    section.classList.remove('hidden');
                                } else {
                                    section.classList.add('hidden');
                                }
                            });
                        });
                    });
                });
            </script>
        @endsection

    @endSection