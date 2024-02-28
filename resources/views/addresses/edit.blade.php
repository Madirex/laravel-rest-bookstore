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

    <form action="{{ route("addresses.update", $address->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="street">Calle:</label>
            <input class="form-control" id="street" name="street" type="text" required value="{{$address->street}}">
        </div>
        <div class="form-group">
            <label for="number">Número:</label>
            <input class="form-control" id="number" name="number" type="text" required value="{{$address->number}}">
        </div>
        <div class="form-group">
            <label for="city">Ciudad:</label>
            <input class="form-control" id="city" name="city" type="text" required value="{{$address->city}}">
        </div>
        <div class="form-group">
            <label for="province">Provincia:</label>
            <input class="form-control" id="province" name="province" type="text" required value="{{$address->province}}">
        </div>
        <div class="form-group">
            <label for="country">País:</label>
            <input class="form-control" id="country" name="country" type="text" required value="{{$address->country}}">
        </div>
        <div class="form-group">
            <label for="postal_code">Código Postal:</label>
            <input class="form-control" id="postal_code" name="postal_code" type="text" required value="{{$address->postal_code}}">
        </div>
        <div class="form-group">
            <label for="addressable_type">Asignar dirección:</label>
            <select class="form-control" id="addressable_type" name="addressable_type" required>
                <option value="">Selecciona el tipo</option>
                <option value="App\Models\User" @if($address->addressable_type == 'App\Models\User') selected @endif>Usuario</option>
            </select>
        </div>
        <div class="form-group">
            <label for="addressable_id">ID:</label>
            <input class="form-control" id="addressable_id" name="addressable_id" type="text" required value="{{$address->addressable_id}}">
        </div>
        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary mx-2" href="{{ route('addresses.index') }}">Volver</a>
    </form>

@endsection
