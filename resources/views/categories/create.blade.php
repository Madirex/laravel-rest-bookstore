@php use App\Models\Category; @endphp
@extends('main')

@section('title', 'Crear categoría')

@section('content')
    <h1>Crear categoría</h1>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <br/>
    @endif

    <form action="{{ route("categories.store") }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input class="form-control" id="name" name="name" type="text" value="{{ old('name') }}" required>
        </div>
        <button class="btn btn-primary" type="submit">Crear</button>
    </form>

@endsection
