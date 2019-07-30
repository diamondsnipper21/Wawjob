<?php

/**
 * @author KCG
 * @since June 5, 2018
 */

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistInFiles implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $file_ids = explode_bracket($value);

        $valid = false;
        foreach (explode_bracket($file_ids) as $file_id) {
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid File.';
    }
}