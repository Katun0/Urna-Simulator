<?php

namespace App\Entity;

use App\Repository\VotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VotoRepository::class)]
class Voto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Candidato::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Candidato $candidato = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $dataVoto = null;

    #[ORM\Column(length: 64, unique: true)]
    private ?string $codigoAutenticacao = null;

    #[ORM\Column(length: 32)]
    private ?string $codigoAutenticacaoSalt = null;

    #[ORM\Column]
    private ?bool $isBranco = false;

    public function __construct()
    {
        $this->dataVoto = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidato(): ?Candidato
    {
        return $this->candidato;
    }

    public function setCandidato(?Candidato $candidato): static
    {
        $this->candidato = $candidato;
        return $this;
    }

    public function getDataVoto(): ?\DateTimeImmutable
    {
        return $this->dataVoto;
    }

    public function getCodigoAutenticacao(): ?string
    {
        return $this->codigoAutenticacao;
    }

    public function setCodigoAutenticacao(string $codigoAutenticacao): static
    {
        $this->codigoAutenticacao = $codigoAutenticacao;

        return $this;
    }

    public function getCodigoAutenticacaoSalt(): ?string
    {
        return $this->codigoAutenticacaoSalt;
    }

    public function setCodigoAutenticacaoSalt(string $codigoAutenticacaoSalt): static
    {
        $this->codigoAutenticacaoSalt = $codigoAutenticacaoSalt;

        return $this;
    }

    public function isBranco(): ?bool
    {
        return $this->isBranco;
    }

    public function setIsBranco(bool $isBranco): static
    {
        $this->isBranco = $isBranco;
        return $this;
    }
}
