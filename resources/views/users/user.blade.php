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

    <!-- TODO: VER ORDERS (facturas) -->

    <br/>

    <a href="{{ route('user.edit', $user) }}" class="btn btn-primary">Editar perfil</a>

    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
        Eliminar cuenta
    </button>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Eliminar cuenta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                    <form method="POST" action="/user">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
