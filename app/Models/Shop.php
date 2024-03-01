<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clase Shop
 */

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'address', 'books', 'active'];

    /**
     * Oculta los campos
     * @var string[] $hidden
     */
    protected $hidden = [
        'active',
    ];





    /**
     * Convierte en tipos nativos
     * @var string[] $casts
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relación con la tabla libros
     * @return mixed mixed
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Relación con la tabla categorías
     * @return mixed mixed
     */

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Busca por nombre de Shop
     * @param $query mixed consulta
     * @param $search string búsqueda
     * @return mixed mixed
     */

    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(address) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

}
