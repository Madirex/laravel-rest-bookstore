@php use App\Models\Book; @endphp
@extends('main')
@section('title', 'Editar imagen de Book')

@section('content')
    <h1><i class="bi bi-pencil-square"></i> Editar imagen de Book</h1>
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
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-tag"></i> {{ $book->name }}</h5>
            <p class="card-text"><i class="fas fa-image"></i> Imagen actual:
                @if($book->image != Book::$IMAGE_DEFAULT)
                    <img alt="Imagen del Book" class="img-fluid book-image" src="{{ asset('storage/' . $book->image) }}">
                @else
                    <img alt="Imagen por defecto" class="img-fluid book-image" src="{{ Book::$IMAGE_DEFAULT }}">
                @endif
            </p>
            <form id="updateImageForm" action="{{ route("books.updateImage", $book->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="image"><i class="bi bi-card-image"></i> Nueva imagen:</label>
                    <input accept="image/*" class="form-control-file" id="image" name="image" required type="file" aria-describedby="imageHelp">
                    <small id="imageHelp" class="form-text text-muted">Por favor, sube una imagen para el Book.</small>
                </div>

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmUpdateModal"><i class="bi bi-check-circle-fill"></i> Actualizar</button>
            </form>
        </div>
    </div>

    <br/>

    <!-- Modal de Confirmación de actualización -->
    <div class="modal fade" id="confirmUpdateModal" tabindex="-1" role="dialog" aria-labelledby="confirmUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUpdateModalLabel">Confirmar Actualización</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres actualizar la imagen?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('updateImageForm').submit();">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
