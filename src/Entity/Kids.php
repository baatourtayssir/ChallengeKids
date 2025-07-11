<?php

namespace App\Entity;

use App\Repository\KidsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KidsRepository::class)]
class Kids extends User
{

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'kids')]
    private Collection $interests;

    public function __construct()
    {
        $this->interests = new ArrayCollection();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getInterests(): Collection
    {
        return $this->interests;
    }

    public function addInterest(Category $interest): static
    {
        if (!$this->interests->contains($interest)) {
            $this->interests->add($interest);
        }

        return $this;
    }

    public function removeInterest(Category $interest): static
    {
        $this->interests->removeElement($interest);

        return $this;
    }
}
