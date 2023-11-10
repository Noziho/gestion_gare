<?php

namespace App\Entity;

use App\Repository\TrainStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrainStationRepository::class)]
class TrainStation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $localisation = null;

    #[ORM\OneToMany(mappedBy: 'trainStation', targetEntity: Item::class)]
    private Collection $lostIte�m;

    public function __construct()
    {
        $this->lostIte�m = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getLostIte�m(): Collection
    {
        return $this->lostIte�m;
    }

    public function addLostIteM(Item $lostIteM): static
    {
        if (!$this->lostIte�m->contains($lostIteM)) {
            $this->lostIte�m->add($lostIteM);
            $lostIteM->setTrainStation($this);
        }

        return $this;
    }

    public function removeLostIteM(Item $lostIteM): static
    {
        if ($this->lostIte�m->removeElement($lostIteM)) {
            // set the owning side to null (unless already changed)
            if ($lostIteM->getTrainStation() === $this) {
                $lostIteM->setTrainStation(null);
            }
        }

        return $this;
    }
}
