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
