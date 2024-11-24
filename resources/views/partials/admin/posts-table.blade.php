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
            <tr class="border-t">
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
                    <form action="{{ route('admin.posts.destroy', $post->postid) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

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