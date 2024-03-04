@extends('main')
@section('title', 'Pedidos - NULLERS')
@section('content')
    <br/>
    <h1>Detalle del pedido</h1>
    <div class="card">
        <div class="card-header">
            <h2>Pedido #{{ $order->id }}</h2>
        </div>
        <div class="card-body">
            <h3>Usuario: {{ $order->user->name }}</h3>
            <h5>Email: {{ $order->user->email }}</h5>
            <div class="mb-5">
                <p>Calle:  {{ $order->user->address->street }}</p>
                <p>Ciudad: {{ $order->user->address->city }}</p>
                <p>Provincia: {{ $order->user->address->province }}</p>
                <p>Country: {{ $order->user->address->country }}</p>
                <p>CP:     {{ $order->user->address->postal_code }}</p>
            </div>
            <p>Fecha: {{ $order->created_at }}</p>
            <p>Total: {{ $order->total_amount }} €</p>
            <p>Estado: {{ $order->status }}</p>
            <h3>Lineas de pedido</h3>
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
