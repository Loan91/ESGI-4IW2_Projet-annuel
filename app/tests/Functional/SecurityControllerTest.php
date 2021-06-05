<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityControllerTest extends WebTestCase
{
    /** @var \Faker\Factory $faker A french faker */
    protected $faker = null;

    /** @var UserPasswordEncoderInterface $passwordEncoder The symfony password encoder */
    protected $passwordEncoder = null;

    /**
     * To set up the project, we have to create a fake user which will be used
     * for connexion
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        self::bootKernel();

        // Gets services
        $this->passwordEncoder = self::$container->get(UserPasswordEncoderInterface::class);
        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
        $this->faker = \Faker\Factory::create('fr_FR');
    }

    public function recreateDatabase()
    {
        // Recreate the database
        exec('bin/console d:s:d --force');
        exec('bin/console d:s:u --force');
    }

    /**
     * This test will check the connexion form
     */
    public function testConnexionForm()
    {
        $this->recreateDatabase();
        
        /** @var $faker \Faker\Generator */
        $faker = $this->faker;
        $userPassword = $faker->password(8, 20);

        // Create new user
        $user = new User();
        $user
            ->setCivility($faker->boolean ? 'Monsieur' : 'Madame')
            ->setEmail('admin@dev.com')
            ->setPhone('0602030405')
            ->setPassword($this->passwordEncoder->encodePassword($user, $userPassword))
            ->setFirstname($faker->firstname)
            ->setLastname($faker->lastname)
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabled(true);

        // Add user in database
        $this->em->persist($user);
        $this->em->flush();

        // On génère l'url menant au formulaire
        $url = self::$container->get('router')->generate('app_login', [], false);

        $crawler = $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        // On récupère le formulaire
        $form = $crawler->filter('#login-form')->form();

        // On va récupérer un utilisateur existant et l'utiliser pour se connecter
        $form->setValues([
            'email' => $user->getEmail(),
            'password' => $userPassword
        ]);

        $crawler = $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);
    }
}
