<?php
namespace App\Entity;

use App\Repository\LawyerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: LawyerRepository::class)]
class Lawyer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:120)]
    private string $firstName = '';

    #[ORM\Column(length:120)]
    private string $lastName = '';

    #[ORM\Column(length:150, unique:true)]
    private string $slug = '';

    #[ORM\Column(length:50, nullable:true)]
    private ?string $barNumber = null;

    #[ORM\Column(type:'text', nullable:true)]
    private ?string $biography = null;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $photoUrl = null;

    #[ORM\Column(type:'boolean', options: ['default' => true])]
    private bool $isActive = true;

    // ═══════════════════════════════════════════════════
    // RELATIONS (NOUVELLES)
    // ═══════════════════════════════════════════════════

    /**
     * @var Address|null Adresse complète de l'avocat
     */
    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id', nullable: true)]
    private ?Address $address = null;

    /**
     * @var Cabinet|null Cabinet auquel appartient l'avocat
     */
    #[ORM\ManyToOne(targetEntity: Cabinet::class, inversedBy: 'lawyers')]
    #[ORM\JoinColumn(name: 'cabinet_id', referencedColumnName: 'id', nullable: true)]
    private ?Cabinet $cabinet = null;

    /**
     * @var Collection<int, Phone> Numéros de téléphone de l'avocat
     */
    #[ORM\OneToMany(targetEntity: Phone::class, mappedBy: 'lawyer', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $phones;

    /**
     * @var Collection<int, EmailAddress> Adresses email de l'avocat
     */
    #[ORM\OneToMany(targetEntity: EmailAddress::class, mappedBy: 'lawyer', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $emails;

    /**
     * @var Collection<int, Specialty> Spécialités de l'avocat
     */
    #[ORM\ManyToMany(targetEntity: Specialty::class)]
    #[ORM\JoinTable(name:'lawyer_specialty')]
    private Collection $specialties;

    // ═══════════════════════════════════════════════════
    // ANCIENS CHAMPS (DEPRECATED - Compatibilité)
    // ═══════════════════════════════════════════════════

    /**
     * @deprecated Utiliser $emails->findPrimary() ou $emails[0]
     */
    #[ORM\Column(length:180, nullable:true)]
    private ?string $email = null;

    /**
     * @deprecated Utiliser $phones->findPrimary() ou $phones[0]
     */
    #[ORM\Column(length:50, nullable:true)]
    private ?string $phone = null;

    /**
     * @deprecated Utiliser $address->getCity()
     */
    #[ORM\Column(length:120, nullable:true)]
    private ?string $city = null;

    // ═══════════════════════════════════════════════════
    // CONSTRUCTEUR
    // ═══════════════════════════════════════════════════

    public function __construct()
    {
        $this->specialties = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->emails = new ArrayCollection();
    }

    // ═══════════════════════════════════════════════════
    // GETTERS / SETTERS - Relations Nouvelles
    // ═══════════════════════════════════════════════════

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;
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

    /**
     * @return Collection<int, Phone>
     */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones->add($phone);
            $phone->setLawyer($this);
        }
        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            if ($phone->getLawyer() === $this) {
                $phone->setLawyer(null);
            }
        }
        return $this;
    }

    /**
     * Retourne le téléphone principal
     */
    public function getPrimaryPhone(): ?Phone
    {
        foreach ($this->phones as $phone) {
            if ($phone->isPrimary()) {
                return $phone;
            }
        }
        return $this->phones->first() ?: null;
    }

    /**
     * @return Collection<int, EmailAddress>
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(EmailAddress $email): self
    {
        if (!$this->emails->contains($email)) {
            $this->emails->add($email);
            $email->setLawyer($this);
        }
        return $this;
    }

    public function removeEmail(EmailAddress $email): self
    {
        if ($this->emails->removeElement($email)) {
            if ($email->getLawyer() === $this) {
                $email->setLawyer(null);
            }
        }
        return $this;
    }

    /**
     * Retourne l'email principal
     */
    public function getPrimaryEmail(): ?EmailAddress
    {
        foreach ($this->emails as $email) {
            if ($email->isPrimary()) {
                return $email;
            }
        }
        return $this->emails->first() ?: null;
    }

    /**
     * @return Collection<int, Specialty>
     */
    public function getSpecialties(): Collection
    {
        return $this->specialties;
    }

    public function addSpecialty(Specialty $s): self
    {
        if(!$this->specialties->contains($s)) {
            $this->specialties->add($s);
        }
        return $this;
    }

    public function removeSpecialty(Specialty $s): self
    {
        $this->specialties->removeElement($s);
        return $this;
    }

    // ═══════════════════════════════════════════════════
    // GETTERS / SETTERS - Champs Basiques
    // ═══════════════════════════════════════════════════

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getBarNumber(): ?string { return $this->barNumber; }
    public function setBarNumber(?string $barNumber): self
    {
        $this->barNumber = $barNumber;
        return $this;
    }

    public function getBiography(): ?string { return $this->biography; }
    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;
        return $this;
    }

    public function getPhotoUrl(): ?string { return $this->photoUrl; }
    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    // ═══════════════════════════════════════════════════
    // GETTERS / SETTERS - Champs Deprecated (Compatibilité)
    // ═══════════════════════════════════════════════════

    /**
     * @deprecated Utiliser getPrimaryEmail()->getEmail()
     */
    public function getEmail(): ?string
    {
        // Fallback intelligent
        if ($this->email) return $this->email;
        $primary = $this->getPrimaryEmail();
        return $primary ? $primary->getEmail() : null;
    }

    /**
     * @deprecated
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @deprecated Utiliser getPrimaryPhone()->getNumber()
     */
    public function getPhone(): ?string
    {
        // Fallback intelligent
        if ($this->phone) return $this->phone;
        $primary = $this->getPrimaryPhone();
        return $primary ? $primary->getNumber() : null;
    }

    /**
     * @deprecated
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @deprecated Utiliser getAddress()->getCity()
     */
    public function getCity(): ?string
    {
        // Fallback intelligent
        if ($this->city) return $this->city;
        return $this->address ? $this->address->getCity() : null;
    }

    /**
     * @deprecated
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    // ═══════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════

    /**
     * Retourne le nom complet
     */
    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }
}