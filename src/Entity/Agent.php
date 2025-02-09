<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\OneToMany(targetEntity: \App\Entity\TaskProposal::class, mappedBy: 'agent', cascade: ['remove'], orphanRemoval: true)]
    private Collection $taskProposals;

    #[ORM\Column(length: 100)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    #[ORM\Column(nullable: true)]
    private ?float $ratingGlobal = null;

    #[ORM\OneToOne(inversedBy: 'idAgent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?\App\Entity\User $idUser = null;

    #[ORM\ManyToMany(targetEntity: \App\Entity\Skill::class, inversedBy: 'agents')]
    #[ORM\JoinTable(name: 'agent_skill')]
    private Collection $skills;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->taskProposals = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getRatingGlobal(): ?float
    {
        return $this->ratingGlobal;
    }

    public function setRatingGlobal(?float $ratingGlobal): self
    {
        $this->ratingGlobal = $ratingGlobal;
        return $this;
    }

    public function getIdUser(): ?\App\Entity\User
    {
        return $this->idUser;
    }

    public function setIdUser(\App\Entity\User $user): self
    {
        $this->idUser = $user;
        return $this;
    }

    /**
     * @return Collection<int, \App\Entity\Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(\App\Entity\Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->addAgent($this);
        }
        return $this;
    }

    public function removeSkill(\App\Entity\Skill $skill): self
    {
        if ($this->skills->removeElement($skill)) {
            $skill->removeAgent($this);
        }
        return $this;
    }
}
