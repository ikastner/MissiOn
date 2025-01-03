<?php

namespace App\Entity;

use App\Repository\CompetenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetenceRepository::class)]
class Competence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Freelance>
     */
    #[ORM\ManyToMany(targetEntity: Freelance::class, inversedBy: 'competences')]
    private Collection $freelance;

    /**
     * @var Collection<int, Missions>
     */
    #[ORM\ManyToMany(targetEntity: Missions::class, inversedBy: 'competences')]
    private Collection $mission;

    public function __construct()
    {
        $this->freelance = new ArrayCollection();
        $this->mission = new ArrayCollection();
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

    /**
     * @return Collection<int, Freelance>
     */
    public function getFreelance(): Collection
    {
        return $this->freelance;
    }

    public function addFreelance(Freelance $freelance): static
    {
        if (!$this->freelance->contains($freelance)) {
            $this->freelance->add($freelance);
        }

        return $this;
    }

    public function removeFreelance(Freelance $freelance): static
    {
        $this->freelance->removeElement($freelance);

        return $this;
    }

    /**
     * @return Collection<int, Missions>
     */
    public function getMission(): Collection
    {
        return $this->mission;
    }

    public function addMission(Missions $mission): static
    {
        if (!$this->mission->contains($mission)) {
            $this->mission->add($mission);
        }

        return $this;
    }

    public function removeMission(Missions $mission): static
    {
        $this->mission->removeElement($mission);

        return $this;
    }
}
