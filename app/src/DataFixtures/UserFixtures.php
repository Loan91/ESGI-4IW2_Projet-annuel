<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    public const USER_REFERENCE = 'user';

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $user = new User();
        $user
            ->setCivility($faker->boolean ? 'Monsieur' : 'Madame')
            ->setEmail('admin@dev.com')
            ->setPhone('0602030405')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'password'))
            ->setFirstname($faker->firstname)
            ->setLastname($faker->lastname)
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(true);

        $manager->persist($user);

        $user = new User();
        $user
            ->setCivility('Monsieur')
            ->setEmail('jean@gmail.com')
            ->setPhone('0602031575')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'password'))
            ->setFirstname('Jean')
            ->setLastname('Dautrui')
            ->setRoles(['ROLE_USER'])
            ->setEnabled(true);

        $manager->persist($user);
        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::USER_REFERENCE, $user);
    }
}
