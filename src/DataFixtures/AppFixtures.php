<?php

namespace App\DataFixtures;


use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
                 ->setlatitude($faker->latitude)
                 ->setLongitude($faker->longitude)
                ->setVille($villes[rand(0, count($villes) - 1)]);
        }


        $etatsFixe = ["Créée", "Ouverte", "En cours", "Clôturée", "Annulée"];
        $etats = [];
        foreach ($etatsFixe as $etat){
            $i = new Etat();
            $i->setLibelle($etat);
            $etats[] = $i;
        }

        $participants = [];
        for ($i = 0; $i < 20; $i++) {
            $participants[$i] = new Participant();
            $participants[$i]->setNom($faker->lastName)
                ->setPrenom($faker->firstName)
                ->setPseudo($faker->userName)
                ->setTelephone($faker->phoneNumber)
                ->setMail($faker->email)
                ->setPassword($faker->password)
                ->setAdministrateur($faker->boolean)
                ->setActif($faker->boolean)
                ->setCampus($campus[rand(0, count($campus) - 1)]);
        }

        $participant = new Participant();
        $participant->setNom("Gontran")
            ->setPrenom("denise")
            ->setPseudo("dudu")
            ->setTelephone($faker->phoneNumber)
            ->setMail($faker->email)
            ->setPassword("\$argon2id\$v=19\$m=65536,t=4,p=1\$Vi5KLlhPSGYwbkhkdzdnUg\$kHKC4MRV6tONo8BmIn80YG8FCErkyjvD7E5PcGrEDcM")
            ->setAdministrateur(true)
            ->setActif(true)
            ->setCampus($campus[rand(0, count($campus) - 1)]);
        $participants[] = $participant;

        $sorties = [];
        for ($i = 0; $i < 180; $i++) {
            $sorties[$i] = new Sortie();
            $sorties[$i]->setNom($faker->company)
                ->setDateHeureDebut($faker->dateTimeBetween($startDate = 'now', $endDate = '+ 7 days', $timezone = 'UTC'))
                ->setDuree($faker->randomDigitNotNull)
                ->setDateLimiteInscription($faker->dateTimeInInterval($startDate = 'now', $interval = '+ 30 days', $timezone = 'UTC'))
                ->setNbInscriptionsMax($faker->randomDigitNotNull)
                ->setInfosSortie($faker->text)
                ->setEtat($etats[array_rand($etats, 1)])
                ->setLieu($lieux[rand(0, count($lieux) - 1)])
                ->setOrganisateur($participants[rand(0, count($participants) - 1)])
                ->setCampus($campus[rand(0, count($campus) - 1)]);
        }

        $inscriptions = [];
        for ($i = 0; $i < 80; $i++) {
            $inscriptions[$i] = new Inscription();
            $inscriptions[$i]->setDateInscription($faker->dateTimeBetween($startDate = 'now', $endDate = '+ 5 days', $timezone = 'UTC'))
                ->setParticipant($participants[rand(0, count($participants) - 1)])
                ->setSortie($sorties[rand(0, count($sorties) - 1)]);
        }

        foreach ($etats as $etat){
            $manager->persist($etat);
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
