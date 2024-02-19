<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartCode extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'code', 'percent_discount', 'fixed_discount', 'available_uses', 'expiration_date'];

}
