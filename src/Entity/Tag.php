<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $idTag = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'idTag')]
    private Collection $idTask;

    public function __construct()
    {
        $this->idTask = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTag(): ?string
    {
        return $this->idTag;
    }

    public function setIdTag(string $idTag): static
    {
        $this->idTag = $idTag;

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

    /**
     * @return Collection<int, Task>
     */
    public function getIdTask(): Collection
    {
        return $this->idTask;
    }

    public function addIdTask(Task $idTask): static
    {
        if (!$this->idTask->contains($idTask)) {
            $this->idTask->add($idTask);
        }

        return $this;
    }

    public function removeIdTask(Task $idTask): static
    {
        $this->idTask->removeElement($idTask);

        return $this;
    }
}
