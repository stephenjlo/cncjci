<?php
namespace App\Entity;

use App\Repository\CabinetRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CabinetRepository::class)]
class Cabinet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:180)]
    private string $name;

    #[ORM\Column(length:180, unique:true)]
    private string $slug;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $website = null;

    #[ORM\Column(type:'text', nullable:true)]
    private ?string $description = null;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $logoUrl = null;

    #[ORM\Column()]
    private bool $isActive = true;

    // ═══════════════════════════════════════════════════
    // RELATIONS (NOUVELLES)
    // ═══════════════════════════════════════════════════

    /**
     * @var CabinetType|null Type du cabinet (Cabinet, SCP, SCPA)
     */
    #[ORM\ManyToOne(targetEntity: CabinetType::class)]
    #[ORM\JoinColumn(name: 'type_id', referencedColumnName: 'id', nullable: true)]
    private ?CabinetType $typeEntity = null;

    /**
     * @var Lawyer|null Associé gérant du cabinet
     */
    #[ORM\ManyToOne(targetEntity: Lawyer::class)]
    #[ORM\JoinColumn(name: 'managing_partner_id', referencedColumnName: 'id', nullable: true)]
    private ?Lawyer $managingPartner = null;

    /**
     * @var Address|null Adresse complète du cabinet
     */
    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id', nullable: true)]
    private ?Address $address = null;

    /**
     * @var Collection<int, Phone> Numéros de téléphone du cabinet
     */
    #[ORM\OneToMany(targetEntity: Phone::class, mappedBy: 'cabinet', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $phones;

    /**
     * @var Collection<int, EmailAddress> Adresses email du cabinet
     */
    #[ORM\OneToMany(targetEntity: EmailAddress::class, mappedBy: 'cabinet', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $emails;

    /**
     * @var Collection<int, Lawyer> Avocats du cabinet
     */
    #[ORM\OneToMany(targetEntity: Lawyer::class, mappedBy: 'cabinet')]
    private Collection $lawyers;

    // ═══════════════════════════════════════════════════
    // ANCIENS CHAMPS (DEPRECATED - Compatibilité)
    // ═══════════════════════════════════════════════════

    /**
     * @deprecated Utiliser $typeEntity
     */
    #[ORM\Column(length:20)]
    private string $type = 'Cabinet';

    /**
     * @deprecated Utiliser $emails
     */
    #[ORM\Column(length:180, nullable:true)]
    private ?string $email = null;

    /**
     * @deprecated Utiliser $phones
     */
    #[ORM\Column(length:50, nullable:true)]
    private ?string $phone = null;

    /**
     * @deprecated Utiliser $address
     */
    #[ORM\Column(length:255, nullable:true)]
    private ?string $oldAddress = null;

    /**
     * @deprecated Utiliser $address->getCity()
     */
    #[ORM\Column(length:120, nullable:true)]
    private ?string $city = null;

    /**
     * @deprecated Utiliser $address->getLat()
     */
    #[ORM\Column(nullable:true)]
    private ?float $lat = null;

    /**
     * @deprecated Utiliser $address->getLng()
     */
    #[ORM\Column(nullable:true)]
    private ?float $lng = null;

    // ═══════════════════════════════════════════════════
    // CONSTRUCTEUR
    // ═══════════════════════════════════════════════════

    public function __construct()
    {
        $this->phones = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
    }

    // ═══════════════════════════════════════════════════
    // GETTERS / SETTERS - Relations Nouvelles
    // ═══════════════════════════════════════════════════

    public function getTypeEntity(): ?CabinetType
    {
        return $this->typeEntity;
    }

    public function setTypeEntity(?CabinetType $type): self
    {
        $this->typeEntity = $type;
        return $this;
    }

    public function getManagingPartner(): ?Lawyer
    {
        return $this->managingPartner;
    }

    public function setManagingPartner(?Lawyer $lawyer): self
    {
        $this->managingPartner = $lawyer;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;
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
            $phone->setCabinet($this);
        }
        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->removeElement($phone)) {
            if ($phone->getCabinet() === $this) {
                $phone->setCabinet(null);
            }
        }
        return $this;
    }

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
            $email->setCabinet($this);
        }
        return $this;
    }

    public function removeEmail(EmailAddress $email): self
    {
        if ($this->emails->removeElement($email)) {
            if ($email->getCabinet() === $this) {
                $email->setCabinet(null);
            }
        }
        return $this;
    }

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
     * @return Collection<int, Lawyer>
     */
    public function getLawyers(): Collection
    {
        return $this->lawyers;
    }

    // ═══════════════════════════════════════════════════
    // GETTERS / SETTERS - Champs Basiques
    // ═══════════════════════════════════════════════════

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getLogoUrl(): ?string { return $this->logoUrl; }
    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;
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
     * @deprecated Utiliser getTypeEntity()
     */
    public function getType(): string|CabinetType|null
    {
        // Pour compatibilité API, retourner l'objet si disponible
        if ($this->typeEntity) {
            return $this->typeEntity;
        }
        return $this->type;
    }

    /**
     * @deprecated
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @deprecated Utiliser getPrimaryEmail()->getEmail()
     */
    public function getEmail(): ?string
    {
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

    /**
     * @deprecated Utiliser getAddress()->getLat()
     */
    public function getLat(): ?float
    {
        if ($this->lat !== null) return $this->lat;
        return $this->address ? $this->address->getLat() : null;
    }

    /**
     * @deprecated
     */
    public function setLat(?float $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @deprecated Utiliser getAddress()->getLng()
     */
    public function getLng(): ?float
    {
        if ($this->lng !== null) return $this->lng;
        return $this->address ? $this->address->getLng() : null;
    }

    /**
     * @deprecated
     */
    public function setLng(?float $lng): self
    {
        $this->lng = $lng;
        return $this;
    }
}