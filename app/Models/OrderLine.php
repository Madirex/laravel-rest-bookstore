<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'id',
        'quantity',
        'book_id',
        'order_id',
        'price',
        'total',
        'subtotal',
        'selected'
    ];

    /**
     * Relación con la tabla Book
     * @return mixed mixed
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function cartCode()
    {
        return $this->belongsTo(CartCode::class, 'cart_code_id');
    }

    /**
     * Relación con la tabla Order
     * @return mixed mixed
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
