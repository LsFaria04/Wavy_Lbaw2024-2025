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
            <tr class="border-t" data-user-id="{{ $user->userid }}">
                <td class="username px-4 py-2 text-gray-800">{{ $user->username }}</td>
                <td class="email px-4 py-2 text-gray-800">{{ $user->email }}</td>
                <td class="px-4 py-2 text-gray-800">
                    <span class="state px-2 py-1 text-gray-700 bg-gray-100 rounded-full">{{ $user->state }}</span>
                </td>
                <td class="visibility px-4 py-2 text-gray-800">
                    <span class="px-2 py-1 rounded-full w-[70px] text-center block
                        {{ $user->visibilitypublic == 1 ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }} rounded-full">
                        {{ $user->visibilitypublic == 1 ? 'Public' : 'Private' }}
                    </span>
                </td>
                <td class="admin px-4 py-2 text-gray-800">
                    <span class="px-2 py-1 rounded-full w-[70px] text-center block
                        {{ $user->isadmin ? 'text-blue-700 bg-blue-100' : 'text-gray-700 bg-gray-100' }}">
                        {{ $user->isadmin ? 'Admin' : 'User' }}
                    </span>                </td>
                <td class="px-4 py-2 text-gray-800">
                    <button class="text-blue-600 hover:underline edit-user-button" data-user-id="{{ $user->userid }}">Edit</button>
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

<!--Edit User Menu-->
<div id="editUserModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg w-[400px]">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Edit User</h2>
        <form id="editUserForm">
            @csrf
            <input type="hidden" id="editUserId" name="id">
            
            <div class="mb-4">
                <label for="editUsername" class="block text-gray-600">Username</label>
                <input type="text" id="editUsername" name="username" class="w-full p-2 border rounded mt-2">
            </div>
            
            <div class="mb-4">
                <label for="editEmail" class="block text-gray-600">Email</label>
                <input type="email" id="editEmail" name="email" class="w-full p-2 border rounded mt-2">
            </div>
            
            <div class="mb-4">
                <label for="editState" class="block text-gray-600">State</label>
                <input type="text" id="editState" name="state" class="w-full p-2 border rounded mt-2">
            </div>

            <div class="mb-4">
                <label for="editVisibility" class="block text-gray-600">Visibility</label>
                <select id="editVisibility" name="visibilitypublic" class="w-full p-2 border rounded mt-2">
                    <option value="1">Public</option>
                    <option value="0">Private</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="editAdmin" class="block text-gray-600">Admin</label>
                <select id="editAdmin" name="isadmin" class="w-full p-2 border rounded mt-2">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="closeModalBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded mr-2">Cancel</button>
                <button type="submit" id="saveEditBtn" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>


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