<?php

namespace App\Tests\Unit;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{
    private const EMAIL_CONSTRAINT_MESSAGE = 'L\'email \"bfend97@gmail\" n\'est pas valide.';

    private const NOT_BLANK_MESSAGE = 'Veuillez saisir une valeur.';

    private const INVALID_EMAIL_VALUE = 'bfend97@gmail';

    private const PASSWORD_REGEX_CONSTRAINT_MESSAGE = 'mot de passe incorrect';

    private const VALID_EMAIL_VALUE = 'bfend97@gmail.com';

    private const VALID_PASSWORD_VALUE = 'password';

    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->validator = $kernel->getContainer()->get('validator');
    }

    public function testUserEntityIsValid()
    {
        $user = new User();

        $user->setEmail(self::VALID_EMAIL_VALUE)
            ->setPassword(self::VALID_PASSWORD_VALUE);

        $this->getValidationErrors($user, 0);
    }

    private function getValidationErrors(User $user, int $numberOfExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($user);

        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors;
    }


}