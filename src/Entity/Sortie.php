<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom doit contenir des lettres"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Le champ titre doit être remplis")
     * @Assert\Length(
     *     min="1", max="30",
     *     minMessage="4 caractères minimum",
     *     maxMessage="30 caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom doit contenir des lettres"
     * )
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
     * Vérifie si l'utilisateur à la possibilité de s'inscrire
     * selon la date limite, le nombre de place et si l'utilisateur
     * est l'organisateur
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function peutSinscrire($idUser)
    {
        $result = false;

        if(!$this->estInscrit($idUser) &&
            time() < $this->getDateLimiteInscription()->getTimestamp() &&
            !$this->estComplet()  &&
            $this->getEtat()->getLibelle() == 'Ouverte')
        {
            $result = !$this->estOrganisateur($idUser);
        }

        return $result;
    }

    /**
     * Vérifie si l'utilisateur peut se désinscrire, selon si il
     * est inscrit, que la date de début ne soit pas commencé
     * et que son état ne soit pas 'créée'
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function peutSeDesinscrire($idUser)
    {
        $result = false;

        if($this->estInscrit($idUser) &&
            time() < $this->getDateHeureDebut()->getTimestamp() &&
            ($this->getEtat()->getLibelle() == 'Ouverte' ||
            $this->getEtat()->getLibelle() == 'Clôturée')
        )
        {
            $result = !$this->estOrganisateur($idUser);
        }

        return $result;
    }

    /**
     * Vérifie si l'utilisateur est inscrit et si il est l'organisateur
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
        $result = true;

        if( $this->getNbInscriptionsMax() > $nbInscriptions && $this->getDateHeureDebut()->getTimestamp() > time() )
        {

            $result = false;
        }

        return $result;
    }

    /**
     * Vérifie si la sortie est terminé depuis plus d'un mois
     * @return bool
     * @author Valentin
     */
    public function estArchive(){

        return time() > date_add($this->getDateHeureFin(), new \DateInterval('P1M'))->getTimestamp();
    }

    /**
     * Vérifie si l'utilisateur est l'organisateur de la sortie
     * @param $userId
     * @return bool
     * @author Valentin
     */
    public function estOrganisateur($userId){
        return $this->getOrganisateur()->getId() == $userId;
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

    /**
     * Vérifie si l'utilisateur peut modifier la sortie
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function peutModifier($idUser){
        return $this->estOrganisateur($idUser) && $this->getEtat()->getLibelle() == 'Créée';
    }

    /**
     * Vérifie si l'utilisateur peut annuler la sortie
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function peutAnnuler($idUser){
        $etatSortie = $this->getEtat()->getLibelle();

        return $this->estOrganisateur($idUser) &&
            time() < $this->getDateHeureDebut()->getTimestamp() &&
            $etatSortie == 'Ouverte' || $etatSortie == 'Clôturée';
    }

    /**
     * Vérifie si l'utilisateur peut publier la sortie
     * @param $idUser
     * @return bool
     * @author Valentin
     */
    public function peutPublier($idUser){
        return $this->getEtat()->getLibelle() == 'Créée' && $this->estOrganisateur($idUser);
    }
}
