    @extends('layouts.app')

    @section('content')
        <div class="flex flex-col items-center w-full">
            <!-- Profile Top Section -->
            <header class="w-full max-w-3xl p-4 bg-white rounded-lg shadow-md flex items-center sticky top-0 z-10">
                <a href="{{ route('home') }}" class="flex items-center text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-800">{{ $user->username }}</h1>
            </header>

            <!-- Edit Profile Messages -->
            @if (session('success'))
                <div class="alert w-full max-w-3xl p-4 bg-green-100 text-green-800 border border-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert w-full max-w-3xl p-4 bg-red-100 text-red-800 border border-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Background Section -->
            <div class="w-full max-w-3xl relative bg-gray-300 h-48 rounded-lg overflow-hidden pt-24">
                <div class="absolute inset-0 bg-cover bg-center">
                    <!-- Background Image To Add -->
                </div>
            </div>

            <!-- Profile Info Section -->
            <div class="w-full max-w-3xl relative bg-white rounded-lg shadow-md">
                <div class="absolute -top-16 left-4 w-32 h-32 bg-gray-200 rounded-full border-4 border-white overflow-hidden">
                    <!-- Profile Image To Add -->
                </div>

                <!-- Edit Profile only visble if Account owner -->
                <div class="pt-20 px-6 pb-4">
                    <h1 class="text-2xl font-bold">{{ $user->username }}</h1>
                    <p class="text-gray-500 mt-2">{{ $user->bio ?? 'No bio available.' }}</p>
                    @if(auth()->id() === $user->userid)
                        <button 
                            class="absolute top-0 right-0 mt-4 mr-4 px-4 py-2 font-bold bg-gray-800 text-white rounded-2xl"
                            onclick="toggleEditMenu()">
                            Edit Profile
                        </button>
                    @endif
                </div>

                <nav class="flex justify-around">
                    <button id="tab-posts" data-tab="user-posts" class="tab-btn flex-1 text-center py-3 text-sm font-semibold text-gray-500 border-b-2 hover:text-sky-900  border-sky-900">Posts</button>
                    <button id="tab-comments" data-tab="user-comments" class="tab-btn flex-1 text-center py-3 text-sm font-semibold text-gray-500 border-b-2 hover:text-sky-900">Comments</button>
                    <button id="tab-likes" data-tab="user-likes" class="tab-btn flex-1 text-center py-3 text-sm font-semibold text-gray-500 border-b-2 hover:text-sky-900">Likes</button>
                </nav>
            </div>

            <!-- Edit Profile Menu -->
            <div id="edit-profile-menu" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>
                    <form action="{{ route('profile.update', $user->userid) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="username" name="username" value="{{ $user->username }}" class="mt-1 block w-full p-2 border rounded-md" required>
                        </div>
                        <div class="mb-4">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full p-2 border rounded-md">{{ $user->bio }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="visibilitypublic" class="block text-sm font-medium text-gray-700">Profile Visibility</label>
                            <select id="visibilitypublic" name="visibilitypublic" class="mt-1 block w-full p-2 border rounded-md">
                                <option value="1" {{ $user->visibilitypublic ? 'selected' : '' }}>Public</option>
                                <option value="0" {{ !$user->visibilitypublic ? 'selected' : '' }}>Private</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500" onclick="toggleEditMenu()">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-sky-700 text-white rounded-md hover:bg-sky-900">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Content Tabs -->
            <div class="w-full max-w-3xl bg-white rounded-lg shadow-md p-6">
                <!-- Posts Section -->
                <section id="user-posts" class="tab-content">
                    @if($posts->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No posts found for this user.</p>
                        </div>
                    @else
                        @foreach($posts as $post)
                            <div class="mb-4 p-4 bg-white rounded-md shadow">
                                <h3 class="font-bold text-gray-800">{{ $post->user->username }}</h3>
                                <span class="text-sm text-gray-500">{{ $post->createddate->diffForHumans() }}</span>
                                <p class="mt-2 text-gray-700">{{ $post->message }}</p>
                            </div>
                        @endforeach
                    @endif
                </section>

                <!-- Comments Section -->
                <section id="user-comments" class="tab-content hidden">
                    @if($comments->isEmpty())
                        <div class="flex justify-center items-center h-32">
                            <p class="text-gray-600 text-center">No comments found for this user.</p>
                        </div>
                    @else
                        @foreach($comments as $comment)
                            <div class="mb-4 p-4 bg-white rounded-md shadow">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800">{{ $comment->user->username }}</h3>

                                    <!-- Need to create Post Page, Comment Page -->
                                    <!-- And Change the <p> "Replying to" to anchors that redirect --> 
                                    <!-- To those pages -->
                                    @if($comment->parentcommentid)
                                        <p class="text-sm hover:text-sky-900">
                                            <strong>Replying to:</strong>
                                            {{ $comment->parentcomment->user->username }}
                                        </p>
                                    @else
                                        <p class="text-sm hover:text-sky-900">
                                            <strong>Replying to:</strong>
                                            {{ $comment->post->user->username }}
                                        </p>
                                    @endif 
                                </div>
                                <span class="text-sm text-gray-500">{{ $comment->createddate }}</span>
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
                function toggleEditMenu() {
                    const menu = document.getElementById('edit-profile-menu');
                    menu.classList.toggle('hidden');
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const alertBoxes = document.querySelectorAll('.alert');
                    alertBoxes.forEach(alertBox => {
                        setTimeout(() => {
                            alertBox.remove()
                        }, 3000); // Time before fade-out
                    });
                });

                document.addEventListener('DOMContentLoaded', () => {
                    const buttons = document.querySelectorAll('.tab-btn');
                    const sections = document.querySelectorAll('.tab-content');

                    buttons.forEach(button => {
                        button.addEventListener('click', () => {
                            const targetTab = button.dataset.tab;

                            // Toggle active button
                            buttons.forEach(btn => {
                                btn.classList.remove('text-sky-900', 'border-sky-900');
                            });
                            button.classList.add('text-sky-900', 'border-sky-900');

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