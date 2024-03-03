@extends('main')

@section('title', 'Editar perfil')

@section('content')
    <h1>Editar perfil</h1>

    <!-- errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('user.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="username">Nombre de usuario</label>
            <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
        </div>

        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="surname">Apellido</label>
            <input type="text" class="form-control" id="surname" name="surname" value="{{ old('surname', $user->surname) }}" required>
        </div>

        <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
        </div>

        <div class="form-group">
            <label for="password">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </form>
@endsection
