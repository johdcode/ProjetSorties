<?php

namespace App\Repository;

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


    public function findOneByOrganisateur(Request $request, User $user)
    {
        //if()
        $sortie = new Sortie();

        //dd($request->request->get('gestion_sortie'));
            //id de l'orga = participant->getSortiesCrées v[]sortiesOrganisee
             $qb = $this->createQueryBuilder('s');
                $qb->andWhere('s.organisateur LIKE :organisateur') // ?
               ->setParameter('organisateur', $request->request->get('gestion_sortie'));


           // condition if = dd($request->request->get("gestion_sortie")["organisateur"]) ;
        //return $result;

    }

}
