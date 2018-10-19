<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Container\Container;

class ArraySameSizeAs implements Rule
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
        $container = Container::getInstance();
        $request = $container->make('Illuminate\Http\Request');
        return count($request->input($attribute)) === count($request->input('items.values'));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The keys and value array must be the same size.';
    }
}