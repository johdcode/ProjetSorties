<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\InscriptionRepository;



/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    public function findOneByOrganisateur(Request $request, Participant $user)
    {
        $requestvalue = $request->request->get("gestion_sortie");

       // Sorties lorsque l'utilisateur est connecté
      if(!empty($requestvalue["organisateur"])) {
          $qb = $this->createQueryBuilder('s');
          $qb->andWhere('s.organisateur = :organisateur'); //si Sortie attribut organisateur = organisateur
          $qb->setParameter('organisateur', $user );
          return $qb->getQuery()->getResult();
      }

     if(!empty($requestvalue["etatInscrit"])) {

       $data =$this->getEntityManager()->getRepository(InscriptionRepository::class)->getEntityName()
                    ->findOneBy($user);
           dd($data);
//        $qb = $this->createQueryBuilder('s');
//
//          return $qb->getQuery()->getResult();
      }


    }

}
