@php use App\Models\Category; @endphp
@extends('main')
@section('title', 'Categorías CRUD')
@section('content')
    <br/>
    <h1>Listado de categorías</h1>
    <form action="{{ route('categories.index') }}" class="mb-3" method="get">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Nombre">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </div>
    </form>

    {{-- Si hay registros --}}
    @if (count($categories) > 0)
        <div class="category-container">
            {{-- Por cada category --}}
            @foreach ($categories as $category)
                <div class="category-card">
                    <div class="category-info">
                        <h2>{{ $category->name }}</h2>
                    </div>
                    <div class="category-actions">
                        <a class="btn btn-primary btn-sm" href="{{ route('categories.show', $category->id) }}"><i class="fas fa-info-circle"></i> Detalles</a>
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                            <a class="btn btn-secondary btn-sm" href="{{ route('categories.edit', $category->id) }}"><i class="fas fa-edit"></i> Editar</a>
                            <a class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#confirmDeleteModal{{ $category->id }}"><i class="fas fa-trash-alt"></i> Eliminar</a>
                        @endif
                    </div>
                </div>

                <!-- Modal de Confirmación de eliminación -->
                <div class="modal fade" id="confirmDeleteModal{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
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
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display: inline;">
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
        <p class='lead'><em>No se han encontrado categorías.</em></p>
    @endif

    <br/>
    <div class="pagination-container">
        {{ $categories->links('pagination::bootstrap-4') }}
    </div>

    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-success" href={{ route('categories.create') }}><i class="fas fa-plus"></i> Nueva categoría</a>
    @endif

@endsection
