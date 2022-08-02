<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'userA', targetEntity: Swipes::class, orphanRemoval: true)]
    private Collection $swipes;

    #[ORM\OneToMany(mappedBy: 'userA', targetEntity: Pairs::class)]
    private Collection $pairs;

    public function __construct()
    {
        $this->swipes = new ArrayCollection();
        $this->pairs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Swipes>
     */
    public function getSwipes(): Collection
    {
        return $this->swipes;
    }

    public function addSwipe(Swipes $swipe): self
    {
        if (!$this->swipes->contains($swipe)) {
            $this->swipes->add($swipe);
            $swipe->setUserA($this);
        }

        return $this;
    }

    public function removeSwipe(Swipes $swipe): self
    {
        if ($this->swipes->removeElement($swipe)) {
            // set the owning side to null (unless already changed)
            if ($swipe->getUserA() === $this) {
                $swipe->setUserA(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pairs>
     */
    public function getPairs(): Collection
    {
        return $this->pairs;
    }

    public function addPair(Pairs $pair): self
    {
        if (!$this->pairs->contains($pair)) {
            $this->pairs->add($pair);
            $pair->setUserA($this);
        }

        return $this;
    }

    public function removePair(Pairs $pair): self
    {
        if ($this->pairs->removeElement($pair)) {
            // set the owning side to null (unless already changed)
            if ($pair->getUserA() === $this) {
                $pair->setUserA(null);
            }
        }

        return $this;
    }
}
