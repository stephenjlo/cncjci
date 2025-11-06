<?php
namespace App\Entity;
use App\Repository\SpecialtyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialtyRepository::class)]
class Specialty {
  #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id = null;
  #[ORM\Column(length:120)] private string $name;
  #[ORM\Column(length:120, unique:true)] private string $slug;
  #[ORM\Column(type:'text', nullable:true)] private ?string $description = null;

  public function getId(): ?int { return $this->id; }
  public function getName(): string { return $this->name; }
  public function setName(string $name): self { $this->name = $name; return $this; }
  public function getSlug(): string { return $this->slug; }
  public function setSlug(string $slug): self { $this->slug = $slug; return $this; }
  public function getDescription(): ?string { return $this->description; }
  public function setDescription(?string $description): self { $this->description = $description; return $this; }
}
