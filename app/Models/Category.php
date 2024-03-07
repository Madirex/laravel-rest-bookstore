<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clase Category
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relación con la tabla Books
     * @return mixed mixed
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Busca por nombre de categoría
     * @param $query mixed consulta
     * @param $search string búsqueda
     * @return mixed mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

}
