<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clase Address
 */
class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'street',
        'number',
        'city',
        'province',
        'country',
        'postal_code',
        'addressable_id',
        'addressable_type'
    ];

    /**
     * Relación con la tabla Address
     * @return mixed mixed
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    /**
     * Busca la dirección
     * @param $query mixed consulta
     * @param $search string búsqueda
     * @return mixed mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(street) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(number) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(city) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(province) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(country) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(postal_code) LIKE ?', ["%" . strtolower($search) . "%"]);
    }
}
