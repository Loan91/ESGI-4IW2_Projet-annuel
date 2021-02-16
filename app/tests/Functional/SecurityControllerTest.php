<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityControllerTest extends WebTestCase
{
    /** @var \Faker\Factory $faker A french faker */
    private $faker = null;

    /** @var User $fakeUser A user to perform tests */
    protected $fakeUser = null;

    /** @var string $userPassword The password of the user */
    protected $userPassword = '';

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
        $em = self::$container->get('doctrine.orm.default_entity_manager');

        // Init variables
        $faker = \Faker\Factory::create('fr_FR');
        $userPassword = $faker->password(8, 20);

        // Create new user
        $fakeUser = new User();
        $fakeUser = $fakeUser
            ->setEmail($faker->email)
            ->setPassword($this->passwordEncoder->encodePassword($fakeUser, $userPassword))
            ->setName($faker->name('M'));

        // Add user in database
        $em->persist($fakeUser);
        $em->flush();

        $this->faker = $faker;
        $this->fakeUser = $fakeUser;
        $this->userPassword = $userPassword;
    }

    /**
     * This test will check the connexion form
     */
    public function testConnexionForm()
    {
        // On génère l'url menant au formulaire
        $url = self::$container->get('router')->generate('app_login', [], false);

        $crawler = $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        // On récupère le formulaire
        $form = $crawler->filter('#login-form')->form();

        // On va récupérer un utilisateur existant et l'utiliser pour se connecter
        $form->setValues([
            'email' => $this->fakeUser->getEmail(),
            'password' => $this->userPassword
        ]);

        $crawler = $this->client->submit($form);
        $this->assertResponseIsSuccessful();
    }
}
