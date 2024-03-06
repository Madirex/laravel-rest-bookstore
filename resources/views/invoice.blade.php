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
            <p>Total: {{ $order->total_amount }} €</p>
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
    </div>
</body>
</html>
