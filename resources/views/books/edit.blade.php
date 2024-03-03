@php use App\Models\Book; @endphp
@extends('main')
@section('title', 'Editar Book')

@section('content')
    <h1>Editar Book</h1>

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

    <form action="{{ route("books.update", $book->id) }}" method="post">
        @csrf
        @method('PUT')
        Puedes usar la función `old` de Laravel para mantener los valores de los campos después de un error de validación. Aquí está cómo puedes hacerlo:

        ```blade
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input class="form-control" id="name" name="name" type="text" required value="{{ old('name', $book->name) }}">
        </div>
        <div class="form-group">
            <label for="isbn">ISBN:</label>
            <input class="form-control" id="isbn" name="isbn" type="text" required value="{{ old('isbn', $book->isbn) }}">
        </div>
        <div class="form-group">
            <label for="author">Autor:</label>
            <input class="form-control" id="author" name="author" type="text" required value="{{ old('author', $book->author) }}">
        </div>
        <div class="form-group">
            <label for="publisher">Editorial:</label>
            <input class="form-control" id="publisher" name="publisher" type="text" required value="{{ old('publisher', $book->publisher) }}">
        </div>
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea class="form-control" id="description" name="description" required>{{ old('description', $book->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="price">Precio:</label>
            <input class="form-control" id="price" min="0.0" name="price" step="0.01" type="number" required
                   value="{{ old('price', $book->price) }}">
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input class="form-control" id="stock" min="0" name="stock" type="number" required
                   value="{{ old('stock', $book->stock) }}">
        </div>
        <div class="form-group">
            <label for="category_name">Categoría:</label>
            <select class="form-control" id="category_name" name="category_name" required>
                <option value="">Seleccione una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->name }}" {{ old('category_name', $book->category_name) == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="shop_id">ID de la tienda:</label>
            <input class="form-control" id="shop_id" min="1" name="shop_id" type="number" required value="{{ old('shop_id', $book->shop->id) }}">
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
    </form>

@endsection
