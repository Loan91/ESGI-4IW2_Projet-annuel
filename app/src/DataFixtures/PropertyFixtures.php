<?php

namespace App\DataFixtures;

use App\Entity\Property;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\HttpFoundation\File\File;

class PropertyFixtures extends Fixture implements DependentFixtureInterface
{
  public const PROPERTY_REFERENCE = 'bien';

  public function load(ObjectManager $manager)
  {
    for ($i = 0; $i < 15; $i++) {
      $this->makeProperty($manager);
    }
  }

  public function makeProperty(ObjectManager $manager)
  {
    $faker = \Faker\Factory::create('fr_FR');

    $bien = new Property();

    $bien = $bien
      ->setType($type = $faker->randomElement(['maison', 'appartement']))
      ->setCategory($faker->randomElement([
        'Studio' => 'studio'
      ] + (function () {
        $data = [];
        for ($i = 0; $i < 15; $i++) {
          $data['f' . $i] = 'f' . $i;
        }
        return $data;
      })()))
      ->setArea($faker->numberBetween($min = 3, $max = 240))
      ->setDescription($faker->text($maxNbChars = 100))
      ->setConstructionDate($faker->dateTime())
      ->setFloor($type === 'appartement' ? $faker->numberBetween($min = 0, $max = 4) : null) // Nullable
      ->setFloors($type === 'maison' ? $faker->numberBetween($min = 3, $max = 12) : 0)
      ->setRooms($faker->numberBetween($min = 0, $max = 4))
      ->setBedrooms($faker->numberBetween($min = 0, $max = 4))
      ->setBathrooms($faker->numberBetween($min = 0, $max = 4))
      ->setToilets($faker->numberBetween($min = 0, $max = 2))
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
      ->setAddress($faker->streetAddress)
      ->setZipCode((int)$faker->postcode)
      ->setCity($faker->city)
      ->setRentOrSale($faker->boolean() ? 'sale' : 'rent')
      ->setPrice($faker->randomNumber(6, true))
      ->setCharges($faker->randomNumber(3, true))
      ->setGuarentee($faker->randomNumber(4, true))
      ->setFeesPrice($faker->randomNumber(2, true))
      ->setInventoryPrice($faker->randomNumber(2, true))
      ->setPublished(true)

      ->setOwner($this->getReference(UserFixtures::USER_REFERENCE));

    // 50% de chances d'avoir une image
    // if($faker->boolean(50)) {
    //   $image = new File(dirname(__DIR__, 2).'/public/images/maison-default.jpg');
    //   $bien->setImageFile($image);
    //   $bien->setImageName($image->getFilename());
    //   $bien->setImageSize($image->getSize());
    // }

    $manager->persist($bien);

    $manager->flush();

    // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
    $this->setReference(self::PROPERTY_REFERENCE, $bien);
  }

  public function getDependencies()
  {
    return array(
      UserFixtures::class,
    );
  }
}
