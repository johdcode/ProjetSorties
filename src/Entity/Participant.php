<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"mail"}, message="Il y a déjà un compte avec cette email")
 * @UniqueEntity(fields={"pseudo"}, message="Il y a déjà un compte avec ce pseudo")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Le champ titre doit être remplis")
     * @Assert\Length(
     *     min="4", max="30",
     *     minMessage="4 caractères minimum",
     *     maxMessage="30 caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom doit contenir des lettres"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Le champ prénom doit être remplis")
     * @Assert\Length(
     *     min="4", max="30",
     *     minMessage="4 caractères minimum",
     *     maxMessage="30 caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre prénom doit contenir des lettres"
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\NotBlank(message="Le champ téléphone doit être remplis")
     * @Assert\Length(
     *     min="4", max="20",
     *     minMessage="4 caractères minimum",
     *     maxMessage="20 caractères maximum"
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom doit contenir des lettres"
     * )
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Le champ email doit être remplis")
     * @Assert\Length(
     *     min="4", max="50",
     *     minMessage="4 caractères minimum",
     *     maxMessage="50 caractères maximum"
     * )
     */
    private $mail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     min="0", max="255",
     *     minMessage="0 caractères minimum",
     *     maxMessage="255 caractères maximum"
     * )
     */
    private $urlPhoto;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $sortiesOrganisees;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participants")
     */
    // * @ORM\JoinColumn(nullable=false)
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="participant")
     */
    private $inscriptions;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *     min="3", max="50",
     *     minMessage="3 caractères minimum",
     *     maxMessage="50 caractères maximum"
     * )
     */
    private $pseudo;

    public function __construct()
    {
        $this->sortiesOrganisees = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->pseudo;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees[] = $sortiesOrganisee;
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if ($this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->removeElement($sortiesOrganisee);
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

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
            $inscription->setParticipant($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getParticipant() === $this) {
                $inscription->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->mail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $this->getAdministrateur() ? $roles[] = 'ROLE_ADMIN' : $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }
}
