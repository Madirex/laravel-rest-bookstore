<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailCustom extends VerifyEmailBase
{
    /**
     * Get the verification mail message for the given URL.
     *
     * @param string $verificationUrl
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($verificationUrl)
    {
        return (new MailMessage)
            ->subject('Nullers Bookstore - Verificación de dirección de correo electrónico')
            ->line('Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico.')
            ->action('Verificar dirección de correo electrónico', $verificationUrl)
            ->line('Si no creaste una cuenta, no se requiere ninguna acción adicional.');
    }
}
