<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EmailAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPrimary = false;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    #[ORM\ManyToOne(inversedBy: 'emails')]
    private ?Lawyer $lawyer = null;

    #[ORM\ManyToOne(inversedBy: 'emails')]
    private ?Cabinet $cabinet = null;

    public function getId(): ?int { return $this->id; }
    public function getLabel(): ?string { return $this->label; }
    public function setLabel(?string $label): self { $this->label = $label; return $this; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function isPrimary(): bool { return $this->isPrimary; }
    public function setIsPrimary(bool $isPrimary): self { $this->isPrimary = $isPrimary; return $this; }
    public function getPosition(): int { return $this->position; }
    public function setPosition(int $position): self { $this->position = $position; return $this; }
    public function getLawyer(): ?Lawyer { return $this->lawyer; }
    public function setLawyer(?Lawyer $lawyer): self { $this->lawyer = $lawyer; return $this; }
    public function getCabinet(): ?Cabinet { return $this->cabinet; }
    public function setCabinet(?Cabinet $cabinet): self { $this->cabinet = $cabinet; return $this; }
}
