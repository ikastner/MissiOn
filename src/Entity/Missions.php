<?php

namespace App\Entity;

use App\Repository\MissionsRepository;
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

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $debut = null;

    #[ORM\Column(length: 255)]
    private ?string $fin = null;

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

    #[ORM\ManyToOne(inversedBy: 'missions')]
    private ?Freelance $freelance = null;

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

    public function getDebut(): ?string
    {
        return $this->debut;
    }

    public function setDebut(string $debut): static
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?string
    {
        return $this->fin;
    }

    public function setFin(string $fin): static
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

    public function getFreelance(): ?Freelance
    {
        return $this->freelance;
    }

    public function setFreelance(?Freelance $freelance): static
    {
        $this->freelance = $freelance;

        return $this;
    }
}
