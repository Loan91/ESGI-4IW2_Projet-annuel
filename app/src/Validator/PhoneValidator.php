<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Phone */

        if (null === $value) {
            return;
        }

        // Size test
        if(strlen($value) !== $constraint->lenght) {
            $this->context->buildViolation($constraint->lenMessage)
            ->setParameter('{{ lenght }}', $constraint->lenght)
            ->addViolation();
        }

        // Content test
        if(\preg_match('/^0[0-9]{9}$/', $value) == false) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        }
    }
}
