<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'image')]
#[ORM\Index(columns: ['entity_type', 'entity_id'], name: 'idx_entity')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Type de l'entité parente (Cabinet, Lawyer, etc.)
     */
    #[ORM\Column(length: 50)]
    private string $entityType;

    /**
     * ID de l'entité parente
     */
    #[ORM\Column]
    private int $entityId;

    /**
     * Nom du fichier original
     */
    #[ORM\Column(length: 255)]
    private string $filename;

    /**
     * Chemin relatif du fichier (ex: /uploads/cabinets/images/xxx.jpg)
     */
    #[ORM\Column(length: 255)]
    private string $filepath;

    /**
     * Label/Titre de l'image (ex: "Photo de l'équipe")
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    /**
     * Catégorie de l'image (ex: "photo_profil", "locaux", "equipe")
     */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $category = null;

    /**
     * Description optionnelle
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * Type MIME (image/jpeg, image/png, etc.)
     */
    #[ORM\Column(length: 50)]
    private string $mimeType;

    /**
     * Taille du fichier en octets
     */
    #[ORM\Column]
    private int $fileSize;

    /**
     * Position d'affichage (ordre)
     */
    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    /**
     * Image principale (pour l'entité)
     */
    #[ORM\Column(options: ['default' => false])]
    private bool $isPrimary = false;

    /**
     * Date d'upload
     */
    #[ORM\Column]
    private \DateTimeImmutable $uploadedAt;

    public function __construct()
    {
        $this->uploadedAt = new \DateTimeImmutable();
    }

    // ═══════════════════════════════════════════════════
    // GETTERS / SETTERS
    // ═══════════════════════════════════════════════════

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): self
    {
        $this->entityType = $entityType;
        return $this;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function setFilepath(string $filepath): self
    {
        $this->filepath = $filepath;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    public function getUploadedAt(): \DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;
        return $this;
    }

    // ═══════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════

    /**
     * Retourne l'URL complète du fichier
     */
    public function getUrl(): string
    {
        return $this->filepath;
    }

    /**
     * Retourne la taille formatée (ex: "1.2 MB")
     */
    public function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->fileSize;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Vérifie si c'est une image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }
}
