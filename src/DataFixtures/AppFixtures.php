<?php

namespace App\DataFixtures;


require_once('vendor/autoload.php');

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Entity\Campus;
use App\Entity\Participant;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @author Valentin
     *
     */
    public function load(ObjectManager $manager)
    {


        $faker = Faker\Factory::create('fr_FR');

        $campus = [];
        for ($i = 0; $i < 5; $i++) {
            $campus[$i] = new Campus();
            $campus[$i]->setNom($faker->city);
        }

        $villes = [];
        for ($i = 0; $i < 5; $i++) {
            $villes[$i] = new Ville();
            $villes[$i]->setNom($faker->city)
                ->setCodePostal($faker->postcode);
        }

        $lieux = [];
        for ($i = 0; $i < 10; $i++) {
            $lieux[$i] = new Lieu();
            $lieux[$i]->setNom($faker->city)
                ->setRue($faker->streetName)
               /* ->setlatitude($faker->latitude($min = -90, $max = 90))
                ->setLongitude($faker->longitude($min = -180, $max = 180))*/
                ->setVille($villes[rand(0, count($villes) - 1)]);
        }

        $etat = new Etat();
        $etat->setLibelle("En cours");
        

        $sorties = [];
        for ($i = 0; $i < 10; $i++) {
            $sorties[$i] = new Sortie();
            $sorties[$i]->setNom($faker->name)
                ->setDateHeureDebut($faker->dateTime)
                ->setDuree($faker->randomDigit)
                ->setDateLimiteInscription($faker->dateTime)
                ->setNbInscriptionsMax($faker->randomDigit)
                ->setInfosSortie($faker->text)
                ->setEtat($etat);
        }

        $participants = [];
        for ($i = 0; $i < 20; $i++) {
            $participants[$i] = new Participant();
            $participants[$i]->setNom($faker->lastName)
                ->setPrenom($faker->firstName)
                ->setTelephone($faker->phoneNumber)
                ->setMail($faker->email)
                ->setMotDePasse($faker->password)
                ->setAdministrateur($faker->boolean)
                ->setActif($faker->boolean)
                ->setCampus($campus[rand(0, count($campus) - 1)]);
        }

        $inscriptions = [];
        for ($i = 0; $i < 15; $i++) {
            $inscriptions[$i] = new Inscription();
            $inscriptions[$i]->setDateInscription($faker->dateTime)
                ->setParticipant($participants[rand(0, count($participants) - 1)])
                ->setSortie($sorties[rand(0, count($sorties) - 1)]);
        }


        foreach ($participants as $participant){
            $manager->persist($participant);
        }

        foreach ($sorties as $sortie){
            $manager->persist($sortie);
        }

        foreach ($villes as $ville){
            $manager->persist($ville);
        }

        foreach ($lieux as $lieu){
            $manager->persist($lieu);
        }

        foreach ($campus as $index){
            $manager->persist($index);
        }

        foreach ($inscriptions as $inscription){
            $manager->persist($inscription);
        }

        $manager->flush();

    }


}
