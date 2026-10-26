<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_TITULO_ELEITOR', fields: ['TituloEleitor'])]
#[UniqueEntity(fields: ['TituloEleitor'], message: 'Este título de eleitor já está cadastrado.')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(length: 20, unique: true)]
    private ?string $TituloEleitor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Documento = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\ManyToOne(targetEntity: CustomRole::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?CustomRole $customRole = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function getID(): ?int
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

    public function setDocumento(?string $Documento): static
    {
        $this->Documento = $Documento;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->TituloEleitor;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getCustomRole(): ?CustomRole
    {
        return $this->customRole;
    }

    public function setCustomRole(?CustomRole $customRole): static
    {
        $this->customRole = $customRole;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
