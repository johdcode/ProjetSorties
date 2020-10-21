<?php

namespace App\Repository;

use App\Entity\Inscription;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function findWithCategories()
    {
        $qb = $this->createQueryBuilder('i'); // Idea
        $qb->andWhere('i.isPublished = true');

        //on demande de récupérer en jointure les catégories
        $qb->join('i.category', 'c');
        //ET ON N'OUBLIE PAS DE LES SÉLECTIONNER !
        $qb->addSelect('c');

        $qb->addOrderBy('i.dateCreated', 'DESC');
        $qb->setMaxResults(50);

        $query = $qb->getQuery();
        $results = $query->getResult();

        //ou si nous avions fait une relation Many2Many, on aurait pu utiliser le Paginator pour avoir un bon limit fonctionnel
        //return new Paginator($query);
        return $results;
    }


}
