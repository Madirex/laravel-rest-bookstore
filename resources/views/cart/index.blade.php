<!-- resources/views/cart.blade.php -->
@extends('main')
@section('title', 'Libros - NULLERS')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
</head>
<body>
@if($cartItems)
<div class="container bg-white rounded">
    <h3>Carrito</h3>
   <div class=""></div>
    <ul>
        @foreach($cartItems as $item)
        <li>
            <p>Libro: {{ $item['id'] }}</p>
            <p>Nombre: {{ $item['name'] }}</p>
            <p>Precio: {{ $item['price'] }}</p>
            <p>Cantidad: {{ $item['quantity'] }}</p>
            <img src="{{asset('storage/' . $item['image'])}}" alt="{{ $item['name'] }}" width="100">
        </li>
        @endforeach
    </ul>
</div>
@else
<p>Tu carrito está vacío.</p>
@endif
</body>
</html>
@endsection
