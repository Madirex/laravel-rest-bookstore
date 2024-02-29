<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // Convertir el correo electrónico a minúsculas antes de buscar el usuario
        $email = strtolower($request->email);

        // Buscar todos los usuarios
        $users = User::all();

        // Buscar el usuario con el correo electrónico proporcionado de manera insensible a mayúsculas y minúsculas
        $user = $users->first(function ($user) use ($email) {
            return strtolower($user->email) === $email;
        });

        if (!$user) {
            // Si no se encuentra el usuario, manejar el error
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        // Si se encuentra el usuario, enviar el enlace de restablecimiento de contraseña
        $response = $this->broker()->sendResetLink(['email' => $user->email]);

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}
