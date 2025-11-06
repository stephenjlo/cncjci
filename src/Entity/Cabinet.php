<?php
namespace App\Entity;
use App\Repository\CabinetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CabinetRepository::class)]
class Cabinet {
  #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id = null;
  #[ORM\Column(length:180)] private string $name;
  #[ORM\Column(length:180, unique:true)] private string $slug;
  #[ORM\Column(length:20)] private string $type = 'Cabinet';
  #[ORM\Column(length:180, nullable:true)] private ?string $email = null;
  #[ORM\Column(length:50, nullable:true)] private ?string $phone = null;
  #[ORM\Column(length:255, nullable:true)] private ?string $website = null;
  #[ORM\Column(length:255, nullable:true)] private ?string $address = null;
  #[ORM\Column(length:120, nullable:true)] private ?string $city = null;
  #[ORM\Column(nullable:true)] private ?float $lat = null;
  #[ORM\Column(nullable:true)] private ?float $lng = null;
  #[ORM\Column(length:255, nullable:true)] private ?string $logoUrl = null;
  #[ORM\Column()] private bool $isActive = true;

  public function getId(): ?int { return $this->id; }
  public function getName(): string { return $this->name; }
  public function setName(string $name): self { $this->name = $name; return $this; }
  public function getSlug(): string { return $this->slug; }
  public function setSlug(string $slug): self { $this->slug = $slug; return $this; }
  public function getType(): string { return $this->type; }
  public function setType(string $type): self { $this->type = $type; return $this; }
  public function getEmail(): ?string { return $this->email; }
  public function setEmail(?string $email): self { $this->email = $email; return $this; }
  public function getPhone(): ?string { return $this->phone; }
  public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }
  public function getWebsite(): ?string { return $this->website; }
  public function setWebsite(?string $website): self { $this->website = $website; return $this; }
  public function getAddress(): ?string { return $this->address; }
  public function setAddress(?string $address): self { $this->address = $address; return $this; }
  public function getCity(): ?string { return $this->city; }
  public function setCity(?string $city): self { $this->city = $city; return $this; }
  public function getLat(): ?float { return $this->lat; }
  public function setLat(?float $lat): self { $this->lat = $lat; return $this; }
  public function getLng(): ?float { return $this->lng; }
  public function setLng(?float $lng): self { $this->lng = $lng; return $this; }
  public function getLogoUrl(): ?string { return $this->logoUrl; }
  public function setLogoUrl(?string $logoUrl): self { $this->logoUrl = $logoUrl; return $this; }
  public function isActive(): bool { return $this->isActive; }
  public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }
}
