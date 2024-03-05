<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'status',
        'order_lines',
        'total_amount',
        'subtotal',
        'total_lines',
        'is_deleted',
        'finished_at'
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
     * Relación con la tabla User
     * @return mixed mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la tabla OrderLine
     * @return mixed mixed
     */
    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('status LIKE ?', ["%" . strtolower($search) . "%"])
            ->orWhereRaw('id::text LIKE ?', ["%" . $search . "%"]);
    }
}
