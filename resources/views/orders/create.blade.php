@extends('main')
@section('title', 'Pedidos - NULLERS')
@section('content')
<br/>
    <h1>Nuevo pedido</h1>
    <form action="{{ route('orders.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="user_id">Usuario</label>
            <select class="form-control" id="user_id" name="user_id">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
    <br/>
    <a class="btn btn-secondary" href={{ route('orders.index') }}>Volver</a>
@endsection
