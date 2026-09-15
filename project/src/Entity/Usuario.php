<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Email = null;

    #[ORM\Column]
    private ?int $Idade = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $TituloEleitor = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Documento = null;

    public function getNome(): ?string
    {
        return $this->Nome;
    }

    public function setNome(string $Nome): static
    {
        $this->Nome = $Nome;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getIdade(): ?int
    {
        return $this->Idade;
    }

    public function setIdade(int $Idade): static
    {
        $this->Idade = $Idade;

        return $this;
    }

    public function getTituloEleitor(): ?string
    {
        return $this->TituloEleitor;
    }

    public function setTituloEleitor(string $TituloEleitor): static
    {
        $this->TituloEleitor = $TituloEleitor;

        return $this;
    }

    public function getDocumento(): ?string
    {
        return $this->Documento;
    }

    public function setDocumento(string $Documento): static
    {
        $this->Documento = $Documento;

        return $this;
    }
}
