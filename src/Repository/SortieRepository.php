<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function findSortieParRecherche(Request $request, UserInterface $user)
    {
        $query = $this->createQueryBuilder('s');
        // FILTRE PAR NOM
        if(!empty($request->request->get('gestion_sortie')['nom'])){
            $query  ->andWhere('s.nom LIKE :nom')
                    ->setParameter('nom', '%' . $request->request->get('gestion_sortie')['nom'] . '%');
        }
        // FILTRE PAR DATE BORNE MIN
        if(!empty($request->request->get('gestion_sortie')['borneDateMin'])){
            $query  ->andWhere('s.dateHeureDebut > :borneDateMin')
                    ->setParameter('borneDateMin',
                        date("Y/m/d H:i:s", mktime(
                            $request->request->get('gestion_sortie')['borneDateMin']['time']['hour'],
                            $request->request->get('gestion_sortie')['borneDateMin']['time']['minute'],
                            0,
                            $request->request->get('gestion_sortie')['borneDateMin']['date']['month'],
                            $request->request->get('gestion_sortie')['borneDateMin']['date']['day'],
                            $request->request->get('gestion_sortie')['borneDateMin']['date']['year']
                        ))
                    );
        }
        // FILTRE PAR DATE BORNE MAX
        if(!empty($request->request->get('gestion_sortie')['borneDateMax'])){
            $query  ->andWhere('s.dateHeureDebut < :borneDateMax')
                    ->setParameter('borneDateMax',
                        date("Y/m/d H:i:s", mktime(
                            $request->request->get('gestion_sortie')['borneDateMax']['time']['hour'],
                            $request->request->get('gestion_sortie')['borneDateMax']['time']['minute'],
                            0,
                            $request->request->get('gestion_sortie')['borneDateMax']['date']['month'],
                            $request->request->get('gestion_sortie')['borneDateMax']['date']['day'],
                            $request->request->get('gestion_sortie')['borneDateMax']['date']['year']
                        ))
                    );
        }
        // FILTRE ORGANISATEUR
        if(!empty($request->request->get('gestion_sortie')['organisateur'])
                && '1' == $request->request->get('gestion_sortie')['organisateur']){
            $query->andWhere('s.organisateur = :organisateur'); //si Sortie attribut organisateur = organisateur
            $query->setParameter('organisateur', $user );
        }
        // TODO FILTRE SORTIES AUX QUELLES JE SUIS INSCRIT
//        if(!empty($request->request->get("gestion_sortie")["etatInscrit"] == 1)) {

//        }
        // TODO FILTRE SORTIES AUX QUELLES JE NE SUIS PAS INSCRIT
//        if(!empty($request->request->get("gestion_sortie")["etatPasInscrit"] == 1)) {

//        }
        // FITRE EVENEMENT PASSE
        if(!empty($request->request->get('gestion_sortie')['etatPasse'])
                && '1' == $request->request->get('gestion_sortie')['etatPasse']){
            $query  ->andWhere("DATE_ADD(s.dateHeureDebut, s.duree, 'minute') < CURRENT_TIMESTAMP()");
        }

//        dd($query->getQuery());
        return $query->getQuery()->getResult();
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
