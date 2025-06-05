<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    private ?int $id = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RendezVous::class)]
    private Collection $rendezVous;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
      $roles = $this->roles;
    // garantit qu'un utilisateur a au moins ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
    }

    
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }
    
     public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Efface les données sensibles si besoin
    }

    public function __construct()
{
    $this->rendezVous = new ArrayCollection();
}

/**
* @return Collection<int, RendezVous>
 */
public function getRendezVous(): Collection
{
    return $this->rendezVous;
}

public function addRendezVous(RendezVous $rendezVous): static
{
    if (!$this->rendezVous->contains($rendezVous)) {
        $this->rendezVous[] = $rendezVous;
        $rendezVous->setUser($this);
    }

    return $this;
}

public function removeRendezVous(RendezVous $rendezVous): static
{
    if ($this->rendezVous->removeElement($rendezVous)) {
        // set the owning side to null (unless already changed)
        if ($rendezVous->getUser() === $this) {
            $rendezVous->setUser(null);
        }
    }

    return $this;
}
}
