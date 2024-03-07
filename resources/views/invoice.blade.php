<!DOCTYPE html>
<html>
<head>
    <title>Factura</title>
    <style>
        /* Agrega tus estilos personalizados aquí */
        body {
            font-family: DejaVu Sans;
        }
        .content {
            margin: 0 auto;
            width: 80%;
        }
        .invoice-header {
            text-align: center;
        }
        .invoice-details {
            margin-top: 50px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details table th, .invoice-details table td {
            border: 1px solid #000;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="invoice-header">
            <h1>Factura para Pedido #{{ $order->id }}</h1>
        </div>
        <div class="invoice-details">
            <p>Usuario: {{ $order->user->name }}</p>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Libro</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderLines as $orderLine)
                        <tr>
                            <td>{{ $orderLine->id }}</td>
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
            <div class="invoice-details">
                <h4>Cupón de descuento aplicado</h4>
                <p>Código: {{ $order->cartCode->code }}</p>
                @if($order->cartCode->percent_discount > 0)
                    <p>Descuento: {{ $order->cartCode->percent_discount }} %</p>
                @else
                    <p>Descuento fijo: {{ $order->cartCode->fixed_discount }} €</p>
                @endif
            </div>
        @endif

        <!-- Total -->
        <div class="invoice-details">
            <h2>Total</h2>
            <p>Total: {{ number_format($order->total_amount, 2, ',', ' ') }} €</p>
            <p>Subtotal: {{ number_format($order->subtotal, 2, ',', ' ') }} €</p>
        </div>

        @if ($order->address)
        <div class="invoice-details">
            <h2>Dirección</h2>
            <p>Calle: {{ $order->address->street }}</p>
            <p>Ciudad: {{ $order->address->city }}</p>
            <p>Provincia: {{ $order->address->province }}</p>
            <p>País: {{ $order->address->country }}</p>
            <p>Código postal: {{ $order->address->postal_code }}</p>
        </div>
        @endif

        <div class="invoice-details">
            <p><b>Fecha del pedido:</b> {{ $order->created_at }}</p>
            <p><b>Estado:</b> {{ $order->status }}</p>
            @if($order->status == 'entregado')
                    <p><b>Fecha de finalización del pedido:</b> {{ $order->finished_at }}</p>
            @endif
        </div>


    </div>
</body>
</html>
