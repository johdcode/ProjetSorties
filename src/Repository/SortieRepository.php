<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


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

       // Sorties lorsque l'utilisateur est connecté
      if(!empty($request->request->get("gestion_sortie")["organisateur"] = 1)) {
          $qb = $this->createQueryBuilder('s');
          $qb->andWhere('s.organisateur = :organisateur'); //si Sortie attribut organisateur = organisateur
          $qb->setParameter('organisateur', $user );
          return $qb->getQuery()->getResult();
      }



    }

}
