@foreach($users as $user)
    <div class="user border-b border-gray-300 p-4 bg-white">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ $user->state === 'deleted' ? '#' : route('profile', $user->username) }}">
                  <div class="flex flex-row gap-2">
                      <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-300">
                        @php
                            $filePath = null;
                            foreach($user->profilepicture as $pic)
                            if(Str::contains($pic, 'profile')) {
                                $filePath = asset('storage/' . $pic->path);
                            }    
                        @endphp
                        @if($filePath !== null)
                            <img  src="{{ $filePath }}" alt="Image" class=" h-full w-full object-cover rounded-md mb-2 mx-auto" >
                        @endif
                      </div>
                      <h3 class="font-bold text-black hover:text-sky-900">
                        {{ $user->state === 'deleted' ? 'Deleted User' : $user->username }}
                      </h3>
                  </div>
                </a>
                <p class="text-sm text-gray-600">{{ $user->bio }}</p>
            </div>
        </div>
    </div>
@endforeach
