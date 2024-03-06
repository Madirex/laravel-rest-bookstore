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
            <p>Total: {{ $order->total_amount }} €</p>
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

        <!-- TOTAL -->
        <div class="card-body">
            <h5 style="font-weight: bold">Total: {{ $order->total_amount }} €</h5>
        </div>

        <!-- editar este pedido -->
        <div class="card-footer">
            <a class="btn btn-secondary" href="{{ route('orders.edit', $order->id) }}"><i class="fas fa-edit"></i>
                Editar</a>
            <!-- imprimir factura -->
            <a href="{{ route('orders.invoice', ['id' => $order->id]) }}" class="btn btn-primary">Descargar Factura</a>

            <!-- enviar por email -->
            <a href="{{ route('orders.email_invoice', ['id' => $order->id]) }}" class="btn btn-primary">Enviar factura por email</a>
        </div>

        <!-- fecha de finalizción si está finalizado -->
        @if($order->status == 'delivered')
            <div class="card-footer">
                <p>Fecha de finalización del pedido: {{ $order->finished_at }}</p>
            </div>
        @endif

    </div>
@endsection
