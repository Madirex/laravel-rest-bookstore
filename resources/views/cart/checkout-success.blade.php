@extends('main')

@section('title', 'Compra Exitosa - NULLERS')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Compra Exitosa</title>
</head>
<body>
<div class="container bg-white rounded mt-5">
    <div class="py-5 text-center">
        <h2>¡Compra realizada con éxito!</h2>
        <p class="lead">Gracias por tu compra. La información de tu pedido ha sido enviada a tu correo electrónico. Puedes seguir navegando en nuestro sitio o revisar el estado de tu pedido en tu perfil.</p>
        <hr>

        <a href="{{ route('books.index') }}" class="btn btn-primary">Continuar comprando</a>
        @if(isset($order))
        <!-- imprimir factura -->
        <a href="{{ route('orders.invoice', ['id' => $order->id]) }}" class="btn btn-primary">Descargar Factura</a>

        <!-- enviar por email -->
        <a href="{{ route('orders.email_invoice', ['id' => $order->id]) }}" class="btn btn-primary">Enviar factura por email</a>
        @endif
    </div>
</div>
</body>
</html>
@endsection
