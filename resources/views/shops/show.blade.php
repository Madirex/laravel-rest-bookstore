@php use App\Models\Shop; @endphp
@php use App\Models\Book; @endphp
@extends('main')
@section('title', 'Tiendas CRUD')
@section('content')
    <br/>
    <h1 class="card-title"><i class="fas fa-store"></i> {{ $shop->name }}</h1>
    <br/>

    @if (count($books) == 0)
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle"></i> No hay libros en esta tienda.
        </div>
    @endif

    @if (count($books) > 0)
        <form action="{{ route('shops.show', $shop->id) }}" class="mb-3" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control" id="search" name="search" placeholder="Nombre">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        <div class="row">
            @foreach($books as $book)
                <div class="col-md-4">
                    <a href="{{ route('books.show', $book->id) }}" class="book-link">
                        <div class="card">
                            <div class="book-header">
                                <img class="book-image"
                                     src="{{ $book->image != Book::$IMAGE_DEFAULT ? asset('storage/' . $book->image) : Book::$IMAGE_DEFAULT }}"
                                     alt="Imagen del Book"
                                     style="width: 100%; height: auto; display:block; margin:auto; max-width: 200px">
                                <p class="book-stock"><i class="fas fa-box-open"></i> {{ $book->stock }}</p>
                            </div>
                            <div class="card-body">
                                <h2 class="book-name">{{ $book->name }}</h2>
                                <p class="book-price"><i class="fas fa-tag"></i> {{ $book->price }}</p>
                                <p class="book-isbn"><i class="fas fa-barcode"></i> {{ $book->isbn }}</p>
                                <p class="book-author"><i class="fas fa-user"></i> {{ $book->author }}</p>
                                <p class="book-publisher"><i class="fas fa-building"></i> {{ $book->publisher }}</p>
                                <p class="card-text"><i class="fas fa-store"></i> {{ $book->shop->name }}</p>
                                <p class="book-description">{{ Illuminate\Support\Str::limit($book->description, 100, '...') }}</p>
                                <p class="book-category">Categoría: {{ $book->category_name }}</p>
                                <div class="book-actions">
                                    <a class="btn btn-primary btn-lg btn-block" href="{{ route('books.show', $book->id) }}"><i class="fas fa-info-circle"></i> Detalles</a>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
    <br/>

    <div class="pagination-container">
        {{ $books->links('pagination::bootstrap-4') }}
    </div>

    {{-- Si el usuario es administrador --}}
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-secondary" href="{{ route('shops.edit', $shop->id) }}"><i class="fas fa-edit"></i> Editar</a>
    @endif

@endsection
