@php use App\Models\User; @endphp

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img class="d-inline-block align-text-top" height="30" src="/favicon.ico" width="30">
            BookStore
        </a>
        <button aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"
                data-target="#navbarNav" data-toggle="collapse" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Libros</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}">Categorías</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    @if (auth()->check() && auth()->user()->hasRole('admin'))
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        Administrar
                    </a>
                    <div class="dropdown-menu" aria-labelledby="adminDropdown">
                        <a class="dropdown-item" href="{{ route('cartcodes.index') }}">Gestionar códigos de tienda</a>
                        <a class="dropdown-item" href="{{ route('users.admin.index') }}">Gestionar usuarios</a>
                        <a class="dropdown-item" href="{{ route('addresses.index') }}">Gestionar direcciones</a>
                    </div>
                    @endif
                </li>
                <!--cuando estoy en /cart no parece este icono-->
                @if (!request()->routeIs('cart.cart') && !request()->routeIs('cart.add')&& !request()->routeIs('cart.remove'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.cart') }}">
                        <i class="fas fa-shopping-cart"></i>
                        Carrito
                    </a>
                </li>
                @endif
            </ul>


            <ul class="navbar-nav ml-auto" style="flex-direction: column;">
                <li class="nav-item">
                    @if(auth()->check())
                        @if(auth()->user()->hasVerifiedEmail())
                            <div class="nav-username">
                                <a href="{{ route('users.profile') }}">
                                    @if(auth()->user()->image != User::$IMAGE_DEFAULT)
                                        <img src="{{ asset('storage/' . auth()->user()->image) }}" class="rounded" width="30" height="30">
                                    @else
                                        <img src="{{ '/' . User::$IMAGE_DEFAULT }}" style="border: 2px solid black; background-color: white;" class="rounded" width="30" height="30">
                                    @endif
                                    {{ ucfirst(strtolower(auth()->user()->username)) }}
                                </a>
                            </div>
                        @else
                            <a href="{{route('verification.notice')}}" style="color:white;font-size: x-small;text-decoration: underline;">Email pendiente de verificación</a>
                        @endif
                    @else
                        <a class="nav-link" href="{{ route('register') }}">Registro</a>
                    @endif
                </li>
                <li class="nav-item">
                    @guest
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    @else
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link"
                                style="background: none; border: none; cursor: pointer; display:block; margin:auto">
                            Logout
                        </button>
                    </form>
                    @endguest
                </li>
            </ul>
        </div>
    </nav>
</header>
