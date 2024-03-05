@extends('main')
@section('title', 'Pedidos - NULLERS')
@section('content')
    @error('error')
        <div class="alert alert-danger" role="alert">
            {{ $message }}
        </div>
    @enderror
    <br/>
    <h1>Editar pedido</h1>
    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="status">Estado</label>
            <select class="form-control" id="status" name="status">
                <option value="pending" {{ $order->status == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="shipping" {{ $order->status == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                <option value="delivered" {{ $order->status == 'Entregado' ? 'selected' : '' }}>Entregado</option>
            </select>
        </div>


        <div class="form-group">
            <label for="user_id">Usuario id</label>
            <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $order->user->id }}" disabled>
        </div>
        <div class="form-group">
            <label for="total_amount">Total</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" value="{{ $order->total_amount }}"
                disabled>
        </div>
        <div class="form-group">
            <label for="created_at">Fecha</label>
            <input type="text" class="form-control" id="created_at" name="created_at" value="{{ $order->created_at }}"
                disabled>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Subtotal</th>

                    @if($order->status == 'pending')
                        <th scope="col">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                {{-- Por cada order --}}
                @foreach ($order->orderLines as $orderLine)
                    <tr>
                        <th>{{ $orderLine->id }}</th>
                        <td>{{ $orderLine->type }}</td>
                        @if($orderLine->type == 'coupon')
                            <td>{{ $orderLine->cartCode }}</td>
                        @else
                            <td>{{ $orderLine->book->name }}</td>
                        @endif
                        <td>{{ $orderLine->price }} €</td>
                        <td>{{ $orderLine->quantity }}</td>
                        <td>{{ $orderLine->subtotal }} €</td>
                        <td>
                            @if($order->status == 'pending')
                                <form></form>
                                <form action="{{ route('orders.destroyOrderLine',[ $order->id, $orderLine->id]) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                                </form>
                                <a class="btn btn-secondary btn-sm edit-btn edit_order_line_button"
                                    data-order-line-id="{{ $orderLine->id }}"
                                    data-order-line-type="{{ $orderLine->type }}"
                                    data-order-line-book-id="{{ $orderLine->book_id }}"
                                    data-order-line-quantity="{{ $orderLine->quantity }}"
                                ><i class="fas fa-edit"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Guardar Pedido</button>
    </form>
    <br>
    <section>
        <br>
        @if($order->status == 'pending')
            <button type="button" class="btn btn-primary" id="add_order_line">Añadir Línea</button>
        @endif
        <article class="add_order_line" style="display: none">
            <h3>Añadir línea de pedido</h3>
            <form action="{{ route('orders.addOrderLine', $order->id) }}" method="POST" class="book_form">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="type">Tipo</label>
                    <select class="form-control" id="type" name="type">
                        <option value="book">Libro</option>
                        <option value="coupon">Cupón</option>
                    </select>
                </div>
                <div class="form-group cupon_form" style="display: none;">
                    <label for="coupon">Cupón</label>
                    <input type="text" class="form-control" id="coupon" name="coupon">
                </div>
                <div class="form-group book_form control">
                    <div class="form_field">
                        <label for="book_id">Libro</label>
                        <select class="form-control" id="book_id" name="book_id">
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}">{{ $book->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_field">
                        <label for="quantity">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity">
                    </div>
                </div>
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit" class="btn btn-primary">Añadir Línea</button>
                <button type="button" class="btn btn-danger" id="cancel_add_order_line">Cancelar</button>
            </form>
        </article>
        <article class="edit_order_line" style="display: none">
            <h3>Editar línea de pedido</h3>
            <form action="{{ route('orders.editOrderLine', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="type_edit">Tipo</label>
                    <select class="form-control" id="type_edit" name="type" disabled>
                        <option value="book">Libro</option>
                        <option value="coupon">Cupón</option>
                    </select>
                </div>
                <div class="form-group cupon_form" style="display: none;">
                    <label for="coupon_edit">Cupón</label>
                    <input type="text" class="form-control" id="coupon_edit" name="coupon">
                </div>
                <div class="form-group book_form">
                    <div class="form_field">
                        <label for="book_id_edit">Libro</label>
                        <select class="form-control" id="book_id_edit" name="book_id" disabled>
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}">{{ $book->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form_field">
                        <label for="quantity">Cantidad</label>
                        <input type="number" class="form-control" id="quantity_edit" name="quantity">
                    </div>
                </div>
                <input type="hidden" name="order_id_edit" value="{{ $order->id }}">
                <input type="hidden" name="order_line_id_edit">
                <button type="submit" class="btn btn-primary">Editar Línea</button>
            </form>

        </article>
    </section>

    <script>
        document.getElementById('type').addEventListener('change', function () {
            if (this.value === 'coupon') {
                document.querySelector('.book_form.control').style.display = 'none';
                document.querySelector('.cupon_form').style.display = 'block';
            } else {
                document.querySelector('.book_form').style.display = 'block';
                document.querySelector('.cupon_form').style.display = 'none';
            }
        });

        document.getElementById('add_order_line').addEventListener('click', function () {
            document.querySelector('.add_order_line').style.display = 'block';
            document.querySelector('#add_order_line').style.display = 'none';
            document.querySelector('.edit_order_line').style.display = 'none';
        });

        document.getElementById('cancel_add_order_line').addEventListener('click', function () {
            document.querySelector('.add_order_line').style.display = 'none';
            document.querySelector('#add_order_line').style.display = 'block';
        });

        document.querySelectorAll('.edit_order_line_button').forEach(function (element) {
            element.addEventListener('click', function () {
                document.querySelector('.edit_order_line').style.display = 'block';
                document.querySelector('.add_order_line').style.display = 'none';
                document.querySelector('#add_order_line').style.display = 'block';
                document.querySelector('.book_form').style.display = 'block';
                document.querySelector('.cupon_form').style.display = 'none';
                document.querySelector('#type_edit').value = element.getAttribute('data-order-line-type');
                document.querySelector('#quantity_edit').value = element.getAttribute('data-order-line-quantity');
                document.querySelector('#book_id_edit').value = element.getAttribute('data-order-line-book-id');
                document.querySelector('input[name="order_line_id_edit"]').value = element.getAttribute('data-order-line-id');

            });
        });



    </script>
@endsection
