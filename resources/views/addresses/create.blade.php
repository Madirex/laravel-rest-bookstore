@php use App\Models\Address; @endphp
@extends('main')

@section('title', 'Crear dirección')

@section('content')
    <h1>Crear dirección</h1>

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

    <form action="{{ route("addresses.store") }}" method="post">
        @csrf
        <div class="form-group">
            <label for="street">Calle:</label>
            <input class="form-control" id="street" name="street" type="text" required value="{{ old('street') }}">
        </div>
        <div class="form-group">
            <label for="number">Número:</label>
            <input class="form-control" id="number" name="number" type="text" required value="{{ old('number') }}">
        </div>
        <div class="form-group">
            <label for="city">Ciudad:</label>
            <input class="form-control" id="city" name="city" type="text" required value="{{ old('city') }}">
        </div>
        <div class="form-group">
            <label for="postal_code">Código Postal:</label>
            <input class="form-control" id="postal_code" name="postal_code" type="text" required value="{{ old('postal_code') }}">
        </div>
        <div class="form-group">
            <label for="addressable_type">Asignar dirección:</label>
            <select class="form-control" id="addressable_type" name="addressable_type" required>
                <option value="">Selecciona el tipo</option>
                <option value="App\Models\User" {{ old('addressable_type') == 'App\Models\User' ? 'selected' : '' }}>Usuario</option>
                <option value="App\Models\Shop" {{ old('addressable_type') == 'App\Models\Shop' ? 'selected' : '' }}>Tienda</option>
                <option value="App\Models\Order" {{ old('addressable_type') == 'App\Models\Order' ? 'selected' : '' }}>Pedido</option>
            </select>
        </div>
        <div class="form-group">
            <label for="addressable_id">ID:</label>
            <input class="form-control" id="addressable_id" name="addressable_id" type="text" required value="{{ old('addressable_id') }}">
        </div>
        <button class="btn btn-primary" type="submit">Crear</button>
    </form>

@endsection
