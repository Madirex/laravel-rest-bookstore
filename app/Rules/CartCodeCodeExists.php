<?php

namespace App\Rules;

use App\Models\CartCode;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class CategoryNameNotExists
 */
class CartCodeCodeExists implements Rule
{
    /**
     * Determine if the validation rule passes.
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function passes($attribute, $value)
    {
        return !CartCode::where('code', 'ILIKE', $value)->exists();
    }

    /**
     * Get the validation error message.
     * @return string string
     */
    public function message()
    {
        return 'El CartCode ya existe.';
    }
}
