<?php
namespace App\Entity;
use App\Repository\LawyerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: LawyerRepository::class)]
class Lawyer {
  #[ORM\Id] #[ORM\GeneratedValue] #[ORM\Column] private ?int $id = null;
  #[ORM\Column(length:120)] private string $firstName;
  #[ORM\Column(length:120)] private string $lastName;
  #[ORM\Column(length:150, unique:true)] private string $slug;
  #[ORM\Column(length:180, nullable:true)] private ?string $email = null;
  #[ORM\Column(length:50, nullable:true)] private ?string $phone = null;
  #[ORM\Column(length:120, nullable:true)] private ?string $city = null;
  #[ORM\Column(length:50, nullable:true)] private ?string $barNumber = null;
  #[ORM\Column(type:'text', nullable:true)] private ?string $biography = null;
  #[ORM\Column(length:255, nullable:true)] private ?string $photoUrl = null;
  #[ORM\ManyToOne(targetEntity: Cabinet::class)] private ?Cabinet $cabinet = null;
  #[ORM\ManyToMany(targetEntity: Specialty::class)]
  #[ORM\JoinTable(name:'lawyer_specialty')]
  private Collection $specialties;

  public function __construct(){ $this->specialties = new ArrayCollection(); }

  public function getId(): ?int { return $this->id; }
  public function getFirstName(): string { return $this->firstName; }
  public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }
  public function getLastName(): string { return $this->lastName; }
  public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }
  public function getSlug(): string { return $this->slug; }
  public function setSlug(string $slug): self { $this->slug = $slug; return $this; }
  public function getEmail(): ?string { return $this->email; }
  public function setEmail(?string $email): self { $this->email = $email; return $this; }
  public function getPhone(): ?string { return $this->phone; }
  public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }
  public function getCity(): ?string { return $this->city; }
  public function setCity(?string $city): self { $this->city = $city; return $this; }
  public function getBarNumber(): ?string { return $this->barNumber; }
  public function setBarNumber(?string $barNumber): self { $this->barNumber = $barNumber; return $this; }
  public function getBiography(): ?string { return $this->biography; }
  public function setBiography(?string $biography): self { $this->biography = $biography; return $this; }
  public function getPhotoUrl(): ?string { return $this->photoUrl; }
  public function setPhotoUrl(?string $photoUrl): self { $this->photoUrl = $photoUrl; return $this; }
  public function getCabinet(): ?Cabinet { return $this->cabinet; }
  public function setCabinet(?Cabinet $cabinet): self { $this->cabinet = $cabinet; return $this; }
  /** @return Collection<int, Specialty> */
  public function getSpecialties(): Collection { return $this->specialties; }
  public function addSpecialty(Specialty $s): self { if(!$this->specialties->contains($s)) $this->specialties->add($s); return $this; }
  public function removeSpecialty(Specialty $s): self { $this->specialties->removeElement($s); return $this; }
}
