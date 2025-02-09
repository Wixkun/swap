<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Customer $idCustomer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Agent $idAgent = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): static
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getIdCustomer(): ?Customer
    {
        return $this->idCustomer;
    }

    public function setIdCustomer(?Customer $idCustomer): static
    {
        $this->idCustomer = $idCustomer;
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

    public function __toString(): string
    {
        return $this->id ? $this->id->__toString() : '';
    }
}
