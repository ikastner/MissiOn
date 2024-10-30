<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\OneToOne(mappedBy: 'entreprise', cascade: ['persist', 'remove'])]
    private ?Gestionnaire $gestionnaire = null;

    /**
     * @var Collection<int, Personel>
     */
    #[ORM\OneToMany(targetEntity: Personel::class, mappedBy: 'entreprise')]
    private Collection $personels;

    public function __construct()
    {
        $this->personels = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getGestionnaire(): ?Gestionnaire
    {
        return $this->gestionnaire;
    }

    public function setGestionnaire(?Gestionnaire $gestionnaire): static
    {
        // unset the owning side of the relation if necessary
        if ($gestionnaire === null && $this->gestionnaire !== null) {
            $this->gestionnaire->setEntreprise(null);
        }

        // set the owning side of the relation if necessary
        if ($gestionnaire !== null && $gestionnaire->getEntreprise() !== $this) {
            $gestionnaire->setEntreprise($this);
        }

        $this->gestionnaire = $gestionnaire;

        return $this;
    }

    /**
     * @return Collection<int, Personel>
     */
    public function getPersonels(): Collection
    {
        return $this->personels;
    }

    public function addPersonel(Personel $personel): static
    {
        if (!$this->personels->contains($personel)) {
            $this->personels->add($personel);
            $personel->setEntreprise($this);
        }

        return $this;
    }

    public function removePersonel(Personel $personel): static
    {
        if ($this->personels->removeElement($personel)) {
            // set the owning side to null (unless already changed)
            if ($personel->getEntreprise() === $this) {
                $personel->setEntreprise(null);
            }
        }

        return $this;
    }
}
