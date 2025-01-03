<?php

namespace App\Entity;

use App\Repository\MissionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionsRepository::class)]
class Missions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    // #[ORM\Column(length: 255)]
    // private ?string $debut = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $debut = null;

    // #[ORM\Column(length: 255)]
    // private ?string $fin = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fin = null;

    #[ORM\Column(length: 255)]
    private ?string $estimation = null;

    #[ORM\Column(length: 255)]
    private ?string $tjm = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $niveau = null;

    #[ORM\Column(length: 255)]
    private ?string $taille_projet = null;

    #[ORM\ManyToOne(inversedBy: 'missions')]
    private ?Personel $personel = null;

    /**
     * @var Collection<int, Candidature>
     */
    #[ORM\OneToMany(targetEntity: Candidature::class, mappedBy: 'mission')]
    private Collection $candidatures;

    /**
     * @var Collection<int, Competence>
     */
    #[ORM\ManyToMany(targetEntity: Competence::class, mappedBy: 'mission')]
    private Collection $competences;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
        $this->competences = new ArrayCollection();
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

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): static
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): static
    {
        $this->fin = $fin;

        return $this;
    }

    public function getEstimation(): ?string
    {
        return $this->estimation;
    }

    public function setEstimation(string $estimation): static
    {
        $this->estimation = $estimation;

        return $this;
    }

    public function getTjm(): ?string
    {
        return $this->tjm;
    }

    public function setTjm(string $tjm): static
    {
        $this->tjm = $tjm;

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

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getTailleProjet(): ?string
    {
        return $this->taille_projet;
    }

    public function setTailleProjet(string $taille_projet): static
    {
        $this->taille_projet = $taille_projet;

        return $this;
    }

    public function getPersonel(): ?Personel
    {
        return $this->personel;
    }

    public function setPersonel(?Personel $personel): static
    {
        $this->personel = $personel;

        return $this;
    }

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setMission($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getMission() === $this) {
                $candidature->setMission(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Competence>
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): static
    {
        if (!$this->competences->contains($competence)) {
            $this->competences->add($competence);
            $competence->addMission($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): static
    {
        if ($this->competences->removeElement($competence)) {
            $competence->removeMission($this);
        }

        return $this;
    }

}
