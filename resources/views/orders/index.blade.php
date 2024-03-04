@extends('main')
@section('title', 'Pedidos - NULLERS')
@section('content')
<br/>
    @error('error')
        <div class="alert alert-danger" role="alert">
            {{ $message }}
        </div>
    @enderror
    <h1>Listado de pedidos</h1>
    <form action="{{ route('orders.index') }}" class="mb-3" method="get">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Id, status, total, user_id">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Usuario</th>
                <th scope="col">Fecha</th>
                <th scope="col">Total</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            {{-- Por cada order --}}
            @foreach ($orders as $order)
                @if(!$order->is_deleted)
                    <tr>
                        <th scope="row">{{ $order->id }}</th>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->total_amount }} €</td>
                        <td>{{ $order->status }}</td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="{{ route('orders.show', $order->id) }}"><i class="fas fa-info-circle"></i></a>
                            @if(auth()->check() && auth()->user()->hasRole('admin'))
                                <a class="btn btn-secondary btn-sm" href="{{ route('orders.edit', $order->id) }}"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('orders.destroy', $order->id) }}" method="post" style="display: inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $orders->links('pagination::bootstrap-4') }}
    </div>

    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-success" href={{ route('orders.create') }}><i class="fas fa-plus"></i> Nuevo Pedido</a>
    @endif

@endsection
