<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for($i=0;$i<20;$i++){
            $ville = new Ville();
            $ville->setNom($faker->lastName);
            $ville-> setCodePostal($faker->postcode);

            $manager->persist($ville);
        }


        $manager->flush();
    }


}
