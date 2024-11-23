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
                <td class="px-4 py-2 text-gray-800">{{ $post->message }}</td>
                <td class="px-4 py-2 text-gray-800">{{ $post->user->username }}</td>
                <td class="px-4 py-2 text-gray-800">
                    @if ($post->visibilitypublic == 1)
                        <span class="px-2 py-1 text-green-700 bg-green-100 rounded-full">Public</span>
                    @else
                        <span class="px-2 py-1 text-red-700 bg-red-100 rounded-full">Private</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-gray-800">{{ $post->createddate->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-gray-800">
                    <a href="{{ route('admin.posts.edit', $post->postid) }}" class="text-blue-600 hover:underline">Edit</a> |
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
