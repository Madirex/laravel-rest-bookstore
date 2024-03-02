@extends('main')

@section('title', 'Crear tienda')

@section('content')
    <h1>Crear tienda</h1>

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

    <form action="{{ route("shops.store") }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input class="form-control" id="name" name="name" type="text" required>
        </div>
        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('shops.index') }}">Volver</a>
    </form>

@endsection
