@foreach($groups as $group)
    <div class="group border-b border-gray-300 p-4 bg-white">
        <div class="group-header mb-2">
            <h3 class="font-bold">
                <a href=" {{ route('group', $group->groupname) }}" 
                class="text-black hover:text-sky-900">
                    {{ $group->groupname }}
                </a>
            </h3>
        </div>
        <div class="group-body mb-2">
            <p>{{ $group->description }}</p>
        </div>
    </div>
@endforeach
