@php use App\Models\Shop; @endphp

@extends('main')
@section('title', 'Tiendas CRUD')
@section('content')
    <br/>
    <h1>Listado de tiendas</h1>
    <form action="{{ route('shops.index') }}" class="mb-3" method="get">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Nombre">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </div>
    </form>

    {{-- Si hay registros --}}
    @if (count($shops) > 0)
        <div class="shop-container">
            {{-- Por cada shop --}}
            @foreach ($shops as $shop)
                <div class="shop-card">
                    <div class="shop-info">
                        <h2>{{ $shop->name }}</h2>
                        <p>Dirección: {{ $shop->address }}</p>
                    </div>
                    <div class="shop-actions">
                        <a class="btn btn-primary btn-sm" href="{{ route('shops.show', $shop->id) }}"><i class="fas fa-info-circle"></i></a>
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                            <a class="btn btn-secondary btn-sm" href="{{ route('shops.edit', $shop->id) }}"><i class="fas fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#confirmDeleteModal{{ $shop->id }}"><i class="fas fa-trash-alt"></i></a>
                        @endif
                    </div>
                </div>

                <!-- Modal de Confirmación de eliminación -->
                <div class="modal fade" id="confirmDeleteModal{{ $shop->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas eliminar esta tienda?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                                <!-- Formulario para eliminar la tienda -->
                                <form action="{{ route('shops.destroy', $shop->id) }}" method="POST" style="display: inline;">
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
        <p class='lead'><em>No se han encontrado tiendas.</em></p>
    @endif

    <br/>
    <div class="pagination-container">
        {{ $shops->links('pagination::bootstrap-4') }}
    </div>

    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-success" href={{ route('shops.create') }}><i class="fas fa-plus"></i> Nueva tienda</a>
    @endif

@endsection
