<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Nullers Bookstore') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link href="{{ asset('/favicon.ico') }}" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
@include('header')

<body>
<div class="custom-container">
    <br/>
    <div class="custom-row">
        <div class="custom-card">
            <div class="card-header">{{ __('Login') }}</div>

            <!-- errors -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <br/>
            @endif

            <div class="card-body">
                <form method="POST" action="{{ route('login') }}" class="custom-form">
                    @csrf

                    <div class="custom-input-group">
                        <label for="login" class="custom-label">{{ __('Email o Nombre de Usuario') }}</label>
                        <input id="login" type="text" class="custom-input @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autocomplete="login" autofocus>
                        @error('login')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <label for="password" class="custom-label">{{ __('Contraseña') }}</label>
                        <input id="password" type="password" class="custom-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <div class="custom-checkbox">
                            <label class="custom-checkbox-label" for="remember">
                                <input class="custom-checkbox-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                {{ __('Recordar') }}
                            </label>
                        </div>
                    </div>
                    <div class="custom-button-group">
                        <button type="submit" class="custom-button">
                            {{ __('Login') }}
                        </button>
                    </div>
                    <div class="custom-button-group">
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Olvíde mi contraseña') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br/>
</div>
</body>
@include('footer')
