<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->created_at)
                        {{ $user->created_at->diffForHumans()}}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.users.edit', ['id' => $user->userid]) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('admin.users.destroy', ['id' => $user->userid]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
