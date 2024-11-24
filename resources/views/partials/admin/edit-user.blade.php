@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit User</h1>

        <form action="{{ route('admin.users.update', $user->userid) }}" method="POST">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
