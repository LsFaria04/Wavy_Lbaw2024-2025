<table class="min-w-full table-auto bg-white rounded-lg shadow-md">
    <thead>
        <tr class="text-left bg-gray-200">
            <th class="px-4 py-2 text-gray-600 font-semibold">Username</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Email</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">State</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Visibility</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Admin</th>
            <th class="px-4 py-2 text-gray-600 font-semibold">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr class="border-t">
                <td class="px-4 py-2 text-gray-800">{{ $user->username }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $user->email }}</td>
                <td class="px-4 py-2 text-gray-800">
                    <span class="px-2 py-1 text-gray-700 bg-gray-100 rounded-full">{{ $user->state }}</span>
                </td>
                <td class="px-4 py-2 text-gray-800">
                    <span class="px-2 py-1 rounded-full w-[70px] text-center block
                        {{ $user->visibilitypublic == 1 ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }} rounded-full">
                        {{ $user->visibilitypublic == 1 ? 'Public' : 'Private' }}
                    </span>
                </td>
                <td class="px-4 py-2 text-gray-800">
                    <span class="px-2 py-1 rounded-full w-[70px] text-center block
                        {{ $user->isadmin ? 'text-blue-700 bg-blue-100' : 'text-gray-700 bg-gray-100' }}">
                        {{ $user->isadmin ? 'Admin' : 'User' }}
                    </span>                </td>
                <td class="px-4 py-2 text-gray-800">
                    <a href="{{ route('admin.users.edit', ['id' => $user->userid]) }}" class="text-blue-600 hover:underline">Edit</a> |
                    <form action="{{ route('admin.users.destroy', ['id' => $user->userid]) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if ($users->hasPages())
    <nav class="flex items-center justify-center space-x-2 mt-6 pagination" aria-label="Pagination">
        @if ($users->onFirstPage())
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed" aria-disabled="true">←</span>
        @else
            <a href="{{ $users->previousPageUrl() }}" 
               class="pagination-link px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-all ease-in-out duration-300" 
               aria-label="Previous page">←</a>
        @endif

        @foreach ($users->links() as $link)
            @if (is_string($link))
                <span class="px-4 py-2 text-gray-500">{{ $link }}</span>
            @elseif (is_array($link))
                @foreach ($link as $page => $url)
                    @if ($page == $users->currentPage())
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

        @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" 
               class="pagination-link px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-all ease-in-out duration-300" 
               aria-label="Next page">→</a>
        @else
            <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed" aria-disabled="true">→</span>
        @endif
    </nav>
@endif