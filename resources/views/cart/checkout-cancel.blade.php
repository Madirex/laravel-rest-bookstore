@extends('main')

@section('title', 'Pedido Cancelado - NULLERS')

@section('content')
<div class="container mt-5">
    <div class="text-center">
        <h2>Pedido Cancelado</h2>
        <p>Tu pedido ha sido cancelado. Si fue un error, puedes intentar realizar la compra nuevamente.</p>

        <a href="{{ route('books.index') }}" class="btn btn-primary">Volver a la Tienda</a>
        <!--ver pedidos pendientes-->
        <a href="{{ route('orders.index') }}" class="btn btn-primary">Ver Pedidos</a>
    </div>
</div>
@endsection
