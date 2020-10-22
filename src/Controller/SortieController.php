<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\GestionSortieType;
use App\Form\LieuCreationType;
use App\Form\LieuType;
use App\Form\SortieModifType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Sodium\add;

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
        $sorties = $sortieRepository->findAll();
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


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie à bien été enregistrée');
            return $this->redirectToRoute('sortie_index');
        }

        // Formulaire ajout de lieu
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuCreationType::class,$lieu);

        return $this->render('sortie/new.html.twig', [
            'sortieForm' => $formSortie->createView(),
            'formLieu' => $formLieu->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     * @param Request $request
     * @param Sortie $sortie
     * @return Response
     */
    public function show(Request $request, Sortie $sortie): Response
    {
        if($sortie->estArchive())
        {
            $this->addFlash('danger', 'La sortie n\'existe plus !');
            return $this->redirectToRoute('sortie_index');
        }
        $nbInscrit = $sortie->getInscriptions()->count();

        $currentUserId = $this->getUser()->getId();
        $peutSinscrire = $sortie->peutSinscrire($currentUserId);
        $peutDesinscrire = $sortie->estInscrit($currentUserId);
        $peutAnnuler = $sortie->peutAnnuler($currentUserId);
        $peutModifier = $sortie->peutModifier($currentUserId);
        $peutPublier = $sortie->peutPublier($currentUserId);

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'peutSinscrire' => $peutSinscrire,
            'peutDesinscrire' => $peutDesinscrire,
            'nbInscrit' => $nbInscrit,
            'peutModifier' => $peutModifier,
            'peutAnnuler' => $peutAnnuler,
            'peutPublier' => $peutPublier
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Sortie $sortie
     * @return Response
     */
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if(time() > $sortie->getDateHeureDebut()->getTimestamp())
        {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier ni annuler une sortie en cours ou passé');
            // redirect to Route prend en parametre le nom de la route + un tableau de parametre à soumettre
            return $this->redirectToRoute('sortie_show',  ['id' => $sortie->getId()]);
        }

        $form = $this->createForm(SortieType::class, $sortie);
        $form->remove('enregistrer')->remove('publier');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_index');
        }

        // Formulaire ajout de lieu
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuCreationType::class,$lieu);

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
            'formLieu' => $formLieu->createView()
        ]);
    }

    /**
     * @Route("/{id}/annuler", name="sortie_annuler", methods={"GET","POST"})
     * @param Request $request
     * @param Sortie $sortie
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function annuler(Request $request, Sortie $sortie, EtatRepository $etatRepository): Response
    {

        if(time() > $sortie->getDateHeureDebut()->getTimestamp())
        {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier ni annuler une sortie en cours ou passée');
            // redirect to Route prend en parametre le nom de la route + un tableau de parametre à soumettre
            return $this->redirectToRoute('sortie_show',  ['id' => $sortie->getId()]);
        }

        $motif = new Sortie();
        $formAnnuler = $this->createFormBuilder($motif)
            ->add('motifAnnulation', TextareaType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Confirmer'
            ])->getForm();

        $formSubmit = $request->request->get('form');

        if($formSubmit)
        {
            $sortie->setMotifAnnulation($formSubmit['motifAnnulation']);
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Annulée']));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie à bien été annulée');
            // redirect to Route prend en parametre le nom de la route + un tableau de parametre à soumettre
            return $this->redirectToRoute('sortie_show',  ['id' => $sortie->getId()]);
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
     * @Route("/{id}/inscrire", name="sortie_inscrire", methods={"GET","POST"})
     * @param $id
     * @param Request $request
     * @param Sortie $sortie
     * @param InscriptionRepository $inscriptionRepository
     * @param SortieRepository $sortieRepository
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function inscrire(
        $id,
        Sortie $sortie,
        InscriptionRepository $inscriptionRepository,
        SortieRepository $sortieRepository,
        EntityManagerInterface $manager): Response
    {
        // => retrouve la sortie cliquée du tableau
       $sortieCliquee = $sortieRepository->find($id);
        //vérifie si l'user en session est existant ds la sortie
        $verificationSiInscrit = $inscriptionRepository->findOneBy(["sortie" => $sortie, "participant" => $this->getUser()]);

           if( $verificationSiInscrit == Null
                &&$sortieCliquee->estComplet()
               && $sortieCliquee->getDateLimiteInscription()->getTimestamp() > time()
               && $sortieCliquee->getEtat()->getLibelle() != "Clôturée"
               && $sortieCliquee->getEtat()->getLibelle()!= "Annulée")
           {
               $inscription = new Inscription();
               $inscription->setDateInscription(new \DateTime());
               $inscription->setSortie($sortieCliquee);
               $inscription->setParticipant($this->getUser());

               $manager->persist($inscription);
               $manager->flush();
               $this->addFlash('success', "Vous vous êtes bien inscrit à votre activité");
         } else {
               $this->addFlash('danger', "Vous êtes déjà inscrit ou la sortie n'est plus valable !");
           }

        return $this->redirectToRoute('sortie_index',  ['id' => $sortie->getId()]);

    }



    /**
     * @Route("/", name="sortie_desinscrire", methods={"POST"})
     * @param Request $request
     * @param Sortie $sortie
     * @return Response
     */
    public function desinscrire(Request $request, Sortie $sortie): Response
    {

        return $this->redirectToRoute('sortie_index',  ['id' => $sortie->getId()]);
    }

    /**
     * @Route("/{id}/publier", name="sortie_publier", methods={"GET", "POST"})
     * @param Request $request
     * @param Sortie $sortie
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function publier(Request $request, Sortie $sortie, EtatRepository $etatRepository): Response
    {
        $etatEnregistrer = $etatRepository->findOneBy([
            'libelle' => 'Ouverte'
        ]);

        $sortie->setEtat($etatEnregistrer);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_show',  ['id' => $sortie->getId()]);
    }

}
