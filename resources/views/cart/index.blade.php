<!-- resources/views/cart.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
</head>
<body>
<h1>Carrito de Compras</h1>
@if($cartItems)
<ul>
    @foreach($cartItems as $bookId => $quantity)
    <li>Libro ID: {{ $bookId }} - Cantidad: {{ $quantity }}</li>
    @endforeach
</ul>
@else
<p>Tu carrito está vacío.</p>
@endif
</body>
</html>
