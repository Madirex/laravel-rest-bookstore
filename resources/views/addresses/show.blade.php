@php use App\Models\Address; @endphp
@extends('main')
@section('title', 'Detalles de la dirección')
@section('content')
    <br/>
    <h1>Detalles de la dirección</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-road"></i> {{ $address->street }} {{ $address->number }}</h5>
            <p class="card-text"><i class="fas fa-city"></i> Ciudad: {{ $address->city }}</p>
            <p class="card-text"><i class="fas fa-flag"></i> Provincia: {{ $address->province }}</p>
            <p class="card-text"><i class="fas fa-globe"></i> País: {{ $address->country }}</p>
            <p class="card-text"><i class="fas fa-mail-bulk"></i> Código postal: {{ $address->postal_code }}</p>
        </div>
    </div>

    <br/>

    <a class="btn btn-primary" href="{{ route('addresses.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>

    {{-- Si el usuario es administrador --}}
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <a class="btn btn-secondary" href="{{ route('addresses.edit', $address->id) }}"><i class="fas fa-edit"></i> Editar</a>
    @endif
@endsection
