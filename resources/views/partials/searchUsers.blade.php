@foreach($users as $user)
    <div class="user mb-4 p-4 bg-white rounded-md shadow-md">
        <div class="user-header mb-2">
            <h3 class="font-bold">
                <a href="{{ route('profile', $user->username) }}" class="text-black hover:text-sky-900">
                    {{ $user->username }}
                </a>
            </h3>
        </div>
        <div class="user-body mb-2">
            <p>{{ $user->bio }}</p>
        </div>
    </div>
@endforeach
