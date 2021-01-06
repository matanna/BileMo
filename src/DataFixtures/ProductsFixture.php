<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use App\Entity\Photo;
use App\Entity\PhoneBrand;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Repository\PhotoRepository;

class ProductsFixture extends Fixture
{
    private $photoRepository;

    public function __construct(PhotoRepository $photoRepository) 
    {
        $this->photoRepository = $photoRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $phoneBrands = ['Samsung', 'Huawei', 'iPhone', 'Sony', 'Xiaomi'];
        $memory = [16, 32, 64, 28, 256, 512];
        $photos = ['front', 'back', 'side', 'camera', 'micro'];

        for ($i=0; $i<=4; $i++) {
            $phoneBrand = new PhoneBrand();
            $phoneBrand->setBrand($phoneBrands[$i]);

            $manager->persist($phoneBrand);
            $manager->flush();

            for ($j=0; $j<=rand(5, 15); $j++) {
                $phone = new Phone();
                $phone->setModel($phoneBrand->getBrand() . ' ' . $faker->word() . '-' . $faker->word())
                      ->setCatchPhrase($faker->sentence(7, true))
                      ->setDescription($faker->paragraph(3, true))
                      ->setPrice($faker->randomFloat(2, 600, 1500))
                      ->setColor($faker->colorName())
                      ->setSize(rand(130, 160) . ' mm, ' . rand(55, 80) . ' mm, ' . rand(7, 12) . ' mm')
                      ->setBatteryPower(rand(4000, 10000) . ' mAh')
                      ->setOsName($faker->randomElement(['PhoneOs', 'SmartOs', 'FullOs', 'BaseOs']))
                      ->setWeight(rand(140, 225) . ' g')
                      ->setMemory(array_rand(array_flip($memory)))
                      ->setAvailability($faker->randomElement([true, false]))
                      ->setBrand($phoneBrand)
                ;
                for ($l=0; $l<=4; $l++) {
                    $photo = new Photo();
                    $namePhoto = 'public/photos/' . $phone->getModel() . '_' . array_rand(array_flip($photos)) . '.jpg';
                    
                    $existingPhoto = $this->photoRepository->findOneBy(['name' => $namePhoto]);

                    if ($existingPhoto) {
                        $existingPhoto->addPhone($phone);
                        $phone->addPhoto($existingPhoto);
                        $manager->persist($existingPhoto);
                        $manager->persist($phone);
                        $manager->flush();

                    } else {
                        $photo->setName($namePhoto);

                        $photo->addPhone($phone);
                        $phone->addPhoto($photo);

                        $manager->persist($photo);
                        $manager->persist($phone);
                        $manager->flush();
                    }
                }
                
            }
        }
        $manager->flush();
    }
}
