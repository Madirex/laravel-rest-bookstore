@php use App\Models\User; @endphp
@extends('main')
@section('title', 'Detalles del usuario')
@section('content')
    <br/>
    <h1>Detalles del usuario</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user"></i> {{ $user->username }}</h5>
            @if($user->image != User::$IMAGE_DEFAULT)
                <img alt="Imagen de la cuenta" class="rounded float-left" width="150" height="150" style="margin: 15px" src="{{ asset('storage/' . $user->image) }}">
            @else
                <img alt="Imagen por defecto" class="rounded float-left" width="150" height="150" style="margin: 15px" src="{{ '/' . User::$IMAGE_DEFAULT }}">
            @endif
            <br/>
            <p class="card-text"><i class="fas fa-id-card"></i> Nombre: {{ $user->name }} {{ $user->surname }}</p>
            <p class="card-text"><i class="fas fa-envelope"></i> Email: {{ $user->email }}</p>
            <p class="card-text"><i class="fas fa-phone"></i> Teléfono: {{ $user->phone }}</p>
            <p class="card-text"><i class="fas fa-map-marker-alt"></i> Dirección: {{ $user->address }}</p>
            <p class="card-text"><i class="fas fa-user-tag"></i> Rol: {{ $user->role }}</p>
        </div>
    </div>

    <br/>

    <a class="btn btn-primary" href="{{ route('users.admin.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>

    {{-- Si el usuario es administrador --}}
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-secondary" href="{{ route('users.admin.edit', $user->id) }}"><i class="fas fa-edit"></i> Editar</a>
        <a class="btn btn-info" href="{{ route('users.admin.image', $user->id) }}"><i class="fas fa-image"></i> Editar imagen</a>

    @endif
@endsection
