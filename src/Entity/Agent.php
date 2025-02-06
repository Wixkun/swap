<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $id = null;

    #[ORM\OneToMany(targetEntity: TaskProposal::class, mappedBy: 'agent')]
    private Collection $taskProposals;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->taskProposals = new ArrayCollection();
    }

    #[ORM\Column(length: 100)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[ORM\Column(nullable: true)]
    private ?float $ratingGlobal = null;

    #[ORM\OneToOne(inversedBy: 'idAgent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $idUser = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdAgent(): ?string
    {
        return $this->idAgent;
    }

    public function setIdAgent(string $idAgent): static
    {
        $this->idAgent = $idAgent;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getRatingGlobal(): ?float
    {
        return $this->ratingGlobal;
    }

    public function setRatingGlobal(?float $ratingGlobal): static
    {
        $this->ratingGlobal = $ratingGlobal;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(User $user): self
    {
        $this->idUser = $user;
        return $this;
    }

    public function getTaskProposals(): Collection
    {
        return $this->taskProposals;
    }
}
