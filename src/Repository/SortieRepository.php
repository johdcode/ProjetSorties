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
       // utilisateur connecté dd($user);
      //dd($request->request->get("gestion_sortie")["organisateur"] ) ;
        //dd($request->request->get('gestion_sortie'));
            //id de l'orga = participant->getSortiesCrées v[]sortiesOrganisee
             $qb = $this->createQueryBuilder('s');
                $qb->andWhere('s.organisateur = :organisateur');
//                $qb->join('s.organisateur', 'o');
//                $qb->addSelect('o');
                    $qb->setParameter('organisateur', $user );

                //jointure entre organisateur et sorties
              //  dd($qb->getQuery()->getResult());


        //return $result;



    }

}
