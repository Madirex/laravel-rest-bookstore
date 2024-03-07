<?php

namespace App\Rules;

use App\Models\CartCode;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class CartCodeCodeExists
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
        $value = trim(strtolower($value));
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
