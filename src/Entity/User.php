<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Equipe $equipe = null;

    /**
     * @var Collection<int, Absence>
     */
    #[ORM\OneToMany(targetEntity: Absence::class, mappedBy: 'employee')]
    private Collection $absences;

    /**
     * @var Collection<int, FraisDeplacement>
     */
    #[ORM\OneToMany(targetEntity: FraisDeplacement::class, mappedBy: 'user')]
    private Collection $fraisDeplacements;

    /**
     * @var Collection<int, Equipe>
     */
    #[ORM\OneToMany(targetEntity: Equipe::class, mappedBy: 'Responsable')]
    private Collection $responsable;

    public function __construct()
    {
        $this->absences = new ArrayCollection();
        $this->fraisDeplacements = new ArrayCollection();
        $this->responsable = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): static
    {
        $this->equipe = $equipe;

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setEmployee($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEmployee() === $this) {
                $absence->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FraisDeplacement>
     */
    public function getFraisDeplacements(): Collection
    {
        return $this->fraisDeplacements;
    }

    public function addFraisDeplacement(FraisDeplacement $fraisDeplacement): static
    {
        if (!$this->fraisDeplacements->contains($fraisDeplacement)) {
            $this->fraisDeplacements->add($fraisDeplacement);
            $fraisDeplacement->setUser($this);
        }

        return $this;
    }

    public function removeFraisDeplacement(FraisDeplacement $fraisDeplacement): static
    {
        if ($this->fraisDeplacements->removeElement($fraisDeplacement)) {
            // set the owning side to null (unless already changed)
            if ($fraisDeplacement->getUser() === $this) {
                $fraisDeplacement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipe>
     */
    public function getResponsable(): Collection
    {
        return $this->responsable;
    }

    public function addResponsable(Equipe $responsable): static
    {
        if (!$this->responsable->contains($responsable)) {
            $this->responsable->add($responsable);
            $responsable->setResponsable($this);
        }

        return $this;
    }

    public function removeResponsable(Equipe $responsable): static
    {
        if ($this->responsable->removeElement($responsable)) {
            // set the owning side to null (unless already changed)
            if ($responsable->getResponsable() === $this) {
                $responsable->setResponsable(null);
            }
        }

        return $this;
    }
}
