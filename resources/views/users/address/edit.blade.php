@php use App\Models\Address; @endphp
@extends('main')
@section('title', 'Editar dirección')

@section('content')
    <h1>Editar dirección</h1>

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

    <form action="{{ route("user.address.update", $address->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="street">Calle:</label>
            <input class="form-control" id="street" name="street" type="text" required
                   value="{{ old('street', $address->street) }}">
        </div>
        <div class="form-group">
            <label for="number">Número:</label>
            <input class="form-control" id="number" name="number" type="text" required
                   value="{{ old('number', $address->number) }}">
        </div>
        <div class="form-group">
            <label for="city">Ciudad:</label>
            <input class="form-control" id="city" name="city" type="text" required
                   value="{{ old('city', $address->city)}}">
        </div>
        <div class="form-group">
            <label for="postal_code">Código Postal:</label>
            <input class="form-control" id="postal_code" name="postal_code" type="text" required
                   value="{{ old('postal_code', $address->postal_code) }}">
        </div>
        <button class="btn btn-primary" type="submit">Actualizar</button>
    </form>

@endsection
