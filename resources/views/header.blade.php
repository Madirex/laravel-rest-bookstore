@php use App\Models\User; @endphp
@php use App\Http\Controllers\CartController; @endphp

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="header-container">
            <a style="display:block; margin:5px;"> </a>
            <a class="navbar-brand" href="{{ url('/') }}">
                <img class="d-inline-block align-text-top" height="30" src="/favicon.ico" width="30">
                BookStore
            </a>
            <a style="display:block; margin:5px;"> </a>
        </div>
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
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('shops.index') }}">Tiendas</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    @if (auth()->check() && auth()->user()->hasRole('admin'))
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Administrar
                        </a>
                        <div class="dropdown-menu" aria-labelledby="adminDropdown">
                            <a class="dropdown-item" href="{{ route('cartcodes.index') }}">Gestionar códigos de
                                tienda</a>
                            <a class="dropdown-item" href="{{ route('users.admin.index') }}">Gestionar usuarios</a>
                            <a class="dropdown-item" href="{{ route('addresses.index') }}">Gestionar direcciones</a>
                            <a class="dropdown-item" href="{{ route('orders.index') }}">Gestionar pedidos</a>
                        </div>
                    @endif
                </li>
            </ul>


            <br/>
            <br/>

            <ul class="navbar-nav ml-auto" style="flex-direction: column;">

                <li class="nav-item ml-auto d-block m-auto">
                    @if(auth()->check())
                        @if(auth()->user()->hasVerifiedEmail())
                            <div class="nav-username">
                                @if (!request()->routeIs('cart.cart') && !request()->routeIs('cart.add')&& !request()->routeIs('cart.remove'))
                                    <div class="nav-container">
                                        <a class="nav-link" href="{{ route('cart.cart') }}">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span class="badge badge-light">
            {{ (app(CartController::class)->itemCount(request()) > 99) ? '99+' : app(CartController::class)->itemCount(request()) }}
        </span>
                                        </a>
                                    </div>
                                @endif
                                <div class="nav-container">
                                    <a style="display:block; margin:5px;"> </a>
                                    <a class="username-nav-content" href="{{ route('users.profile') }}">
                                        @if(auth()->user()->image != User::$IMAGE_DEFAULT)
                                            <img src="{{ asset('storage/' . auth()->user()->image) }}" class="rounded"
                                                 width="30" height="30">
                                        @else
                                            <img src="{{ '/' . User::$IMAGE_DEFAULT }}"
                                                 style="border: 2px solid black; background-color: white;" class="rounded"
                                                 width="30" height="30">
                                        @endif
                                        {{ ucfirst(strtolower(auth()->user()->username)) }}
                                    </a>
                                    <a style="display:block; margin:5px;"> </a>
                                </div>
                                    <div class="nav-container">
                                        @if(Auth::check())
                                            <a href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                               class="nav-link">
                                                <i class="fas fa-sign-out-alt" style="color: #b72424;"></i>
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        @endif
                                    </div>
                            </div>
                        @else
                            <a href="{{route('verification.notice')}}"
                               style="color:white;font-size: x-small;text-decoration: underline;">Email pendiente de
                                verificación</a>
                        @endif
                    @else
                        <a class="nav-link" href="{{ route('register') }}">Registro</a>
                    @endif
                </li>
                <li class="nav-item">
                    @guest
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    @endguest
                </li>
            </ul>
        </div>
    </nav>
</header>
