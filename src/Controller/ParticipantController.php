<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * Met à jour l'utilisateur connecté
     *
     * @Route("/profil/editer", name="/profil/editer", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function editerProfil(Request $request, UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ParticipantType::class, $this->getUser());
        // Initialise les valeurs
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si le mot de passe doit être modifier
            if(empty($request->request->get('participant')['password']['first'])){
                $this->getUser()->setPassword($this->getUser()->getPassword());
            } else {
                $this->getUser()->setPassword(
                    $passwordEncoder->encodePassword(
                        $this->getUser(),
                        $request->request->get('participant')['password']['first']
                    )
                );
            }
            // Supprime l'image de profil précédente
            if(!empty($form->get('urlPhoto')->getData()) && !empty($this->getUser()->getUrlPhoto())){
//                dd($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'imageProfil' . DIRECTORY_SEPARATOR . $this->getUser()->getUrlPhoto());
                unlink($this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'imageProfil' . DIRECTORY_SEPARATOR . $this->getUser()->getUrlPhoto());
            }
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('urlPhoto')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photo_profil_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $this->getUser()->setUrlPhoto($newFilename);
            }

            $this->getDoctrine()->getManager()->persist($this->getUser());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('participant_show', [ 'id' => $this->getUser()->getId()]);
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/index", name="participant_index", methods={"GET"})
     * @param ParticipantRepository $participantRepository
     * @return Response
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/new", name="participant_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_index');
        }

        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}/edit", name="participant_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Participant $participant
     * @return Response
     */
    public function edit(Request $request, Participant $participant): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('participant_index');
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}", name="participant_delete", methods={"DELETE"})
     * @param Request $request
     * @param Participant $participant
     * @return Response
     */
    public function delete(Request $request, Participant $participant): Response
    {

        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participant_index');
    }
}
