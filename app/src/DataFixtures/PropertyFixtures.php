<?php

namespace App\DataFixtures;

use App\Entity\Property;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PropertyFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROPERTY_REFERENCE = 'bien';

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        $bien = new Property();
        $bien = $bien
            ->setType('maison')
            ->setCategory('f4')
            ->setArea('24')
            ->setDescription('put a description in here')
            ->setConstructionDate($faker->dateTime())
            // ->setFloor() // Nullable
            ->setFloors(0)
            ->setRooms(4)
            ->setBedrooms(2)
            ->setBathrooms(1)
            ->setToilets(1)
            ->setIsFurnished(true)
            ->setContainsStorage(true)
            ->setIsKitchenSeparated(false)
            ->setContainDiningRoom(true)
            ->setGround('Bois')
            ->setHeater('individuel')
            ->setFireplace(true)
            ->setElevator(true)
            ->setExternalStorage(true)
            ->setAreaExternalStorage(8)
            ->setGuarding(true)
            ->setEnergyConsumption(120)
            ->setGasEmissions(36)
            ->setAddress($faker->address)
            ->setZipCode((int)$faker->postcode)
            ->setCity($faker->city)
            ->setCountry($faker->country)
            ->setRentOrSale('sale')
            ->setPrice($faker->randomNumber(6, true))
            ->setCharges($faker->randomNumber(3, true))
            ->setGuarentee($faker->randomNumber(4, true))
            ->setFeesPrice($faker->randomNumber(2, true))
            ->setInventoryPrice($faker->randomNumber(2, true))

            ->setOwner($this->getReference(UserFixtures::USER_REFERENCE));

        $manager->persist($bien);

        $manager->flush();

        // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
        $this->addReference(self::PROPERTY_REFERENCE, $bien);
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
