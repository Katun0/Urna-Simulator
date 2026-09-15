<?php

namespace App\Entity;

use App\Repository\PartidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartidoRepository::class)]
class Partido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nome = null;

    /**
     * @var Collection<int, Candidato>
     */
    #[ORM\OneToMany(targetEntity: Candidato::class, mappedBy: 'Partido')]
    private Collection $candidatos;

    public function __construct()
    {
        $this->candidatos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->Nome;
    }

    public function setNome(string $Nome): static
    {
        $this->Nome = $Nome;

        return $this;
    }

    /**
     * @return Collection<int, Candidato>
     */
    public function getCandidatos(): Collection
    {
        return $this->candidatos;
    }

    public function addCandidato(Candidato $candidato): static
    {
        if (!$this->candidatos->contains($candidato)) {
            $this->candidatos->add($candidato);
            $candidato->setPartido($this);
        }

        return $this;
    }

    public function removeCandidato(Candidato $candidato): static
    {
        if ($this->candidatos->removeElement($candidato)) {
            // set the owning side to null (unless already changed)
            if ($candidato->getPartido() === $this) {
                $candidato->setPartido(null);
            }
        }

        return $this;
    }
}
