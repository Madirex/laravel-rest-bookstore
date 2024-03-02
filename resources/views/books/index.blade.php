@php use App\Models\Book; @endphp
@extends('main')
@section('title', 'Libros - NULLERS')
@section('content')
    <br/>
    <h1>Listado de libros</h1>
    <form action="{{ route('books.index') }}" class="mb-3" method="get">
        @csrf
        <div class="input-group">
            <input type="text" class="form-control" id="search" name="search" placeholder="Nombre">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    {{-- Si hay registros --}}
    @if (count($books) > 0)
        <div class="book-container">
            {{-- Por cada book --}}
            {{-- Por cada book --}}
            @foreach ($books as $book)
                <a href="{{ route('books.show', $book->id) }}" class="book-link">
                    <div class="book-card">
                        <p class="book-price"><i class="fas fa-euro-sign"></i> {{ $book->price }}</p>
                        <div class="book-header">
                            <img class="book-image"
                                 src="{{ $book->image != Book::$IMAGE_DEFAULT ? asset('storage/' . $book->image) : Book::$IMAGE_DEFAULT }}"
                                 alt="Imagen del Book"
                                 onerror="this.onerror=null; this.src='http://localhost/images/books.bmp';">
                            <p class="book-stock"><i class="fas fa-box-open"></i> {{ $book->stock }}</p>
                        </div>
                        <h2 class="book-name">{{ $book->name }}</h2>
                        <p class="book-isbn"><i class="fas fa-barcode"></i> {{ $book->isbn }}</p>
                        <p class="book-author"><i class="fas fa-user"></i> {{ $book->author }}</p>
                        <p class="book-publisher"><i class="fas fa-building"></i> {{ $book->publisher }}</p>
                        <p class="book-description">{{ Illuminate\Support\Str::limit($book->description, 100, '...') }}</p>
                        <p class="book-category">Categoría: {{ $book->category_name }}</p>
                        <div class="book-actions">
                            <a class="btn btn-primary btn-sm" href="{{ route('books.show', $book->id) }}"><i
                                    class="fas fa-info-circle"></i></a>
                            @if(auth()->check() && auth()->user()->hasRole('admin'))
                                <a class="btn btn-secondary btn-sm" href="{{ route('books.edit', $book->id) }}"><i
                                        class="fas fa-edit"></i></a>
                                <a class="btn btn-info btn-sm" href="{{ route('books.editImage', $book->id) }}"><i
                                        class="fas fa-image"></i></a>
                                <a class="btn btn-danger btn-sm delete-btn" data-toggle="modal"
                                   data-target="#confirmDeleteModal{{ $book->id }}"><i class="fas fa-trash-alt"></i></a>
                            @endif
                            @if(auth()->check() && auth()->user()->hasVerifiedEmail() && $book->stock > 0)
                            <form action="{{ route('cart.handle') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-cart-plus"></i></button>
                            </form>
                            @endif
                        </div>
                    </div>
                </a>

                <!-- Modal de Confirmación de eliminación -->
                <div class="modal fade" id="confirmDeleteModal{{ $book->id }}" tabindex="-1" role="dialog"
                     aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas eliminar este elemento?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                                <!-- Formulario para eliminar el elemento -->
                                <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Borrar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class='lead'><em>No se han encontrado libros.</em></p>
    @endif

    <div class="pagination-container">
        {{ $books->links('pagination::bootstrap-4') }}
    </div>

    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-success" href={{ route('books.create') }}><i class="fas fa-plus"></i> Crear libro</a>
    @endif

@endsection
