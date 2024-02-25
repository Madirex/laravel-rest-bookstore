<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img alt="Logo" class="d-inline-block align-text-top" height="30" src="favicon.ico" width="30">
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
                @if (auth()->check() && auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('books.create') }}">Nuevo libro</a>
                    </li>
                @endif
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}">Categorías</a>
                </li>
                @if (auth()->check() && auth()->user()->hasRole('admin'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.create') }}">Nueva categoría</a>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav ml-auto" style="flex-direction: column;">
                <li class="nav-item">
                    @guest
                        <a class="nav-link" href="{{ route('register') }}">Registro</a>
                    @else
                        <div class="nav-username">{{ auth()->user()->name }}</div>
                    @endguest
                </li>
                <li class="nav-item">
                    @guest
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    @else
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">Logout</button>
                        </form>
                    @endguest
                </li>
            </ul>
        </div>
    </nav>
</header>
