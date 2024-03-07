<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CartCode extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = ['id', 'code', 'percent_discount', 'fixed_discount', 'available_uses', 'expiration_date', 'is_deleted'];

    /**
     * Oculta los campos
     * @var string[] $hidden
     */
    protected $hidden = [
        'is_deleted',
    ];

    /**
     * Convierte en tipos nativos
     * @var string[] $casts
     */
    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * generar un UUID cuando se crea un nuevo registro
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string)Str::uuid();
        });
    }

    /**
     * Busca por nombre de CartCode
     * @param $query mixed consulta
     * @param $search string búsqueda
     * @return mixed mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(code) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

}
