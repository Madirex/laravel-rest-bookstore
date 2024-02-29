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
            <div class="card-header">{{ __('Reset Password') }}</div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="custom-form">
                    @csrf

                    <div class="custom-input-group">
                        <label for="email" class="custom-label">{{ __('Email') }}</label>
                        <input id="email" type="email" class="custom-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="custom-button-group">
                        <button type="submit" class="custom-button">
                            {{ __('Send Password Reset Link') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br/>
</div>
</body>
@include('footer')

<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        var emailInput = document.querySelector('#email');
        emailInput.value = emailInput.value.toLowerCase();
    });
</script>
