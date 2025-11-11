<?php
namespace App\Service;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Service de gestion des images (réutilisable pour toutes les entités)
 */
class ImageManager
{
    private const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 MB
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public function __construct(
        private EntityManagerInterface $em,
        private ImageRepository $imageRepository,
        private string $uploadsDirectory,
        private SluggerInterface $slugger,
        private ?string $baseUrl = null
    ) {}

    /**
     * Upload une image pour une entité
     *
     * @param UploadedFile $file Fichier uploadé
     * @param string $entityType Type de l'entité (Cabinet, Lawyer, etc.)
     * @param int $entityId ID de l'entité
     * @param array $options Options supplémentaires (label, category, description, isPrimary)
     * @return Image
     * @throws \Exception
     */
    public function uploadImage(
        UploadedFile $file,
        string $entityType,
        int $entityId,
        array $options = []
    ): Image {
        // Validation du type de fichier
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('Type de fichier non autorisé. Formats acceptés : JPEG, PNG, GIF, WebP');
        }

        // Validation de la taille
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('Fichier trop volumineux. Taille maximum : 2 Mo');
        }

        // Génération du nom de fichier unique
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        // Détermination du dossier de destination
        $subfolder = strtolower($entityType) . 's/images'; // Ex: cabinets/images, lawyers/images
        $targetDirectory = $this->uploadsDirectory . '/' . $subfolder;

        // Création du dossier si nécessaire
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        // Upload du fichier
        try {
            $file->move($targetDirectory, $newFilename);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'upload du fichier : ' . $e->getMessage());
        }

        // Création de l'entité Image
        $image = new Image();
        $image->setEntityType($entityType);
        $image->setEntityId($entityId);
        $image->setFilename($file->getClientOriginalName());
        $image->setFilepath('/uploads/' . $subfolder . '/' . $newFilename);
        $image->setMimeType($file->getMimeType());
        $image->setFileSize($file->getSize());

        // Options supplémentaires
        if (isset($options['label'])) {
            $image->setLabel($options['label']);
        }
        if (isset($options['category'])) {
            $image->setCategory($options['category']);
        }
        if (isset($options['description'])) {
            $image->setDescription($options['description']);
        }
        if (isset($options['position'])) {
            $image->setPosition($options['position']);
        }
        if (isset($options['isPrimary'])) {
            // Si c'est l'image principale, désactiver les autres
            if ($options['isPrimary']) {
                $this->unsetAllPrimaryImages($entityType, $entityId);
            }
            $image->setIsPrimary($options['isPrimary']);
        }

        $this->em->persist($image);
        $this->em->flush();

        return $image;
    }

    /**
     * Supprime une image
     *
     * @param Image $image Image à supprimer
     * @return bool True si supprimé, false sinon
     */
    public function deleteImage(Image $image): bool
    {
        // Suppression du fichier physique
        $fullPath = $this->uploadsDirectory . $image->getFilepath();
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Suppression de l'entité
        $this->em->remove($image);
        $this->em->flush();

        return true;
    }

    /**
     * Définit une image comme principale
     *
     * @param Image $image Image à définir comme principale
     */
    public function setPrimaryImage(Image $image): void
    {
        // Désactiver toutes les autres images principales
        $this->unsetAllPrimaryImages($image->getEntityType(), $image->getEntityId());

        // Activer celle-ci
        $image->setIsPrimary(true);
        $this->em->flush();
    }

    /**
     * Désactive toutes les images principales d'une entité
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     */
    private function unsetAllPrimaryImages(string $entityType, int $entityId): void
    {
        $images = $this->imageRepository->findByEntity($entityType, $entityId);
        foreach ($images as $img) {
            if ($img->isPrimary()) {
                $img->setIsPrimary(false);
            }
        }
        $this->em->flush();
    }

    /**
     * Met à jour l'ordre des images
     *
     * @param array $imageIds Tableau d'IDs dans le nouvel ordre
     */
    public function updateImagesOrder(array $imageIds): void
    {
        foreach ($imageIds as $position => $imageId) {
            $image = $this->imageRepository->find($imageId);
            if ($image) {
                $image->setPosition($position);
            }
        }
        $this->em->flush();
    }

    /**
     * Récupère toutes les images d'une entité
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     * @return Image[]
     */
    public function getEntityImages(string $entityType, int $entityId): array
    {
        return $this->imageRepository->findByEntity($entityType, $entityId);
    }

    /**
     * Récupère l'image principale d'une entité
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     * @return Image|null
     */
    public function getPrimaryImage(string $entityType, int $entityId): ?Image
    {
        return $this->imageRepository->findPrimaryImage($entityType, $entityId);
    }

    /**
     * Convertit une URL relative en URL absolue
     *
     * @param string|null $filepath Chemin du fichier
     * @return string|null
     */
    public function getAbsoluteUrl(?string $filepath): ?string
    {
        if (!$filepath) {
            return null;
        }

        // Si l'URL est déjà absolue, la retourner telle quelle
        if (str_starts_with($filepath, 'http://') || str_starts_with($filepath, 'https://')) {
            return $filepath;
        }

        // Sinon, ajouter le baseUrl si défini
        if ($this->baseUrl && str_starts_with($filepath, '/')) {
            return rtrim($this->baseUrl, '/') . $filepath;
        }

        return $filepath;
    }

    /**
     * Récupère les images par catégorie
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     * @param string $category Catégorie
     * @return Image[]
     */
    public function getImagesByCategory(string $entityType, int $entityId, string $category): array
    {
        return $this->imageRepository->findByEntityAndCategory($entityType, $entityId, $category);
    }

    /**
     * Compte le nombre d'images d'une entité
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     * @return int
     */
    public function countEntityImages(string $entityType, int $entityId): int
    {
        return $this->imageRepository->countByEntity($entityType, $entityId);
    }
}
