<?php

namespace App\Tests\Functional;

use App\Entity\Property;
use App\Entity\Search;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SearchTest extends KernelTestCase
{
    /** @var EntityManager */
    private $entityManager;

    protected function setUp(): void
    {
      $kernel = self::bootKernel();

      $this->entityManager = $kernel->getContainer()
          ->get('doctrine')
          ->getManager();
    }

    public function testFindInterestedUsers(): void
    {
        $property = new Property();
        $property->setType('all');
        // $property->setCategory('');
        $property->setCity('Lombard');
        $property->setPrice(570000);

        $users = $this->entityManager
            ->getRepository(Search::class)
            ->findInterestedUsers($property)
        ;

        var_dump($users);



        # var_dump($users[1]['email']);
    }
}
