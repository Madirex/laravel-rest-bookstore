<?php

namespace App\Rules;

use App\Models\Category;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class CategoryNameExists
 */
class CategoryNameExists implements Rule
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
        return !Category::where('name', 'ILIKE', $value)->exists();
    }

    /**
     * Get the validation error message.
     * @return string string
     */
    public function message()
    {
        return 'La categoría ya existe.';
    }
}
