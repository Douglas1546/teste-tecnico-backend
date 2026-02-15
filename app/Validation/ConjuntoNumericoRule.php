<?php

namespace App\Validation;

use Illuminate\Contracts\Validation\ImplicitRule;

class ConjuntoNumericoRule implements ImplicitRule
{
    public function passes($attribute, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        $numeros = array_filter($value, 'is_numeric');

        return count($numeros) > 0;
    }

    public function message()
    {
        return ':attribute não é um conjunto numérico';
    }
}