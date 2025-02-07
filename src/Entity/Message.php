<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $idConversation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $idUser = null;

    #[ORM\ManyToOne(targetEntity: TaskProposal::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?TaskProposal $taskProposal = null;
    
    public function getTaskProposal(): ?TaskProposal
    {
        return $this->taskProposal;
    }
    
    public function setTaskProposal(?TaskProposal $taskProposal): static
    {
        $this->taskProposal = $taskProposal;
        return $this;
    }    

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }
    public function getContent(): ?string
    {
        return $this->content;
    }
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }
    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }
    public function setSentAt(\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;
        return $this;
    }
    public function getIdConversation(): ?Conversation
    {
        return $this->idConversation;
    }
    public function setIdConversation(?Conversation $idConversation): static
    {
        $this->idConversation = $idConversation;
        return $this;
    }
    public function getIdUser(): ?User
    {
        return $this->idUser;
    }
    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;
        return $this;
    }
}
