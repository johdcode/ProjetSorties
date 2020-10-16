<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\GestionSortieType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{

    /**
     * @Route("/", name="sortie_index", methods={"GET", "POST"})
     * @param Sortie $sortie
     * @param CampusRepository $campusRepository
     * @param SortieRepository $sortieRepository
     * @param Request $request
     * @param EntityManagerInterfaceInterface $em
     * @return Response
     *
     */
    public function index(
        InscriptionRepository $inscriptionRepository,
        SortieRepository $sortieRepository,
        Request $request,
        EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $sortieForm = $this->createForm(GestionSortieType::class);
        $sortiesOrganisees =  $sortieRepository->findOneByOrganisateur($request, $user);

        $sortieForm->handleRequest($request);

        return $this->render('sortie/index.html.twig', [
           'sortieForm' => $sortieForm->createView(),
            'sortiesOrganisees'=> $sortiesOrganisees
        ]);
    }

    /**
     * @Route("/new", name="sortie_new")
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @param ParticipantRepository $participantRepository
     * @param CampusRepository $campusRepository
     * @return Response
     */
    public function new(Request $request, EtatRepository $etatRepository, ParticipantRepository $participantRepository, CampusRepository $campusRepository) : Response
    {
        $sortie = new Sortie();
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {

            // récupération des valeurs du formulaire au sein de la requête
            $i = $request->request->get('sortie');

            // $i est soit true ou false selon l'existence du bouton 'enregistrer' dans le formulaire
            $i = array_key_exists('enregistrer', $i);

            // choisit l'état à appliquer selon le bouton d'envoi du formulaire
            $i ? $etatAajouter = 'Créée' : $etatAajouter = 'Ouverte';

            $etatEnregistrer = $etatRepository->findOneBy([
                'libelle' => $etatAajouter
            ]);
            $sortie->setEtat($etatEnregistrer);

            $organisateur = $participantRepository->find($this->getUser()->getId());
            $sortie->setOrganisateur($organisateur);

            $sortie->setCampus($campusRepository->find($organisateur->getCampus()->getId()));

            //$sortie->setLieu()


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie à bien été enregistrée');
            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/new.html.twig', [
            'sortieForm' => $formSortie->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_index');
    }
}
