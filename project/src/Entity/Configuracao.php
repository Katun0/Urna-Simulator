<?php

namespace App\Entity;

use App\Repository\ConfiguracaoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfiguracaoRepository::class)]
class Configuracao
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $eleicaoFinalizada = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isEleicaoFinalizada(): ?bool
    {
        return $this->eleicaoFinalizada;
    }

    public function setEleicaoFinalizada(bool $eleicaoFinalizada): static
    {
        $this->eleicaoFinalizada = $eleicaoFinalizada;

        return $this;
    }
}
