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

    <section>
        <br/>
        <button type="button" class="btn btn-primary" style="display: none" id="add_order_line">Añadir Línea</button>
        <button type="button" class="btn btn-primary" style="display: none" id="add_cart_code">Editar código de descuento</button>
        <br/>
        <br/>
        <!-- en el caso de que haya un cart_code que no sea null, agregar botón eliminar a ruta removeCoupon -->
        @if($order->cartCode)
            <form action="{{ route('orders.removeCoupon', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-danger" id="remove_cart_code">Eliminar código de descuento</button>
            </form>
        @endif
        <article class="add_order_line" style="display: none">
            <h3>Añadir línea de pedido</h3>
            <form action="{{ route('orders.addOrderLine', $order->id) }}" method="POST" class="book_form">
                @csrf
                @method('PATCH')
                <div class="form-group book_form control">
                    <div class="form_field">
                        <label for="book_id">Libro</label>
                        <select class="form-control" id="book_id" name="book_id">
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}">{{ $book->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <br/>
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



        <article class="add_cart_code" style="display: none">
            <h3>Editar código de descuento</h3>
            <form action="{{ route('orders.addCouponToOrder', $order->id) }}" method="POST" class="book_form">
                @csrf
                @method('PUT')
                <div class="form-group book_form control">
                    <div class="form-group">
                        <label for="cart_code">Código de descuento</label>
                        <input type="text" class="form-control" id="cart_code" name="cart_code"
                               value="{{ old('cart_code', $order->cartCode ? $order->cartCode->code : '') }}">
                        <br/>
                    </div>
                </div>
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit" class="btn btn-primary">Aplicar Código</button>
                <button type="button" class="btn btn-danger" id="cancel_add_cart_code">Cancelar</button>
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
                <div class="form-group coupon_form" style="display: none;">
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
    <br>
    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="status">Estado</label>
            <select class="form-control" id="status" name="status">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Enviado</option>
                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregado</option>
            </select>
        </div>


        <div class="form-group">
            <label for="user_id">Usuario id</label>
            <input type="text" class="form-control" id="user_id" name="user_id"
                   value="{{ old('user_id', $order->user_id) }}" disabled>
        </div>
        <div class="form-group">
            <label for="total_amount">Total</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount"
                   value="{{ old('total_amount', $order->total_amount) }}"
                   disabled>
        </div>
        <div class="form-group">
            <label for="created_at">Fecha</label>
            <input type="text" class="form-control" id="created_at" name="created_at"
                   value="{{ old('created_at', $order->created_at) }}"
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
                <th scope="col" id="actionsColumn">Acciones</th>
            </tr>
            </thead>
            <tbody>
            {{-- Por cada order --}}
            @foreach ($order->orderLines as $orderLine)
                <tr>
                    <th>{{ $orderLine->id }}</th>
                    <td>{{ $orderLine->type }}</td>
                    <td>{{ $orderLine->book->name }}</td>
                    <td>{{ $orderLine->price }} €</td>
                    <td>{{ $orderLine->quantity }}</td>
                    <td>{{ $orderLine->subtotal }} €</td>
                    <td>
                        <form></form> <!-- FIXME: form vacío necesario para eliminación de primer pedido, mejorar estructura -->
                        @if($order->status == 'pending')
                            <!-- Enlace de eliminación con modal de confirmación -->
                            <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmDeleteModal{{ $orderLine->id }}">
                                <i class="fas fa-trash-alt"></i>
                            </a>

                            <!-- Modal de Confirmación de eliminación -->
                            <div class="modal fade" id="confirmDeleteModal{{ $orderLine->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Estás seguro de que deseas eliminar esta línea de pedido?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                                            <!-- Formulario para eliminar el elemento -->
                                            <form id="delete-form-{{ $orderLine->id }}" action="{{ route('orders.destroyOrderLine', ['order' => $orderLine->order->id, 'orderLine' => $orderLine->id]) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <button type="button" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $orderLine->id }}').submit();">Borrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br/>
        <button type="submit" class="btn btn-primary">Guardar Pedido</button>
    </form>



    <script>
        document.getElementById('status').addEventListener('change', function () {
            if (this.value === 'pending') {
                document.getElementById('actionsColumn').style.display = 'table-cell';
                document.getElementById('add_order_line').style.display = 'inline-block';
                document.getElementById('add_cart_code').style.display = 'inline-block';
            } else {
                document.getElementById('actionsColumn').style.display = 'none';
                document.getElementById('add_order_line').style.display = 'none';
                document.getElementById('add_cart_code').style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('.book_form.control').style.display = 'block';
            if (document.getElementById('status').value === 'pending') {
                document.getElementById('actionsColumn').style.display = 'table-cell';
                document.getElementById('add_order_line').style.display = 'inline-block';
                document.getElementById('add_cart_code').style.display = 'inline-block';
            } else {
                document.getElementById('actionsColumn').style.display = 'none';
                document.getElementById('add_order_line').style.display = 'none';
                document.getElementById('add_cart_code').style.display = 'none';
            }
        })

        /* add */
        document.getElementById('add_order_line').addEventListener('click', function () {
            document.querySelector('.add_order_line').style.display = 'inline-block';
            document.querySelector('#add_order_line').style.display = 'none';
            document.querySelector('#add_cart_code').style.display = 'none';
            document.querySelector('#remove_cart_code').style.display = 'none';
            document.querySelector('.edit_order_line').style.display = 'none';
        });

        document.getElementById('add_cart_code').addEventListener('click', function () {
            document.querySelector('.add_cart_code').style.display = 'inline-block';
            document.querySelector('#add_cart_code').style.display = 'none';
            document.querySelector('#remove_cart_code').style.display = 'none';
            document.querySelector('#add_order_line').style.display = 'none';
        });

        /* cancel */
        document.getElementById('cancel_add_order_line').addEventListener('click', function () {
            document.querySelector('.add_order_line').style.display = 'none';
            document.querySelector('#add_order_line').style.display = 'inline-block';
            document.querySelector('#add_cart_code').style.display = 'inline-block';
            document.querySelector('#remove_cart_code').style.display = 'inline-block';
        });

        document.getElementById('cancel_add_cart_code').addEventListener('click', function () {
            document.querySelector('.add_cart_code').style.display = 'none';
            document.querySelector('#add_cart_code').style.display = 'inline-block';
            document.querySelector('#remove_cart_code').style.display = 'inline-block';
            document.querySelector('#add_order_line').style.display = 'inline-block';
        });

        /* edit */
        document.querySelectorAll('.edit_order_line_button').forEach(function (element) {
            element.addEventListener('click', function () {
                document.querySelector('.edit_order_line').style.display = 'block';
                document.querySelector('.add_order_line').style.display = 'none';
                document.querySelector('#add_order_line').style.display = 'block';
                document.querySelector('.book_form').style.display = 'block';
                document.querySelector('.coupon_form').style.display = 'none';
                document.querySelector('#type_edit').value = element.getAttribute('data-order-line-type');
                document.querySelector('#quantity_edit').value = element.getAttribute('data-order-line-quantity');
                document.querySelector('#book_id_edit').value = element.getAttribute('data-order-line-book-id');
                document.querySelector('input[name="order_line_id_edit"]').value = element.getAttribute('data-order-line-id');

            });
        });
    </script>
@endsection
