@php use App\Models\Category; @endphp
@extends('main')
@section('title', 'Detalles de la categoría')
@section('content')
    <br/>
    <h1>Detalles de la categoría</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-tag"></i> {{ $category->name }}</h5>
            <br/>
            <p class="card-text"><i class="fas fa-calendar-alt"></i> Fecha de creación: {{ $category->created_at }}</p>
            <p class="card-text"><i class="fas fa-sync-alt"></i> Fecha de actualización: {{ $category->updated_at }}</p>
        </div>
    </div>

    <br/>

    <a class="btn btn-primary" href="{{ route('categories.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>

@endsection
