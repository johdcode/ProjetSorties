<?php

namespace App\Controller\API;

use App\Entity\Lieu;
use App\Form\LieuCreationType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiLieuController extends AbstractController
{
    /**
     * @Route("/api/lieu", name="api_lieu")
     */
    public function index(LieuRepository $lieuRepository, SerializerInterface $serializer)
    {
        $lieux = $lieuRepository->findBy([], ['nom'=>'ASC'], 10);
//        dd($lieux);
        $data = $serializer->serialize($lieux, 'json');

        $response = new JsonResponse();
        $response->setContent($data);
        
        return $response;
    }
    /**
     * @Route("/api/lieu/creer", name="api_lieu_creer")
     */
    public function creerLieu(Request $request, EntityManagerInterface $em)
    {
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuCreationType::class,$lieu);
        $formLieu->handleRequest($request);
        if($formLieu->isSubmitted() && $formLieu->isValid()){
            $em ->persist($lieu);
            $em->flush();
        }
        return new Response("<h3>Requête traitée par le serveur.</h3>");
    }
    /**
     * @Route("/api/lieu/{id}", name="api_lieu_id", requirements={"id"="\d+"})
     */
    public function getLieuAvecId($id, LieuRepository $lieuRepository, SerializerInterface $serializer)
    {
        $lieu = $lieuRepository->find($id);
//        dd($lieux);
        $data = $serializer->serialize($lieu, 'json');

        $response = new JsonResponse();
        $response->setContent($data);

        return $response;
    }

    /**
     * @Route("/api/lieu/ville/{id_ville}", name="api_lieu_ville", requirements={"id_ville"="\d+"})
     */
    public function getLieuAvecVille($id_ville, LieuRepository $lieuRepository, VilleRepository $villeRepository, SerializerInterface $serializer)
    {
        $ville = $villeRepository->find($id_ville);
        $lieux = $lieuRepository->findAllDeVille($ville);
        $data = $serializer->serialize($lieux, 'json');

        $response = new JsonResponse();
        $response->setContent($data);

        return $response;
    }

}
