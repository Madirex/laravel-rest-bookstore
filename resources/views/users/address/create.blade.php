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

    <form action="{{ route("user.address.store") }}" method="post">
        @csrf
        <div class="form-group">
            <label for="street">Calle:</label>
            <input class="form-control" id="street" name="street" type="text" required>
        </div>
        <div class="form-group">
            <label for="number">Número:</label>
            <input class="form-control" id="number" name="number" type="text" required>
        </div>
        <div class="form-group">
            <label for="city">Ciudad:</label>
            <input class="form-control" id="city" name="city" type="text" required>
        </div>
        <div class="form-group">
            <label for="province">Provincia:</label>
            <input class="form-control" id="province" name="province" type="text" required>
        </div>
        <div class="form-group">
            <label for="country">País:</label>
            <input class="form-control" id="country" name="country" type="text" required>
        </div>
        <div class="form-group">
            <label for="postal_code">Código Postal:</label>
            <input class="form-control" id="postal_code" name="postal_code" type="text" required>
        </div>
        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('users.profile') }}">Volver</a>
    </form>

@endsection