<?php

namespace App\Controller\API;

use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
