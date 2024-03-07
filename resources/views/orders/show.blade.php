@extends('main')
@section('title', 'Pedidos - NULLERS')
@section('content')
<br/>
<h1>Detalle del pedido</h1>
<div class="card">
    <div class="card-header">
        <h3>Pedido #{{ $order->id }}</h3>
    </div>
        <div class="card-body">
            @if ($order->address)
                <h2>Dirección</h2>
                <div class="mb-5">
                    <p>Calle: {{ $order->address->street }}</p>
                    <p>Ciudad: {{ $order->address->city }}</p>
                    <p>Provincia: {{ $order->address->province }}</p>
                    <p>Country: {{ $order->address->country }}</p>
                    <p>CP: {{ $order->address->postal_code }}</p>
                </div>
            @endif
            <h2>Usuario: {{ $order->user->name }}</h2>
            <p>Email: {{ $order->user->email }}</p>
            <p>Fecha: {{ $order->created_at }}</p>
            <p>Estado: {{ $order->status }}</p>
            <h3>Pedidos</h3>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                {{-- Por cada order --}}
                @foreach ($order->orderLines as $orderLine)
                    <tr>
                        <th scope="row">{{ $orderLine->id }}</th>
                        <td>{{ $orderLine->book->name }}</td>
                        <td>{{ $orderLine->price }} €</td>
                        <td>{{ $orderLine->quantity }}</td>
                        <td>{{ $orderLine->subtotal }} €</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- cupón de descuento -->
        @if($order->cartCode)
        <div class="card-body">
            <h4>Cupón de descuento aplicado</h4>
            <p>Código: {{ $order->cartCode->code }}</p>

            @if($order->cartCode->percent_discount > 0)
                <p>Descuento: {{ $order->cartCode->percent_discount }} %</p>
            @else
                <p>Descuento fijo: {{ $order->cartCode->fixed_discount }} €</p>
            @endif
        </div>
        @endif

        <!-- TOTAL Y SUBTOTAL-->
        <div class="card-body">
            <h5 style="font-weight: bold">Total: {{ number_format($order->total_amount, 2, ',', ' ') }} €</h5>
            <h5 style="font-weight: bold">Subtotal: {{ number_format($order->subtotal, 2, ',', ' ') }} €</h5>
        </div>

        <!-- editar este pedido -->
        <div class="card-footer">
            @if ($order->status == 'pendiente')
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelOrderModal{{ $order->id }}">Cancelar Pedido</button>
            @endif

            @if (auth()->check() && auth()->user()->hasRole('admin'))
            <a class="btn btn-secondary" href="{{ route('orders.edit', $order->id) }}"><i class="fas fa-edit"></i>
                Editar</a>
            @endif
            <!-- imprimir factura -->
            <a href="{{ route('orders.invoice', ['id' => $order->id]) }}" class="btn btn-primary">Descargar Factura</a>

            <!-- enviar por email -->
            <a href="{{ route('orders.email_invoice', ['id' => $order->id]) }}" class="btn btn-primary">Enviar factura por email</a>
        </div>

        <!-- modal de confirmación para cancelar pedido -->
        <!-- Modal de Confirmación de cancelación -->
        <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelOrderModalLabel">Confirmar Cancelación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas cancelar este pedido?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <a href="{{ route('orders.cancel_invoice', ['id' => $order->id]) }}" class="btn btn-danger">Sí, cancelar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- fecha de finalizción si está finalizado -->
        @if($order->status == 'entregado')
            <div class="card-footer">
                <p>Fecha de finalización del pedido: {{ $order->finished_at }}</p>
            </div>
        @endif

    </div>
</div>
@endsection
