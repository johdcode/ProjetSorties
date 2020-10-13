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
//        for($i=0;$i<20;$i++){
//            $participant = new Participant();
//            $participant->setNom($faker->lastName);
//            $participant->setPrenom($faker->firstName);
//            $participant->setTelephone($faker->phoneNumber);
//            $participant->setMail($faker->email);
//            $participant->setMotDePasse($faker->password);
//            $participant->setNom($faker->name);
//            $participant->setAdministrateur($faker->boolean);
//            $participant->setActif($faker->boolean);
//            $participant->setUrlPhoto($faker->imageUrl($width = 80, $height = 80));
//            //$participant->setCampus($faker->city);
//            $manager->persist($participant);
//        }


        //$manager->flush();
    }
}
