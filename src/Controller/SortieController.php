<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\GestionSortieType;
use App\Form\LieuCreationType;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
     * @param CampusRepository $campusRepository
     * @param SortieRepository $sortieRepository
     * @param Request $request
     * @return Response
     *
     */
    public function index(
        CampusRepository $campusRepository,
        SortieRepository $sortieRepository,
        Request $request): Response
    {
        $sorties = [];
        $campus = $campusRepository->findAll();

        $sortieForm = $this->createForm(GestionSortieType::class);
        $sortieForm->handleRequest($request);
        //lors de la soumission controler en bdd les sorties
       if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $sorties = $sortieRepository->findSortieParRecherche($request, $this->getUser());
       }

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'campus' => $campus,
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/new", name="sortie_new")
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @param ParticipantRepository $participantRepository
     * @param CampusRepository $campusRepository
     * @param LieuRepository $lieuRepository
     * @return Response
     */
    public function new(
        Request $request, EtatRepository $etatRepository,
        ParticipantRepository $participantRepository,
        CampusRepository $campusRepository, LieuRepository $lieuRepository) : Response
    {
        $sortie = new Sortie();
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {

            // récupération des valeurs du formulaire au sein de la requête
            $requeteSortie = $request->request->get('sortie');

            // $i est soit true ou false selon l'existence du bouton 'enregistrer' dans le formulaire
            $submitEnregistrer = array_key_exists('enregistrer', $requeteSortie);

            // choisit l'état à appliquer selon le bouton d'envoi du formulaire
            $submitEnregistrer ? $etatAajouter = 'Créée' : $etatAajouter = 'Ouverte';

            $etatEnregistrer = $etatRepository->findOneBy([
                'libelle' => $etatAajouter
            ]);
            $sortie->setEtat($etatEnregistrer);

            // Récupération de l'entité participant, grâce aux infos en session
            $organisateur = $participantRepository->find($this->getUser()->getId());
            $sortie->setOrganisateur($organisateur);

            // Ne renvoie que l'id du campus car $this->getUser() ne possède pas l'entité Campus complète
            $campusOrganisateur = $campusRepository->find($this->getUser()->getCampus()->getId());
            $sortie->setCampus($campusOrganisateur);

            $lieuChoisi = $requeteSortie['lieu'];
            $sortie->setLieu($lieuRepository->find($lieuChoisi['nom']));

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
     * @param Sortie $sortie
     * @return Response
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Sortie $sortie
     * @return Response
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        if(time() < $sortie->getDateHeureDebut()->getTimestamp())
        {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ni annuler une sortie en cours ou passé');
            // redirect to Route prend en parametre le nom de la route + un tableau de parametre à soumettre
            return $this->redirectToRoute('sortie_show',  ['id' => $sortie->getId()]);
        }

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
     * @Route("/{id}/annuler", name="sortie_annuler", methods={"GET","POST"})
     * @param Request $request
     * @param Sortie $sortie
     * @param SortieRepository $sortieRepository
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function annuler(Request $request, Sortie $sortie, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {

        if(time() < $sortie->getDateHeureDebut()->getTimestamp())
        {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ni annuler une sortie en cours ou passé');
            // redirect to Route prend en parametre le nom de la route + un tableau de parametre à soumettre
            return $this->redirectToRoute('sortie_show',  ['id' => $sortie->getId()]);
        }

        $motif = new Sortie();
        $formAnnuler = $this->createFormBuilder($motif)
            ->add('motifAnnulation', TextareaType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Confirmer'
            ])->getForm();
        $formAnnuler->handleRequest();

        if($formAnnuler->isSubmitted() && $formAnnuler->isValid())
        {
            $sortie->setMotifAnnulation($motif->getMotifAnnulation());
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));
            dd($sortie);
        }

        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie,
            'form' => $formAnnuler->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     * @param Request $request
     * @param Sortie $sortie
     * @return Response
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

    /**
     * @Route("/{id}", name="sortie_inscrire", methods={"POST"})
     * @param Request $request
     * @param Sortie $sortie
     * @return Response
     */
    public function inscrire(Request $request, Sortie $sortie): Response
    {


        return $this->redirectToRoute('sortie_show',  ['id' => $sortie->getId()]);
    }
}
