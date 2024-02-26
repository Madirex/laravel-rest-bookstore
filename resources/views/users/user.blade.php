@extends('main')
@section('title', 'Detalles del usuario')
@section('content')
    <br/>
    <h1>Detalles del usuario</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user"></i> {{ $user->username }}</h5>
            <br/>
            <p class="card-text"><i class="fas fa-id-card"></i> Nombre: {{ $user->name }} {{ $user->surname }}</p>
            <p class="card-text"><i class="fas fa-envelope"></i> Email: {{ $user->email }}</p>
            <p class="card-text"><i class="fas fa-phone"></i> Teléfono: {{ $user->phone }}</p>
            <p class="card-text"><i class="fas fa-map-marker-alt"></i> Dirección: {{ $user->address }}</p>
            <p class="card-text"><i class="fas fa-user-tag"></i> Rol: {{ $user->role }}</p>
            <p class="card-text"><i class="fas fa-calendar-alt"></i> Fecha de creación: {{ $user->created_at }}</p>
            <p class="card-text"><i class="fas fa-sync-alt"></i> Fecha de actualización: {{ $user->updated_at }}</p>
            <!-- TODO: IMAGE -->
        </div>
    </div>

    <!-- TODO: MODIFICAR PERFIL -->
    <!-- TODO: VER ORDERS (facturas) -->

@endsection
