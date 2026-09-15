<?php

namespace App\Entity;

use App\Repository\CandidatoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatoRepository::class)]
class Candidato
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null {
        get {
            return $this->id;
        }
    }

    #[ORM\Column(length: 255)]
    private ?string $Nome = null;

    #[ORM\Column(length: 255)]
    private ?string $Numero = null;

    #[ORM\ManyToOne(inversedBy: 'candidatos')]
    private ?Partido $Partido = null;

    public function getNome(): ?string
    {
        return $this->Nome;
    }

    public function setNome(string $Nome): static
    {
        $this->Nome = $Nome;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->Numero;
    }

    public function setNumero(string $Numero): static
    {
        $this->Numero = $Numero;

        return $this;
    }

    public function getPartido(): ?Partido
    {
        return $this->Partido;
    }

    public function setPartido(?Partido $Partido): static
    {
        $this->Partido = $Partido;

        return $this;
    }
}
