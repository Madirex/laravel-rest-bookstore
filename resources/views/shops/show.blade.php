@php use App\Models\Shop; @endphp
@extends('main')
@section('title', 'Detalles de la tienda')
@section('content')
    <br/>
    <h1>Detalles de la tienda</h1>
    @if($shop)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-tag"></i> {{ $shop->name }}</h5>
                <p class="card-text"><i class="fas fa-calendar-alt"></i> Fecha de creación: {{ $shop->created_at }}</p>
                <p class="card-text"><i class="fas fa-sync-alt"></i> Fecha de actualización: {{ $shop->updated_at }}</p>
            </div>
        </div>

        <br/>

        {{-- Si el usuario es administrador --}}
        @if(auth()->check() && auth()->user()->hasRole('admin'))
            <a class="btn btn-secondary" href="{{ route('shops.edit', $shop->id) }}"><i class="fas fa-edit"></i> Editar</a>
        @endif
    @else
        <p class='lead'><em>No se ha encontrado la tienda.</em></p>
    @endif
@endsection
