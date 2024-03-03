@php use App\Models\Book; @endphp
@extends('main')
@section('title', 'Detalles Book')
@section('content')
    <h1>Detalles del Book</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-tag"></i> {{ $book->name }}</h5>
            <p class="card-text"><i class="fas fa-info-circle"></i> Descripción: {{ $book->description }}</p>
            <p class="card-text"><i class="fas fa-barcode"></i> ISBN: {{ $book->isbn }}</p>
            <p class="card-text"><i class="fas fa-user"></i> Autor: {{ $book->author }}</p>
            <p class="card-text"><i class="fas fa-building"></i> Editorial: {{ $book->publisher }}</p>
            <p class="card-text"><i class="fas fa-store"></i> Tienda: {{ $book->shop->name }}</p>
            <p class="card-text"><i class="fas fa-euro-sign"></i> Precio: {{ $book->price }}</p>
            <p class="card-text">
                @if($book->image != Book::$IMAGE_DEFAULT)
                    <img alt="Imagen del Book" class="img-fluid book-image" src="{{ asset('storage/' . $book->image) }}">
                @else
                    <img alt="Imagen por defecto" class="img-fluid book-image" src="{{ Book::$IMAGE_DEFAULT }}">
                @endif
            </p>
            <p class="card-text"><i class="fas fa-layer-group"></i> Stock: {{ $book->stock }}</p>
            <p class="card-text"><i class="fas fa-folder-open"></i> Categoría: {{ $book->category_name }}</p>
        </div>
    </div>

    <br/>
    @if(auth()->check() && auth()->user()->hasVerifiedEmail() && $book->stock > 0)
        <form action="{{ route('cart.handle') }}" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="book_id" value="{{ $book->id }}">
            <input type="hidden" name="action" value="add">
            <button type="submit" class="btn btn-success btn-sm">Agregar al carrito <i class="fas fa-cart-plus"></i></button>
        </form>
    @endif

    <br/><br/>
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-secondary" href="{{ route('books.edit', $book->id) }}"><i class="fas fa-edit"></i> Editar</a>
        <a class="btn btn-info" href="{{ route('books.editImage', $book->id) }}"><i class="fas fa-image"></i> Editar Imagen</a>
    @endif
    <br/><br/>

@endsection
