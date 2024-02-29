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
<br/>
<div class="custom-container">
    <div class="custom-row">
        <div class="custom-card">
            <div class="card-header">{{ __('Registro') }}</div>

            <div class="card-body">
                <form method="POST" action="{{ route('register') }}" class="custom-form">
                    @csrf

                    <div class="custom-input-group">
                        <label for="username" class="custom-label">{{ __('Nombre de usuario') }}</label>
                        <input id="username" type="text" class="custom-input @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                        @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <label for="name" class="custom-label">{{ __('Nombre') }}</label>
                        <input id="name" type="text" class="custom-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <label for="surname" class="custom-label">{{ __('Apellidos') }}</label>
                        <input id="surname" type="text" class="custom-input @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required autocomplete="surname" autofocus>
                        @error('surname')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <label for="phone" class="custom-label">{{ __('Teléfono') }}</label>
                        <input id="phone" type="text" class="custom-input @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <label for="email" class="custom-label">{{ __('Email') }}</label>
                        <input id="email" type="email" class="custom-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <label for="password" class="custom-label">{{ __('Contraseña') }}</label>
                        <input id="password" type="password" class="custom-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="custom-input-group">
                        <label for="password-confirm" class="custom-label">{{ __('Confirmar contraseña') }}</label>
                        <input id="password-confirm" type="password" class="custom-input" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="custom-button-group">
                        <button type="submit" class="custom-button">
                            {{ __('Registro') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<br/>
</body>
@include('footer')
