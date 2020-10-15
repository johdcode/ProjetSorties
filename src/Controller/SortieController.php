<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\GestionSortieType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
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
     */
    public function index(

        CampusRepository $campusRepository,
        SortieRepository $sortieRepository,
        Request $request,
        EntityManagerInterface $em): Response
    {
        $campus = $campusRepository->findAll();
        $sortie = new Sortie();
        $sortieForm = $this->createForm(GestionSortieType::class, $sortie);

        $sortieForm->handleRequest($request);


        //lors de la soumission controler en bdd les sorties
       if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $em->persist($sortie);
            $em->flush();
       }

        return $this->render('sortie/index.html.twig', [
            'campus' => $campus,
           'sortieForm' => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
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
