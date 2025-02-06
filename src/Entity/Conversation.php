<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $id = null;

    public function __construct()
    {
        $this->id = Uuid::v4(); 
    }

    #[ORM\Column]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'conversation')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Customer $idCustomer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agent $idAgent = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdConversation(): ?string
    {
        return $this->idConversation;
    }

    public function setIdConversation(string $idConversation): static
    {
        $this->idConversation = $idConversation;

        return $this;
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
}
