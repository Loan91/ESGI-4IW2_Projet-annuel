<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Password */
        if (null === $value || '' === $value) {
            return;
        }

        // Taille minimale
        if (strlen($value) < $constraint->options['minSize']) {
            $this->context->buildViolation($constraint->minSizeMessage)
                ->setParameter('{{ value }}', $constraint->options['minSize'])
                ->addViolation();
        }

        // Taille maximale
        if (strlen($value) > $constraint->options['maxSize']) {
            $this->context->buildViolation($constraint->maxSizeMessage)
                ->setParameter('{{ value }}', $constraint->options['maxSize'])
                ->addViolation();
        }

        // Un chiffre, une lettre majuscule, une minuscule, un caractère spécial
        if (!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&-+=()])(?=\S+$).{' . $constraint->options['minSize'] . ',' . $constraint->options['maxSize'] . '}$/', $value, $matches)) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->containMessage)
                ->addViolation();
        }
    }
}
