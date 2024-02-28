<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueCaseInsensitive implements Rule
{
    private $table, $column, $ignoreId;

    public function __construct($table, $column, $ignoreId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)
            ->whereRaw("LOWER($this->column) = ?", [strtolower($value)]);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        return $query->count() == 0;
    }

    public function message()
    {
        return 'El valor del campo :attribute ya está en uso.';
    }
}
