@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Post</h1>
    
    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')
        
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
            <label for="message">Post Message</label>
            <textarea class="form-control" name="message" id="message" rows="4">{{ old('message', $post->message) }}</textarea>
        </div>

        <div class="form-group">
            <label for="media">Upload Media</label>
            <input type="file" class="form-control" name="media" id="media">
        </div>

        <div class="form-group">
            <label for="remove_media">Remove Existing Media</label>
            <input type="checkbox" name="remove_media" value="1" id="remove_media">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
