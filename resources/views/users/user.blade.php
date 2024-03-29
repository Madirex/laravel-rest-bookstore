@php use App\Models\User; @endphp

@extends('main')
@section('title', 'Detalles del usuario')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <br/>
    <h1>Detalles del usuario</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user"></i> {{ $user->username }}</h5>
            @if($user->image != User::$IMAGE_DEFAULT)
                <img alt="Imagen de la cuenta" class="rounded float-left" width="150" height="150" style="margin: 15px"
                     src="{{ asset('storage/' . $user->image) }}">
            @else
                <img alt="Imagen por defecto" class="rounded float-left" width="150" height="150" style="margin: 15px"
                     src="{{ '/' . User::$IMAGE_DEFAULT }}">
            @endif
            <br/>
            <p class="card-text"><i class="fas fa-id-card"></i> Nombre: {{ $user->name }} {{ $user->surname }}</p>
            <p class="card-text"><i class="fas fa-envelope"></i> Email: {{ $user->email }}</p>
            <p class="card-text"><i class="fas fa-phone"></i> Teléfono: {{ $user->phone }}</p>
            <p class="card-text"><i class="fas fa-user-tag"></i> Rol: {{ $user->role }}</p>
        </div>
    </div>

    @if($user->address)
        <br/>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> Dirección</h5>
                <div class="card-body">
                    <h5 class="card-title"><i
                            class="fas fa-road"></i> {{ $user->address->street }} {{ $user->address->number }}</h5>
                    <p class="card-text"><i class="fas fa-city"></i> Ciudad: {{ $user->address->city }}</p>
                    <p class="card-text"><i class="fas fa-flag"></i> Provincia: {{ $user->address->province }}</p>
                    <p class="card-text"><i class="fas fa-globe"></i> País: {{ $user->address->country }}</p>
                    <p class="card-text"><i class="fas fa-mail-bulk"></i> Código
                        postal: {{ $user->address->postal_code }}</p>
                    <a class="btn btn-secondary" href="{{route('users.address.edit', $user) }}"><i class="fas fa-edit"></i> Editar dirección</a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAddressModal">
                        Eliminar dirección
                    </button>
                </div>
            </div>

            <div class="modal fade" id="deleteAddressModal" tabindex="-1" role="dialog" aria-labelledby="deleteAddressModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteAddressModalLabel">Eliminar dirección</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que quieres eliminar tu dirección? Esta acción no se puede deshacer.
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                            <form method="POST" action="{{ route('user.address.delete', $user->address->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @else
        <br/>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> Dirección</h5>
                <p class="card-text">No tienes una dirección registrada.</p>
                <a href="{{ route('users.address.create', $user) }}" class="btn btn-primary">Crear dirección</a>
            </div>
        </div>
    @endif

    <br/>

    <!-- ver dinero -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-wallet"></i> Dinero</h5>
            <!-- parseado a formato españa -->
            <p class="card-text"><i class="fas fa-coins"></i> {{ number_format($user->money, 2, ',', '.') }} €</p>
        </div>
    </div>

    <!-- Ver orders -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-receipt"></i> Pedidos</h5>
            <a href="{{ route('user.orders.index') }}" class="btn btn-primary">Ver pedidos</a>
        </div>
    </div>

    <br/>

    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Editar perfil</a>
    <a href="{{ route('users.editImage') }}" class="btn btn-info">Cambiar imagen</a>

    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
        Eliminar cuenta
    </button>
    <br/>
    <br/>

    <form method="POST" action="{{ route('password.reset.send') }}">
        <a href="{{ route('user.email.change.form') }}" class="btn btn-secondary">Cambiar correo</a>
        @csrf
        <button type="submit" class="btn btn-secondary">Cambiar contraseña</button>
    </form>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Eliminar cuenta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer.
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                    <form method="POST" action="/user">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
