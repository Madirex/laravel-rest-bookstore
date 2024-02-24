<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clase Book
 */
class Book extends Model
{
    use HasFactory;

    public static $IMAGE_DEFAULT = 'images/book.png';
    protected $fillable = ['isbn', 'name', 'author', 'publisher', 'image', 'description', 'price', 'stock', 'category_name', 'active'];

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
     * Relación con la tabla categorías
     * @return mixed mixed
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Busca por nombre de Book
     * @param $query mixed consulta
     * @param $search string búsqueda
     * @return mixed mixed
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('LOWER(author) LIKE ?', ["%" . strtolower($search) . "%"]);
    }
}
