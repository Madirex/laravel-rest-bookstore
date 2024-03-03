@php use App\Models\Address; @endphp
@extends('main')
@section('title', 'Direcciones')
@section('content')
    <br/>
    <h1>Listado de direcciones</h1>
    <form action="{{ route('addresses.index') }}" class="mb-3" method="get">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Calle">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </div>
    </form>

    {{-- Si hay registros --}}
    @if (count($addresses) > 0)
        <div class="address-container">
            {{-- Por cada address --}}
            @foreach ($addresses as $address)
                <div class="address-card">
                    <div class="address-info">
                        <h2><i class="fas fa-road"></i> {{ $address->street }} {{ $address->number }}</h2>
                        <p><i class="fas fa-city"></i> Ciudad: {{ $address->city }}</p>
                        <p><i class="fas fa-flag"></i> Provincia: {{ $address->province }}</p>
                        <p><i class="fas fa-globe"></i> País: {{ $address->country }}</p>
                        <p><i class="fas fa-mail-bulk"></i> Código postal: {{ $address->postal_code }}</p>
                    </div>
                    <div class="address-actions">
                        <a class="btn btn-primary btn-sm" href="{{ route('addresses.show', $address->id) }}"><i class="fas fa-info-circle"></i> Detalles</a>
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                            <a class="btn btn-secondary btn-sm" href="{{ route('addresses.edit', $address->id) }}"><i class="fas fa-edit"></i> Editar</a>
                            <a class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#confirmDeleteModal{{ $address->id }}"><i class="fas fa-trash-alt"></i> Eliminar</a>
                        @endif
                    </div>
                </div>

                <!-- Modal de Confirmación de eliminación -->
                <div class="modal fade" id="confirmDeleteModal{{ $address->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
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
                                <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" style="display: inline;">
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
        <p class='lead'><em>No se han encontrado direcciones.</em></p>
    @endif

    <br/>
    <div class="pagination-container">
        {{ $addresses->links('pagination::bootstrap-4') }}
    </div>

    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-success" href={{ route('addresses.create') }}><i class="fas fa-plus"></i> Nueva dirección</a>
    @endif

@endsection
