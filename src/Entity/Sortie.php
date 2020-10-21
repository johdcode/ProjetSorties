<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    private $dateHeureFin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="sortiesOrganisees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="sortie")
     */
    private $inscriptions;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motifAnnulation;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateHeureFin()
    {
        $duree_seconde = $this->getDuree() * 60;
        $this->setDateHeureFin(
            \DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", $this->getDateHeureDebut()->getTimestamp() + $duree_seconde))
        );
        return $this->dateHeureFin;
    }

    /**
     * @param mixed $dateHeureFin
     */
    public function setDateHeureFin($dateHeureFin): void
    {
        $this->dateHeureFin = $dateHeureFin;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * Vérifie si l'utilisateur est inscrit et si la date d'inscription
     * n'est pas dépassée
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function peutSinscrire($idUser)
    {
        $result = false;

        if(!$this->utilisateurEstInscrit($idUser) && time() < $this->getDateLimiteInscription()->getTimestamp())
        {
            $result = !$this->estOrganisateur($idUser);
        }

        return $result;
    }

    /**
     * Vérifie si l'utilisateur est inscrit et si la date d'inscription
     * n'est pas dépassée
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function estInscrit($idUser)
    {
        $result = false;

        foreach ($this->getInscriptions() as $inscription){
            if ($idUser == $inscription->getParticipant()->getId()){
                $result = !$this->estOrganisateur($idUser);
                break;
            }
        }

        return $result;
    }

    /**
     * Vérifie si la sortie a atteint la limite d'inscriptions
     * et si la date de début n'a pas encore été atteinte
     * @return bool
     * @author Valentin
     */
    public function estComplet(){
        $nbInscriptions = $this->getInscriptions()->count();
        $result = false;

        if($this->getDateHeureDebut()->getTimestamp() > time() && $this->getNbInscriptionsMax() == $nbInscriptions)
        {
            $result = true;
        }

        return $result;
    }

    /**
     * Vérifie si l'utilisateur en session est inscrit
     * à la sortie
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function utilisateurEstInscrit($idUser){
        $result = false;

        // vérifier, via idUser, si l'utilisateur est dans une des inscriptions de la sortie
        foreach ($this->getInscriptions() as $inscritpion){
            if($inscritpion->getParticipant()->getId() == $idUser)
            {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Vérifie si la sortie est terminé depuis plus d'un mois
     * @return bool
     * @author Valentin
     */
    public function estArchive(){
        $result = false;

        if(time() > date_add($this->getDateHeureFin(), new \DateInterval('P1M')))
        {
            $result = true;
        }

        return $result;
    }

    /**
     * Vérifie si l'utilisateur est l'organisateur de la sortie
     * @param $userId
     * @return bool
     * @author Valentin
     */
    public function estOrganisateur($userId){
        $result = false;

        if ($this->getOrganisateur()->getId() == $userId){
            $result = true;
        }

        return $result;
    }

    /**
     * Vérifie si la sortie est terminé
     * @return bool
     * @author Valentin
     */
    public function estTermine(){
        time() > $this->getDateHeureFin() ? $result = true : $result = false;

        return $result;
    }
}
