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

    public function __construct()
    {
        $this->id = Uuid::v4(); 
    }

    #[ORM\Column]
    private ?float $proposedPrice = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agent $idAgent = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $idTask = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdTaskProposal(): ?string
    {
        return $this->idTaskProposal;
    }

    public function setIdTaskProposal(string $idTaskProposal): static
    {
        $this->idTaskProposal = $idTaskProposal;

        return $this;
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

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIdAgent(): ?Agent
    {
        return $this->idAgent;
    }

    public function setIdAgent(?Agent $idAgent): static
    {
        $this->idAgent = $idAgent;

        return $this;
    }

    public function getIdTask(): ?Task
    {
        return $this->idTask;
    }

    public function setIdTask(?Task $idTask): static
    {
        $this->idTask = $idTask;

        return $this;
    }
}
