<?php

namespace App\Rules;

use App\Models\Book;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ISBNNameExists
 */
class ISBNNameExists implements Rule
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
        return !Book::where('isbn', 'ILIKE', $value)->exists();
    }

    /**
     * Get the validation error message.
     * @return string string
     */
    public function message()
    {
        return 'El ISBN del libro ya está agregado al sistema.';
    }
}
