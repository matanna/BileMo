<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ClientFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i=0; $i<=15; $i++) {
            $client = new Client();
            $client->setName($faker->company());

            $manager->persist($client);
            $manager->flush();

            for ($j=0; $j<=rand(15, 40); $j++) {
                $user = new User();
                $user->setUsername($faker->username())
                     ->setEmail($faker->freeEmail())
                     ->setPassword($faker->password())
                     ->setClient($client)
                ;

                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}