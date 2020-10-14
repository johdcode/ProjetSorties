<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\GestionSortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil/créer", name="accueil")
     */
    public function index(Request $request)
    {
        $sortie = new Sortie();

        $sortieForm = $this->createForm(GestionSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            //hydrater les propriétés

        }


        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'sortie' => $sortie
        ]);
    }
}
