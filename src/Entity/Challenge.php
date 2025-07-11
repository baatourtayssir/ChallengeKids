<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\VisibilityType;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
class Challenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $visibility = null;


    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Publication $educationalContent = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Publication $mission = null;


    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'fils')]
    private ?self $pere = null;

    /**
     * @return Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'pere', targetEntity: self::class)]
    private Collection $fils;

    #[ORM\ManyToOne(inversedBy: 'Challenges')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    public function __construct()
    {
        $this->fils = new ArrayCollection();
    }

    // ... getters et setters ...

    public function getPere(): ?self
    {
        return $this->pere;
    }

    public function setPere(?self $pere): static
    {
        $this->pere = $pere;
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFils(): Collection
    {
        return $this->fils;
    }

    public function addFils(self $fils): static
    {
        if (!$this->fils->contains($fils)) {
            $this->fils[] = $fils;
            $fils->setPere($this);
        }
        return $this;
    }

    public function removeFils(self $fils): static
    {
        if ($this->fils->removeElement($fils)) {
            if ($fils->getPere() === $this) {
                $fils->setPere(null);
            }
        }
        return $this;
    }

    public function getId(): ?int
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

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(?string $visibility): self
    {
        $this->visibility = $visibility;
        return $this;
    }
    public function getEducationalContent(): ?Publication
    {
        return $this->educationalContent;
    }

    public function setEducationalContent(?Publication $educationalContent): static
    {
        $this->educationalContent = $educationalContent;

        return $this;
    }

    public function getMission(): ?Publication
    {
        return $this->mission;
    }

    public function setMission(?Publication $mission): static
    {
        $this->mission = $mission;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        $this->cours = $cours;

        return $this;
    }
}
