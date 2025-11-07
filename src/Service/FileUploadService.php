<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService
{
    public const DEFAULT_CABINET_LOGO = 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png';
    public const DEFAULT_LAWYER_PHOTO = null; // Ou une URL par défaut si nécessaire

    public function __construct(
        private string $uploadsDirectory,
        private SluggerInterface $slugger
    ) {}

    /**
     * Upload un fichier et retourne l'URL complète
     *
     * @param UploadedFile $file Le fichier uploadé
     * @param string $subfolder Sous-dossier (ex: 'cabinets', 'lawyers')
     * @return string L'URL complète du fichier
     * @throws FileException
     */
    public function upload(UploadedFile $file, string $subfolder = ''): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $targetDirectory = $this->uploadsDirectory;
        if ($subfolder) {
            $targetDirectory .= '/' . $subfolder;
        }

        // Créer le dossier si nécessaire
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        try {
            $file->move($targetDirectory, $newFilename);
        } catch (FileException $e) {
            throw new FileException('Erreur lors de l\'upload du fichier: ' . $e->getMessage());
        }

        // Retourner l'URL complète
        // Note: Vous devrez configurer le baseUrl dans services.yaml
        $relativePath = ($subfolder ? $subfolder . '/' : '') . $newFilename;
        return '/uploads/' . $relativePath;
    }

    /**
     * Supprime un fichier uploadé
     *
     * @param string $fileUrl L'URL du fichier à supprimer
     * @return bool True si supprimé, false sinon
     */
    public function delete(string $fileUrl): bool
    {
        // Extraire le chemin relatif de l'URL
        if (str_starts_with($fileUrl, '/uploads/')) {
            $relativePath = str_replace('/uploads/', '', $fileUrl);
            $fullPath = $this->uploadsDirectory . '/' . $relativePath;

            if (file_exists($fullPath)) {
                return unlink($fullPath);
            }
        }

        return false;
    }

    /**
     * Retourne l'URL du logo par défaut pour les cabinets
     */
    public function getDefaultCabinetLogo(): string
    {
        return self::DEFAULT_CABINET_LOGO;
    }

    /**
     * Retourne l'URL de la photo par défaut pour les lawyers
     */
    public function getDefaultLawyerPhoto(): ?string
    {
        return self::DEFAULT_LAWYER_PHOTO;
    }
}
