<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Repository\PropertyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $rdv1 = new Contact();
        $rdv1
            ->setProperty($this->getReference(PropertyFixtures::PROPERTY_REFERENCE))
            ->setProspect($this->getReference(UserFixtures::PROSPECT_REFERENCE))
            ->setDesiredDate($faker->dateTime)
            ->setStatus('RDV_TERMINE');
        $manager->persist($rdv1);

        $rdv2 = new Contact();
        $rdv2
            ->setProperty($this->getReference(PropertyFixtures::PROPERTY_REFERENCE))
            ->setProspect($this->getReference(UserFixtures::PROSPECT_REFERENCE))
            ->setDesiredDate($faker->dateTime)
            ->setStatus('RDV_CREE');
        $manager->persist($rdv2);

        $rdv3 = new Contact();
        $rdv3
            ->setProperty($this->getReference(PropertyFixtures::PROPERTY_REFERENCE))
            ->setProspect($this->getReference(UserFixtures::PROSPECT_REFERENCE))
            ->setDesiredDate($faker->dateTime)
            ->setStatus('RDV_TERMINE');
        $manager->persist($rdv3);

        $rdv4 = new Contact();
        $rdv4
            ->setProperty($this->getReference(PropertyFixtures::PROPERTY_REFERENCE))
            ->setProspect($this->getReference(UserFixtures::PROSPECT_REFERENCE))
            ->setDesiredDate($faker->dateTime)
            ->setStatus('RDV_NOUVELLE_DATE');
        $manager->persist($rdv4);

        $rdv5 = new Contact();
        $rdv5
            ->setProperty($this->getReference(PropertyFixtures::PROPERTY_REFERENCE))
            ->setProspect($this->getReference(UserFixtures::PROSPECT_REFERENCE))
            ->setDesiredDate($faker->dateTime)
            ->setStatus('RDV_FERME');
        $manager->persist($rdv5);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            PropertyFixtures::class,
        );
    }
}
