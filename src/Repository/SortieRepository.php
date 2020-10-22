<?php

namespace App\Repository;

use App\Entity\Inscription;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

        //FILTRE PAR CAMPUS
        if (!empty($request->request->get('gestion_sortie')['campus'])) {
            $resultat = (int)$request->request->get('gestion_sortie')['campus'];
            $query->andWhere('c.id = :req')
                ->join('s.campus', 'c')
                ->addSelect('c')
                ->setParameter('req', $resultat);
        }

        // FILTRE PAR NOM
        if(!empty($request->request->get('gestion_sortie')['nom'])){
            $mots_cles = preg_split('/ /', $request->request->get('gestion_sortie')['nom']);
            foreach ($mots_cles as $index => $mot){
                if($index == 0){
                    $query  ->andWhere('s.nom LIKE :nom' .$index)
                            ->setParameter('nom'. $index, '%' . $mot . '%');
                } else {
                    $query  ->orWhere('s.nom LIKE :nom' . $index)
                            ->setParameter('nom'. $index, '%' . $mot . '%');
                }
            }
        }

        // FILTRE PAR DATE BORNE MIN
        if(!empty($request->request->get('gestion_sortie')['borneDateMin'])){
            $query  ->andWhere('s.dateHeureDebut > :borneDateMin')
                    ->setParameter('borneDateMin', new \DateTime($request->request->get('gestion_sortie')['borneDateMin'])
            );
        }

        // FILTRE PAR DATE BORNE MAX
        if(!empty($request->request->get('gestion_sortie')['borneDateMax'])){
            $query  ->andWhere('s.dateHeureDebut < :borneDateMax')
                    ->setParameter('borneDateMax', new \DateTime($request->request->get('gestion_sortie')['borneDateMax'])
            );
        }

        // FILTRE ORGANISATEUR
        if(!empty($request->request->get('gestion_sortie')['organisateur'])
                && '1' == $request->request->get('gestion_sortie')['organisateur']){
            $query->andWhere('s.organisateur = :organisateur'); //si Sortie attribut organisateur = organisateur
            $query->setParameter('organisateur', $user );
          
        }

        // FILTRE SORTIES AUXQUELLES JE SUIS INSCRIT
        if(!empty($request->request->get("gestion_sortie")["etatInscrit"])
                &&  '1' == $request->request->get("gestion_sortie")["etatInscrit"]) {
                $query->addSelect('i') // to make Doctrine actually use the join
                ->leftJoin('s.inscriptions', 'i')
                ->andWhere('i.participant = :user')
                ->setParameter('user', $user);
       }

        // FILTRE SORTIES AUXQUELLES JE NE SUIS INSCRIT
        if(!empty($request->request->get("gestion_sortie")["etatPasInscrit"])
            &&  '1' == $request->request->get("gestion_sortie")["etatPasInscrit"]) {

            $q = $this->getEntityManager()->createQuery("SELECT i FROM App\Entity\Inscription i WHERE i.sortie IN (SELECT ss.id FROM App\Entity\Sortie ss) AND i.participant != :participant_id");
            $query->andWhere($query->expr()->in('s.id', $q->getDQL()));
            $query->setParameter('participant_id', $user->getId());
       }

        // FITRE EVENEMENT PASSE
        if(!empty($request->request->get('gestion_sortie')['etatPasse'])
                && '1' == $request->request->get('gestion_sortie')['etatPasse']){
            $query  ->andWhere("DATE_ADD(s.dateHeureDebut, s.duree, 'minute') < CURRENT_TIMESTAMP()");
        }

        // Ordonner par date de début
        $query->addOrderBy('s.dateHeureDebut', 'DESC');

       return $query->getQuery()->getResult();
    }
}
