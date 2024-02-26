@php use App\Models\CartCode; @endphp
@extends('main')
@section('title', 'Códigos de tienda - NULLERS')
@section('content')
    <br/>
    <h1>Listado de códigos de tienda</h1>
    <form action="{{ route('cartcodes.index') }}" class="mb-3" method="get">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Nombre">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </div>
    </form>

    {{-- Si hay registros --}}
    @if (count($cartcodes) > 0)
        <div class="cartcode-container">
            {{-- Por cada cartcode --}}
            @foreach ($cartcodes as $cartcode)
                <div class="cartcode-card">
                    <div class="cartcode-info">
                        <h2>Código: {{ $cartcode->code }}</h2>
                        @if($cartcode->percent_discount > 0)
                            <p class="card-text"><i class="fas fa-percent"></i> Descuento en porcentaje: {{ $cartcode->percent_discount }}%</p>
                        @endif
                        @if($cartcode->fixed_discount > 0)
                            <p class="card-text"><i class="fas fa-dollar-sign"></i> Descuento fijo: {{ $cartcode->fixed_discount }}</p>
                        @endif
                        <p><i class="fas fa-box-open"></i> Usos disponibles: {{ $cartcode->available_uses }}</p>
                        <p><i class="fas fa-calendar-alt"></i> Fecha de expiración: {{ $cartcode->expiration_date }}</p>
                    </div>
                    <div class="cartcode-actions">
                        <a class="btn btn-primary btn-sm" href="{{ route('cartcodes.show', $cartcode->id) }}"><i
                                class="fas fa-info-circle"></i> Detalles</a>
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                            <a class="btn btn-secondary btn-sm" href="{{ route('cartcodes.edit', $cartcode->id) }}"><i
                                    class="fas fa-edit"></i> Editar</a>
                            <a class="btn btn-danger btn-sm delete-btn" data-toggle="modal"
                               data-target="#confirmDeleteModal{{ $cartcode->id }}"><i class="fas fa-trash-alt"></i>
                                Eliminar</a>
                        @endif
                    </div>
                </div>

                <!-- Modal de Confirmación de eliminación -->
                <div class="modal fade" id="confirmDeleteModal{{ $cartcode->id }}" tabindex="-1" role="dialog"
                     aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas eliminar este código?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                                <!-- Formulario para eliminar el elemento -->
                                <form action="{{ route('cartcodes.destroy', $cartcode->id) }}" method="POST"
                                      style="display: inline;">
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
        <p class='lead'><em>No se han encontrado códigos de tienda.</em></p>
    @endif

    <br/>
    <div class="pagination-container">
        {{ $cartcodes->links('pagination::bootstrap-4') }}
    </div>

    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-success" href={{ route('cartcodes.create') }}><i class="fas fa-plus"></i> Nuevo código</a>
    @endif

@endsection
