<?php

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\TrainStation;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $user = new User();
        $user->setFullName($faker->name);
        $user->setEmail("admin@admin.com");
        $user->setPhoneNumber($faker->phoneNumber);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('azerty');

        $item = [];
        $trainStation = [];

        for ($i = 0; $i < 10; $i++){
            $trainStation[$i] = new TrainStation();
            $trainStation[$i]->setName($faker->name);
            $trainStation[$i]->setLocalisation($faker->address);
            $manager->persist($trainStation[$i]);
        }

        for ($i = 0; $i < 10; $i++){
            $item[$i] = new Item();
            $item[$i]->setOwner($user);
            $item[$i]->setDescription($faker->text);
            $item[$i]->setLocalisation($faker->address);
            $item[$i]->setStatut('random');
            $item[$i]->setDateSignalement(new DateTime());
            $item[$i]->setTrainStation($trainStation[$i]);
            $manager->persist($item[$i]);
        }

        $manager->persist($user);
        $manager->flush();
    }
}
