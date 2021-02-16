<?php

namespace App\Validator;

use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidPassword extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Le mot de passe doit contenir au moins 1 chiffre, une minuscule, une majuscule et un caractère spécial';

    public $min;

    public function __construct(array $data)
    {
        if (!\key_exists('min', $data)) {
            throw new InvalidArgumentException('You MUST set a minimum count of character in the ValidPassword constraint', 500);
        }
        $this->min = $data['min'];
    }
}
