@extends('main')
@section('title', 'Libros - NULLERS')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <style>
        ul {
            list-style: none;
            padding: 0;
        }
    </style>
</head>
<body>
@if($cartItems)
<div class="container bg-white rounded">
    <div class="border-bottom pb-md-4 pt-md-4">
        <div class="d-flex align-items-center justify-content-lg-start">
            <p class="h3 mb-0">Carrito</p>
        </div>
    </div>
    <ul>
        @foreach($cartItems as $item)
        <li class="cart-item">
            <div class="container-fluid mt-3 mb-3">
                <div class="row border-bottom pb-2 pt-2">
                    <div class="col-md-3">
                        <a href="{{ route('books.show', $item['id']) }}">
                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                 style=" height: 180px">
                        </a>
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="h5">{{ $item['name'] }}</p>
                                <div class="d-flex flex-column align-items-start">
                                    @if($item['stock'] >= 0)
                                    <span class="badge text-success p-2">En stock</span>
                                    @else
                                    <span class="badge text-secondary p-2">No disponible</span>
                                    @endif
                                    <span class="mb-0 badge p-2">Autor: {{$item['author']}}</span>
                                    <ul class="list-group list-group-horizontal">
                                        <li>
                                            <span class="badge text-info">Cantidad:</span>
                                            <input type="number" class="item-quantity" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}"
                                                   style="width: 3rem; border-radius: 5px; border:1px solid #6c757d"
                                                   onchange="updateItemQuantity({{ $item['id'] }}, this.value)">
                                        </li>
                                        <form method="POST" action="{{ route('cart.remove') }}"
                                              class="form-remove-book">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="book_id" value="{{ $item['id'] }}">
                                            <button type="submit" class="text-danger badge">Eliminar</button>
                                        </form>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex justify-content-end align-items-start">
                                <p class="mb-0 h4 item-price" data-price="{{ $item['price'] }}">{{ $item['price'] }} €</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
    <div class="container mb-3">
        <div class="row">
            <div class="col-md-8">
                <a href="{{ route('books.index') }}" class="text-bg-secondary">Seguir comprando</a>
            </div>
            <div class="col-md-4 d-flex justify-content-end">
                <p class="h4" id="cart-total">Total: 0 €</p>
            </div>
        </div>
    </div>
</div>
@else
<div class="container bg-white rounded">
    <!--cuando carrito esta vacio-->
    <div class="border-bottom pb-md-4 pt-md-4">
        <div class="d-flex align-items-center justify-content-lg-start">
            <p class="h3 mb-0">Carrito</p>
        </div>

        <div class="container-fluid mt-3 mb-3">
            <div class="row pb-2 pt-2">
                <div class="col-md-4">
                    <img src="{{ asset('images/empty_cart.jpg') }}" alt="Carrito vacío" width="100%"
                         style="user-select: none; -webkit-user-drag: none; -moz-user-select: none; -ms-user-select: none;">
                </div>
                <div class="col-md-8 pt-5">
                    <p class="h5 mb-4">No hay libros en el carrito</p>
                    <a href="{{ route('books.index') }}"
                       class="btn btn-primary">Ir a la tienda</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.container').addEventListener('change', function(e) {
            if (e.target && e.target.matches('.item-quantity')) {
                const bookId = e.target.dataset.bookId;
                const quantity = e.target.value;
                updateItemQuantity(bookId, quantity);
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        calculateTotal(true);
    });
    function updateItemQuantity(bookId, quantity) {
        let formData = new FormData();
        formData.append('book_id', bookId);
        formData.append('quantity', quantity);
        formData.append('_token', '{{ csrf_token() }}');

        calculateTotal();

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Item successfully added to cart');
                    setTimeout(calculateTotal, 0);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                setTimeout(calculateTotal, 0);
            });
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const priceElement = item.querySelector('.item-price');
            const quantityInput = item.querySelector('.item-quantity');
            if (priceElement && quantityInput) {
                const price = parseFloat(priceElement.getAttribute('data-price'));
                const quantity = parseInt(quantityInput.value, 10);
                total += price * quantity;
            }
        });
        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.innerText = 'Total: ' + total.toFixed(2) + ' €';
        }
    }

    calculateTotal();
</script>
</html>
@endsection
