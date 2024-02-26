@php use App\Models\CartCode; @endphp
@extends('main')
@section('title', 'Detalles del código de tienda - NULLERS')
@section('content')
    <h1>Detalles del código de tienda</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-tag"></i> {{ $cartcode->code }}</h5>
            @if($cartcode->percent_discount > 0)
                <p class="card-text"><i class="fas fa-percent"></i> Descuento en porcentaje: {{ $cartcode->percent_discount }}%</p>
            @endif
            @if($cartcode->fixed_discount > 0)
                <p class="card-text"><i class="fas fa-dollar-sign"></i> Descuento fijo: {{ $cartcode->fixed_discount }}</p>
            @endif
            <p class="card-text"><i class="fas fa-box-open"></i> Usos disponibles: {{ $cartcode->available_uses }}</p>
            <p class="card-text"><i class="fas fa-calendar-alt"></i> Fecha de expiración: {{ $cartcode->expiration_date }}</p>
        </div>
    </div>

    <br/>

    <a class="btn btn-primary" href="{{ route('cartcodes.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
    <a class="btn btn-secondary" href="{{ route('cartcodes.edit', $cartcode->id) }}"><i class="fas fa-edit"></i> Editar</a>
@endsection
