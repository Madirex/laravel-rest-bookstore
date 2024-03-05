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
                    <p>Calle:  {{ $order->address->street }}</p>
                    <p>Ciudad: {{ $order->address->city }}</p>
                    <p>Provincia: {{ $order->address->province }}</p>
                    <p>Country: {{ $order->address->country }}</p>
                    <p>CP:     {{ $order->address->postal_code }}</p>
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
    </div>
@endsection
