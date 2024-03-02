@php use App\Models\CartCode; @endphp
@extends('main')

@section('title', 'Crear código de tienda - NULLERS')

@section('content')
    <h1>Crear código de tienda</h1>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <br/>
    @endif

    <form action="{{ route("cartcodes.store") }}" method="post">
        @csrf
        <div class="form-group">
            <label for="code">Código:</label>
            <input class="form-control" id="code" name="code" type="text" required>
        </div>
        <!-- Tipo de descuento -->
        <div class="form-group">
            <label>Tipo de descuento:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="discount_type" id="percent_discount_radio" value="percent" checked>
                <label class="form-check-label" for="percent_discount_radio">
                    Descuento (porcentaje de 1 a 100)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="discount_type" id="fixed_discount_radio" value="fixed">
                <label class="form-check-label" for="fixed_discount_radio">
                    Descuento fijo
                </label>
            </div>
        </div>

        <!-- Valor del descuento por porcentaje -->
        <div class="form-group" id="percent_discount_group">
            <label for="percent_discount">Valor del descuento (porcentaje):</label>
            <input class="form-control" id="percent_discount" step="0.01" name="percent_discount" type="number" min="0" max="100" required>
        </div>

        <!-- Valor del descuento fijo -->
        <div class="form-group" id="fixed_discount_group" style="display: none;">
            <label for="fixed_discount">Valor del descuento fijo:</label>
            <input class="form-control" id="fixed_discount" step="0.01" name="fixed_discount" type="number" min="0" required>
        </div>
        <!-- available_uses -->
        <div class="form-group">
            <label for="available_uses">Usos disponibles:</label>
            <input class="form-control" id="available_uses" name="available_uses" type="number" min="1" required>
        </div>

        <!-- expiration_date -->
        <div class="form-group">
            <label for="expiration_date">Fecha de expiración:</label>
            <input class="form-control" id="expiration_date" name="expiration_date" type="date" required>
        </div>

        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('cartcodes.index') }}">Volver</a>
    </form>

    <script>
        window.onload = function() {
            if (document.getElementById('percent_discount_radio').checked) {
                document.getElementById('fixed_discount').value = 0;
            } else {
                document.getElementById('percent_discount').value = 0;
            }
        };
        document.getElementById('percent_discount_radio').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('percent_discount_group').style.display = 'block';
                document.getElementById('fixed_discount_group').style.display = 'none';
                document.getElementById('fixed_discount').value = 0;
            }
        });

        document.getElementById('fixed_discount_radio').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('fixed_discount_group').style.display = 'block';
                document.getElementById('percent_discount_group').style.display = 'none';
                document.getElementById('percent_discount').value = 0;
            }
        });
    </script>

@endsection
