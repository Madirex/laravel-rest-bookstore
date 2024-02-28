<!-- resources/views/users/image.blade.php -->

@extends('main')

@section('title', 'Cambiar imagen')

@section('content')
    <h1>Cambiar imagen</h1>

    <form action="{{ route('users.updateImage') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="image">Imagen:</label>
            <input type="file" class="form-control-file" id="image" name="image" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar imagen</button>
    </form>
@endsection
