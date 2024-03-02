@extends('main')

@section('title', 'Cambiar correo electrónico')

@section('content')
    <h1>Cambiar correo electrónico</h1>

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

    <form method="POST" action="{{ route('user.email.change') }}">
        @csrf

        <div class="form-group">
            <label for="new_email">Nuevo correo electrónico</label>
            <input type="email" class="form-control" id="new_email" name="new_email" required>
        </div>

        <div class="form-group">
            <label for="password">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <button type="submit" class="btn btn-primary">Cambiar correo electrónico</button>
    </form>
@endsection
