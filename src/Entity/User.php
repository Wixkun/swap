<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToOne(mappedBy: 'idUser', cascade: ['persist'])]
    private ?Agent $idAgent = null;

    #[ORM\OneToOne(mappedBy: 'idUser', cascade: ['persist'])]
    private ?Customer $idCustomer = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->roles = ['ROLE_CUSTOMER']; 
    }

    public function getId(): ?Uuid
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getIdAgent(): ?Agent
    {
        return $this->idAgent;
    }

    public function setIdAgent(Agent $idAgent): static
    {
        if ($idAgent->getIdUser() !== $this) {
            $idAgent->setIdUser($this);
        }
        $this->idAgent = $idAgent;
        return $this;
    }

    public function removeAgent(): self
    {
        $this->idAgent = null;
        return $this;
    }

    public function getIdCustomer(): ?Customer
    {
        return $this->idCustomer;
    }

    public function setIdCustomer(Customer $idCustomer): static
    {
        if ($idCustomer->getIdUser() !== $this) {
            $idCustomer->setIdUser($this);
        }
        $this->idCustomer = $idCustomer;
        return $this;
    }

    public function removeCustomer(): self
    {
        $this->idCustomer = null;
        return $this;
    }

    public function eraseCredentials(): void
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
