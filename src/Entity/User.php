<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /**
     * @var array<string> Rôles de l'utilisateur
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 120)]
    private string $firstName;

    #[ORM\Column(length: 120)]
    private string $lastName;

    #[ORM\Column]
    private bool $isActive = true;

    /**
     * @var Lawyer|null Si c'est un lawyer, référence vers son profil
     */
    #[ORM\OneToOne(targetEntity: Lawyer::class)]
    #[ORM\JoinColumn(name: 'lawyer_id', nullable: true)]
    private ?Lawyer $lawyer = null;

    /**
     * @var Cabinet|null Si c'est un RESPO_CABINET, son cabinet
     */
    #[ORM\ManyToOne(targetEntity: Cabinet::class)]
    #[ORM\JoinColumn(name: 'cabinet_id', nullable: true)]
    private ?Cabinet $cabinet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantir que tous les users ont au moins ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des données sensibles temporaires, effacez-les ici
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getLawyer(): ?Lawyer
    {
        return $this->lawyer;
    }

    public function setLawyer(?Lawyer $lawyer): self
    {
        $this->lawyer = $lawyer;
        return $this;
    }

    public function getCabinet(): ?Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(?Cabinet $cabinet): self
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    // ═══════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function isSuperAdmin(): bool
    {
        return in_array('ROLE_SUPER_ADMIN', $this->roles, true);
    }

    public function isRespoCabinet(): bool
    {
        return in_array('ROLE_RESPO_CABINET', $this->roles, true);
    }

    public function isLawyer(): bool
    {
        return in_array('ROLE_LAWYER', $this->roles, true);
    }
}