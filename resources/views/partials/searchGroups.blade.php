<h3 class="font-bold text-xl mb-4">Groups</h3>
@foreach($groups as $group)
    <div class="group mb-4 p-4 bg-white rounded-md shadow-sm">
        <div class="group-header mb-2">
            <h3 class="font-bold">{{ $group->groupname }}</h3>
        </div>
        <div class="group-body mb-2">
            <p>{{ $group->description }}</p>
        </div>
    </div>
@endforeach
