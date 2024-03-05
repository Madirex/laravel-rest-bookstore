@php use App\Models\Shop; @endphp
@extends('main')
@section('title', 'Editar tienda')

@section('content')
    <h1>Editar tienda</h1>

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

    <form action="{{ route("shops.update", $shop->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input class="form-control" id="name" name="name" type="text" required value="{{ old('name', $shop->name) }}">
        </div>
        <button class="btn btn-primary" type="submit">Actualizar</button>
    </form>

@endsection
