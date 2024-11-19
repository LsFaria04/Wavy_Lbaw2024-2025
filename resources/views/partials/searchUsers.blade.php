<h3 class="font-bold text-xl mb-4">Users</h3>
@foreach($users as $user)
    <div class="user mb-4 p-4 bg-white rounded-md shadow-sm">
        <div class="user-header mb-2">
            <h3 class="font-bold">{{ $user->username }}</h3>
        </div>
        <div class="user-body mb-2">
            <p>{{ $user->bio }}</p>
        </div>
    </div>
@endforeach
