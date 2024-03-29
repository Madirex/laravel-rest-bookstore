<?php

namespace App\Models;

use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Clase User
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public static $IMAGE_DEFAULT = 'images/user.png';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'surname',
        'username',
        'isDeleted',
        'phone',
        'image',
        'cart',
        'orders',
        'money'
    ];

    /**
     * Relación con la tabla Address
     * @return mixed mixed
     */
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Comprueba si el usuario tiene el rol especificado
     * @param $role string de rol
     * @return bool ¿role presente?
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Busca por username/email de User
     * @param $query mixed consulta
     * @param $search string búsqueda
     * @return mixed mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(username) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(email) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom);
    }
}
