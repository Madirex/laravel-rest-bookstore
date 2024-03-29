@php use App\Models\Book; @endphp
@extends('main')

@section('title', 'Crear Libro - NULLERS')

@section('content')
    <h1>Crear Libro</h1>

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

    <form action="{{ route("books.store") }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input class="form-control" id="name" name="name" type="text" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="isbn">ISBN:</label>
            <input class="form-control" id="isbn" name="isbn" type="text" value="{{ old('isbn') }}" required>
        </div>
        <div class="form-group">
            <label for="author">Autor:</label>
            <input class="form-control" id="author" name="author" type="text" value="{{ old('author') }}" required>
        </div>
        <div class="form-group">
            <label for="publisher">Editorial:</label>
            <input class="form-control" id="publisher" name="publisher" type="text" value="{{ old('publisher') }}" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea class="form-control" id="description" name="description" required>{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="price">Precio:</label>
            <input class="form-control" id="price" min="0.0" name="price" step="0.01" type="number" required
                   value="{{ old('price', 0) }}" >
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input class="form-control" id="stock" min="1" name="stock" type="number" required value="{{ old('stock', 1) }}">
        </div>
        <div class="form-group">
            <label for="category_name">Categoría:</label>
            <select class="form-control" id="category_name" name="category_name" required>
                <option value="">Seleccione una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->name }}" {{ old('category_name') == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="shop_id">ID de la tienda:</label>
            <input class="form-control" id="shop_id" min="1" name="shop_id" type="number" required value="{{ old('shop_id', 1) }}">
        </div>

        <button class="btn btn-primary" type="submit">Crear</button>
    </form>

@endsection
