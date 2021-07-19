<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator as FakerGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $passwordEncoder A password encoder */
    private $passwordEncoder;

    /** @var string Reference to get a user generated between fakers */
    public const USER_REFERENCE = 'user';
    public const PROSPECT_REFERENCE = 'prospect';

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Persist users in database
     */
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $admin = $this->createAdmin($faker);
        $manager->persist($admin);

        // Other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::USER_REFERENCE, $admin);

        $prospect = $this->createExampleMember($faker);
        $manager->persist($prospect);
        
        $this->addReference(self::PROSPECT_REFERENCE, $prospect);
        
        for ($i=0; $i < 36; $i++) { 
            $manager->persist($this->createMember($faker));
        }
        $manager->flush();        
    }

    /**
     * Returns a fresh admin user generated
     */
    public function createAdmin(FakerGenerator $faker): User
    {
        $faker = \Faker\Factory::create('fr_FR');

        $user = new User();
        return $user
            ->setCivility($faker->boolean ? 'Monsieur' : 'Madame')
            ->setEmail('admin@dev.com')
            ->setPhone('0602030405')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'password'))
            ->setFirstname($faker->firstname)
            ->setLastname($faker->lastname)
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(true);
    }
    
    /**
     * Returns a fresh example member user
     */
    public function createExampleMember(FakerGenerator $faker): User
    {
        $faker = \Faker\Factory::create('fr_FR');

        $prospect = new User();
        return $prospect
            ->setCivility($faker->boolean ? 'Monsieur' : 'Madame')
            ->setEmail('user@mail.com')
            ->setPhone('0602030405')
            ->setPassword($this->passwordEncoder->encodePassword($prospect, 'Passw0r@'))
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setRoles(['ROLE_USER']) // Set role user to each members
            ->setEnabled(true) // Enable each members
            ->setCreatedAt($faker->dateTimeBetween('-6 months', '+6 months')); 
    }

    /**
     * Returns a fresh member user generated
     */
    public function createMember(FakerGenerator $faker): User
    {
        $faker = \Faker\Factory::create('fr_FR');

        $user = new User();
        return $user
            ->setCivility($faker->boolean ? 'Monsieur' : 'Madame')
            ->setEmail($faker->email)
            ->setPhone('0602030405')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'memberpassword'))
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setRoles(['ROLE_USER']) // Set role user to each members
            ->setEnabled(true) // Enable each members
            ->setCreatedAt($faker->dateTimeBetween('-6 months', '+6 months')); 
    }
}
