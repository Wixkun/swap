<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Agent>
     */
    #[ORM\ManyToMany(targetEntity: Agent::class)]
    private Collection $idAgent;

    public function __construct()
    {
        $this->idAgent = new ArrayCollection();

        $this->id = Uuid::v4(); 
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdSkill(): ?string
    {
        return $this->idSkill;
    }

    public function setIdSkill(string $idSkill): static
    {
        $this->idSkill = $idSkill;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Agent>
     */
    public function getIdAgent(): Collection
    {
        return $this->idAgent;
    }

    public function addIdAgent(Agent $idAgent): static
    {
        if (!$this->idAgent->contains($idAgent)) {
            $this->idAgent->add($idAgent);
        }

        return $this;
    }

    public function removeIdAgent(Agent $idAgent): static
    {
        $this->idAgent->removeElement($idAgent);

        return $this;
    }
}
