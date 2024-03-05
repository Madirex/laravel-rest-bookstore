@php use App\Models\User; @endphp

@extends('main')
@section('title', 'Usuarios')
@section('content')
    <br/>
    <h1>Listado de usuarios</h1>
    <form action="{{ route('users.admin.index') }}" class="mb-3" method="get">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Nombre">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </div>
    </form>

    {{-- Si hay registros --}}
    @if (count($users) > 0)
        <div class="user-container">
            {{-- Por cada user --}}
            @foreach ($users as $user)
                <div class="user-card">
                    @if($user->image != User::$IMAGE_DEFAULT)
                        <img alt="Imagen de la cuenta" class="rounded float-left" width="150" height="150" style="margin: 15px" src="{{ asset('storage/' . $user->image) }}">
                    @else
                        <img alt="Imagen por defecto" class="rounded float-left" width="150" height="150" style="margin: 15px" src="{{ '/' . User::$IMAGE_DEFAULT }}">
                    @endif


                    <div class="user-info">
                        <h2>{{ $user->username }}</h2>
                        <p>✉️ {{ $user->email }}</p>
                        <p>Nombre: {{ $user->name }} {{ $user->surname }}</p>


                    </div>
                    <div class="user-actions">
                        <a class="btn btn-primary btn-sm" href="{{ route('users.admin.show', $user->id) }}"><i class="fas fa-info-circle"></i></a>
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                            <a class="btn btn-secondary btn-sm" href="{{ route('users.admin.edit', $user->id) }}"><i class="fas fa-edit"></i></a>
                            <a class="btn btn-info btn-sm" href="{{ route('users.admin.image', $user->id) }}"><i class="fas fa-image"></i></a>

                            <a class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#confirmDeleteModal{{ $user->id }}"><i class="fas fa-trash-alt"></i></a>
                        @endif
                    </div>
                </div>

                <!-- Modal de Confirmación de eliminación -->
                <div class="modal fade" id="confirmDeleteModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas eliminar este elemento?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                                <!-- Formulario para eliminar el elemento -->
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Borrar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class='lead'><em>No se han encontrado usuarios.</em></p>
    @endif

    <br/>
    <div class="pagination-container">
        {{ $users->links('pagination::bootstrap-4') }}
    </div>

    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-success" href={{ route('users.admin.create') }}><i class="fas fa-plus"></i> Nuevo usuario</a>
    @endif

@endsection
