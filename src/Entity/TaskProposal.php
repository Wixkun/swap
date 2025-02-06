<?php

namespace App\Entity;

use App\Repository\TaskProposalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;


#[ORM\Entity(repositoryClass: TaskProposalRepository::class)]
class TaskProposal
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[ORM\Column(nullable: true)]
    private ?float $ratingGlobal = null;

    #[ORM\OneToOne(inversedBy: 'idAgent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $idUser = null;

    #[ORM\OneToMany(mappedBy: 'agent', targetEntity: TaskProposal::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $taskProposals;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getProposedPrice(): ?float
    {
        return $this->proposedPrice;
    }

    public function setProposedPrice(float $proposedPrice): static
    {
        $this->proposedPrice = $proposedPrice;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): static
    {
        $this->agent = $agent;
        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;
        return $this;
    }
}
