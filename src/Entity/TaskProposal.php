<?php

namespace App\Entity;

use App\Repository\TaskProposalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TaskProposalRepository::class)]
class TaskProposal
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?float $proposedPrice = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Agent::class, inversedBy: 'taskProposals')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Agent $agent = null;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'taskProposals')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Task $task = null;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'taskProposal', cascade: ['remove'])]
    private Collection $messages;

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
        $this->messages = new ArrayCollection();
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
