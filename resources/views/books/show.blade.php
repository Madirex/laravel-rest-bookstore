@php use App\Models\Book; @endphp

@extends('main')

@section('title', 'Detalles Book')

@section('content')
    <br/>
    <h1><i class="fas fa-tag"></i> {{ $book->name }}</h1>
    <br/>
    <div class="card">
        <div class="row">
            <div class="col-md-4">
                @if($book->image != Book::$IMAGE_DEFAULT)
                    <img alt="Imagen del Book" style="padding-left: 40px;padding-top: 40px;" class="img-fluid book-image" src="{{ asset('storage/' . $book->image) }}">
                @else
                    <img alt="Imagen por defecto" style="padding-left: 40px;padding-top: 40px;" class="img-fluid book-image" src="{{ Book::$IMAGE_DEFAULT }}">
                @endif
                <div class="row-cols-bg" style="padding-left: 40px;padding-top: 40px; padding-bottom: 40px;">
                    <p class="card-text"><i class="fas fa-user"></i> Autor: {{ $book->author }}</p>
                    <p class="card-text"><i class="fas fa-building"></i> Editorial: {{ $book->publisher }}</p>
                    <p class="card-text"><i class="fas fa-barcode"></i> ISBN: {{ $book->isbn }}</p>
                </div>
            </div>
            <div class="col-md-8" >
                <div class="card-body" style="padding-top: 60px">
                    <p><i class="fas fa-store"></i> Tienda: <a style="text-decoration:none;" href="{{ route('shops.show', $book->shop->id) }}" class="card-text">{{ $book->shop->name }}</a></p>
                    <p><i class="fas fa-tag"></i> Categoría: <a style="text-decoration:none;" href="{{ route('categories.show', $book->category->id) }}" class="card-text">{{ $book->category_name }}</a></p>
                    <p class="card-text"><i class="fas fa-info-circle"></i> Descripción: {{ $book->description }}</p>
                    <!-- categoría con enlace -->
                    <p><i class="fas fa-euro-sign"></i> {{ $book->price }}</p>
                    <p><i class="fas fa-layer-group"></i> Stock: {{ $book->stock }}</p>
                </div>
                @if(auth()->check() && auth()->user()->hasVerifiedEmail() && $book->stock > 0)
                    <form action="{{ route('cart.handle') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-success btn-lg">Agregar al carrito <i class="fas fa-cart-plus"></i></button>
                    </form>
                @endif
            </div>

        </div>
    </div>

    <br/>


    <br/><br/>
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-secondary" href="{{ route('books.edit', $book->id) }}"><i class="fas fa-edit"></i> Editar</a>
        <a class="btn btn-info" href="{{ route('books.editImage', $book->id) }}"><i class="fas fa-image"></i> Editar Imagen</a>
    @endif
    <br/><br/>

@endsection
