<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $line1 = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $line2 = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $country = 'Côte d\'Ivoire';

    #[ORM\Column(nullable: true)]
    private ?float $lat = null;

    #[ORM\Column(nullable: true)]
    private ?float $lng = null;

    public function getId(): ?int { return $this->id; }
    public function getLine1(): ?string { return $this->line1; }
    public function setLine1(?string $v): self { $this->line1 = $v; return $this; }
    public function getLine2(): ?string { return $this->line2; }
    public function setLine2(?string $v): self { $this->line2 = $v; return $this; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $v): self { $this->city = $v; return $this; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(?string $v): self { $this->postalCode = $v; return $this; }
    public function getCountry(): ?string { return $this->country; }
    public function setCountry(?string $v): self { $this->country = $v; return $this; }
    public function getLat(): ?float { return $this->lat; }
    public function setLat(?float $v): self { $this->lat = $v; return $this; }
    public function getLng(): ?float { return $this->lng; }
    public function setLng(?float $v): self { $this->lng = $v; return $this; }
}
