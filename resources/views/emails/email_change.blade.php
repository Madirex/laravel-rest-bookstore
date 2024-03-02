<body>
<h1>Confirmación de cambio de correo electrónico</h1>

<p>Por favor, haga clic en el siguiente enlace para confirmar su cambio de correo electrónico:</p>

<a href="{{ url('/user/email/confirm/' . $token) }}">Confirmar cambio de correo electrónico</a>

<p>Si no ha solicitado este cambio, por favor ignore este mensaje.</p>
</body>
