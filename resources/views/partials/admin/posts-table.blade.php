<table class="min-w-full table-auto bg-white rounded-lg shadow-md">
    <thead>
        <tr class="text-left bg-gray-200">
            <th class="px-4 py-2 text-gray-600 font-semibold">Message</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Author</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Visibility</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Date</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($posts as $post)
            <tr id="post-{{ $post->postid }}" class="border-t">
                <td class="px-4 py-2 text-gray-800 max-w-xs overflow-hidden text-ellipsis">{{ $post->message }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $post->user->username }}</td>
                <td class="px-4 py-2 text-gray-800">
                    @if ($post->visibilitypublic == 1)
                        <span class="px-2 py-1 text-green-700 bg-green-100 rounded-full w-[70px] text-center block">Public</span>
                    @else
                        <span class="px-2 py-1 text-red-700 bg-red-100 rounded-full w-[70px] text-center block">Private</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-gray-800">{{ $post->createddate->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-gray-800">
                    <button type="button" 
                            class="text-red-600 hover:underline delete-post-button" 
                            data-post-id="{{ $post->postid }}" 
                            data-post-message="{{ $post->message }}">
                        Delete
                    </button>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>

<!--Delete Post Menu-->
<div id="deleteMenu" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-20">
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
        <h2 class="text-xl font-semibold text-gray-900">Delete Post</h2>
        <p class="mt-4 text-sm text-gray-600">Are you sure you want to delete this post? This action cannot be undone.</p>
        <form id="deleteForm" method="POST" action="{{ route('admin.posts.destroy', 'POST_ID') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="post_id" id="postId">
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="cancelButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none" onclick="closeDeleteMenu()">Cancel</button>
                <button type="submit" id="confirmButton" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">Delete</button>
            </div>
        </form>
    </div>
</div>

@if ($posts->hasPages())
    <nav class="flex items-center justify-center space-x-2 mt-6 pagination" aria-label="Pagination">
        @if ($posts->onFirstPage())
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed" aria-disabled="true">←</span>
        @else
            <a href="{{ $posts->previousPageUrl() }}" 
               class="pagination-link px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-all ease-in-out duration-300" 
               aria-label="Previous page">←</a>
        @endif

        @foreach ($posts->links() as $link)
            @if (is_string($link))
                <span class="px-4 py-2 text-gray-500">{{ $link }}</span>
            @elseif (is_array($link))
                @foreach ($link as $page => $url)
                    @if ($page == $posts->currentPage())
                        <a href="{{ $url }}" 
                           class="pagination-link active bg-blue-600 text-white px-4 py-2 rounded-lg"
                           aria-current="page">
                            {{ $page }}
                        </a>
                    @else
                        <a href="{{ $url }}" 
                           class="pagination-link bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-blue-500 transition-all ease-in-out duration-300" 
                           data-page="{{ $page }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($posts->hasMorePages())
            <a href="{{ $posts->nextPageUrl() }}" 
               class="pagination-link px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-all ease-in-out duration-300" 
               aria-label="Next page">→</a>
        @else
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed" aria-disabled="true">→</span>
        @endif
    </nav>
@endif