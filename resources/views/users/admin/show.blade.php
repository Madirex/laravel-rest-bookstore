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
            <p class="card-text"><i class="fas fa-user-tag"></i> Rol: {{ $user->role }}</p>
        </div>
    </div>

    @if($user->address)
        <br/>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> Dirección</h5>
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-road"></i> {{ $user->address->street }} {{ $user->address->number }}</h5>
                    <p class="card-text"><i class="fas fa-city"></i> Ciudad: {{ $user->address->city }}</p>
                    <p class="card-text"><i class="fas fa-flag"></i> Provincia: {{ $user->address->province }}</p>
                    <p class="card-text"><i class="fas fa-globe"></i> País: {{ $user->address->country }}</p>
                    <p class="card-text"><i class="fas fa-mail-bulk"></i> Código postal: {{ $user->address->postal_code }}</p>
                    <a class="btn btn-secondary" href="{{ route('addresses.edit', $user->address->id) }}"><i class="fas fa-edit"></i> Editar dirección</a>
                </div>
            </div>
        </div>
    @endif

    <br/>

    {{-- Si el usuario es administrador --}}
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-secondary" href="{{ route('users.admin.edit', $user->id) }}"><i class="fas fa-edit"></i> Editar</a>
        <a class="btn btn-info" href="{{ route('users.admin.image', $user->id) }}"><i class="fas fa-image"></i> Editar imagen</a>
    @endif
@endsection
