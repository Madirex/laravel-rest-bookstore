@php use App\Models\User; @endphp
@extends('main')

@section('title', 'Crear usuario')

@section('content')
    <h1>Crear usuario</h1>

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

    <form action="{{ route("users.admin.store") }}" method="post">
        @csrf
        <div class="form-group">
            <label for="username">Nombre de usuario:</label>
            <input class="form-control" id="username" name="username" type="text" required>
        </div>
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input class="form-control" id="name" name="name" type="text" required>
        </div>
        <div class="form-group">
            <label for="surname">Apellidos:</label>
            <input class="form-control" id="surname" name="surname" type="text" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input class="form-control" id="email" name="email" type="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Teléfono:</label>
            <input class="form-control" id="phone" name="phone" type="text" required>
        </div>
        <div class="form-group">
            <label for="address">Dirección:</label>
            <input class="form-control" id="address" name="address" type="text" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input class="form-control" id="password" name="password" type="password" required>
        </div>
        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('users.admin.index') }}">Volver</a>
    </form>

@endsection
