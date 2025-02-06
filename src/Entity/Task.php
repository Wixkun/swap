<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use App\Entity\User;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'tasks')]
    #[ORM\JoinTable(name: 'task_tag')]
    private Collection $tags;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $imagePaths = [];

    #[ORM\Column(length: 50, options: ['default' => 'pending'])]
    private string $status = 'pending';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: TaskProposal::class, mappedBy: 'task', cascade: ['remove'], orphanRemoval: true)]
    private Collection $taskProposals;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->tags = new ArrayCollection();
        $this->status = 'pending';
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->taskProposals = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addTask($this);
        }
        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeTask($this);
        }
        return $this;
    }

    public function getImagePaths(): ?array
    {
        return $this->imagePaths;
    }

    public function setImagePaths(?array $imagePaths): static
    {
        $this->imagePaths = $imagePaths;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function getTaskProposals(): Collection
    {
        return $this->taskProposals;
    }

    public function addTaskProposal(TaskProposal $taskProposal): static
    {
        if (!$this->taskProposals->contains($taskProposal)) {
            $this->taskProposals->add($taskProposal);
            $taskProposal->setTask($this);
        }
        return $this;
    }

    public function removeTaskProposal(TaskProposal $taskProposal): static
    {
        if ($this->taskProposals->removeElement($taskProposal)) {
            if ($taskProposal->getTask() === $this) {
                $taskProposal->setTask(null);
            }
        }
        return $this;
    }

}
