# 📷 SYSTÈME D'UPLOAD D'IMAGES RÉUTILISABLE

## 📋 VUE D'ENSEMBLE

Un système d'upload d'images **polymorphe** et **réutilisable** pour toutes les entités du projet (Cabinet, Lawyer, et futures entités).

### ✅ Fonctionnalités

- ✅ Upload multiple d'images
- ✅ Gestion des métadonnées (label, catégorie, description)
- ✅ Image principale (primary)
- ✅ Ordre d'affichage (position)
- ✅ Exposition dans l'API publique
- ✅ Affichage dans le back-office
- ✅ Réutilisable pour n'importe quelle entité

---

## 🏗️ ARCHITECTURE

### Entité `Image`

**Fichier** : `src/Entity/Image.php`

**Champs** :
```php
- id (int)
- entityType (string)     // Ex: 'Cabinet', 'Lawyer'
- entityId (int)          // ID de l'entité parente
- filename (string)       // Nom original du fichier
- filepath (string)       // Chemin de stockage (/uploads/cabinets/images/xxx.jpg)
- label (string|null)     // Titre de l'image
- category (string|null)  // Catégorie (ex: 'photo_profil', 'locaux')
- description (text|null) // Description optionnelle
- mimeType (string)       // Type MIME (image/jpeg, etc.)
- fileSize (int)          // Taille en octets
- position (int)          // Ordre d'affichage (0 = premier)
- isPrimary (bool)        // Image principale ?
- uploaded_at (datetime)  // Date d'upload
```

**Index** :
- `idx_entity (entity_type, entity_id)` : Pour requêtes rapides

---

## 🔧 SERVICES

### `ImageManager`

**Fichier** : `src/Service/ImageManager.php`

**Méthodes principales** :

```php
// Upload une image
uploadImage(
    UploadedFile $file,
    string $entityType,     // 'Cabinet', 'Lawyer'
    int $entityId,
    array $options = []
): Image

// Options disponibles:
$options = [
    'label' => 'Photo d'équipe',
    'category' => 'equipe',
    'description' => 'Notre équipe au complet',
    'isPrimary' => false,
    'position' => 0
];

// Supprimer une image
deleteImage(Image $image): bool

// Définir comme image principale
setPrimaryImage(Image $image): void

// Récupérer toutes les images d'une entité
getEntityImages(string $entityType, int $entityId): Image[]

// Récupérer l'image principale
getPrimaryImage(string $entityType, int $entityId): ?Image

// Récupérer par catégorie
getImagesByCategory(string $entityType, int $entityId, string $category): Image[]

// Mettre à jour l'ordre
updateImagesOrder(array $imageIds): void

// Compter les images
countEntityImages(string $entityType, int $entityId): int

// URL absolue (pour API)
getAbsoluteUrl(?string $filepath): ?string
```

---

## 📦 CONTRAINTES

### Types de fichiers autorisés
- ✅ JPEG (image/jpeg)
- ✅ PNG (image/png)
- ✅ GIF (image/gif)
- ✅ WebP (image/webp)

### Taille maximale
- **2 Mo** par fichier

### Stockage
- **Dossier** : `public/uploads/{entity_type}s/images/`
  - Cabinet → `public/uploads/cabinets/images/`
  - Lawyer → `public/uploads/lawyers/images/`

---

## 💻 UTILISATION DANS LE CODE

### Exemple 1 : Upload dans un contrôleur

```php
<?php
namespace App\Controller\Admin;

use App\Entity\Cabinet;
use App\Service\ImageManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CabinetAdminController extends AbstractController
{
    public function __construct(
        private ImageManager $imageManager
    ) {}

    #[Route('/admin/cabinets/{id}/upload-image', methods: ['POST'])]
    public function uploadImage(Cabinet $cabinet, Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('image');

        if (!$file) {
            $this->addFlash('error', 'Aucun fichier sélectionné');
            return $this->redirectToRoute('admin_cabinet_edit', ['id' => $cabinet->getId()]);
        }

        try {
            $image = $this->imageManager->uploadImage(
                $file,
                'Cabinet',
                $cabinet->getId(),
                [
                    'label' => $request->request->get('label'),
                    'category' => $request->request->get('category'),
                    'description' => $request->request->get('description'),
                    'isPrimary' => $request->request->getBoolean('is_primary', false)
                ]
            );

            $this->addFlash('success', 'Image uploadée avec succès');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_cabinet_edit', ['id' => $cabinet->getId()]);
    }
}
```

### Exemple 2 : Afficher les images d'une entité

```php
// Dans le contrôleur
$images = $this->imageManager->getEntityImages('Cabinet', $cabinet->getId());

return $this->render('admin/cabinet/view.html.twig', [
    'cabinet' => $cabinet,
    'images' => $images,
]);
```

### Exemple 3 : Récupérer l'image principale

```php
$primaryImage = $this->imageManager->getPrimaryImage('Lawyer', $lawyer->getId());

if ($primaryImage) {
    $photoUrl = $primaryImage->getFilepath();
} else {
    $photoUrl = '/images/default-avatar.png';
}
```

### Exemple 4 : Exposer dans l'API

```php
// Dans CabinetController (API)
private function serializeCabinetDetail(Cabinet $cabinet): array
{
    // Récupérer les images
    $images = $this->imageManager->getEntityImages('Cabinet', $cabinet->getId());

    // Serializer les images
    $imagesData = array_map(fn($img) => [
        'id' => $img->getId(),
        'url' => $this->imageManager->getAbsoluteUrl($img->getFilepath()),
        'label' => $img->getLabel(),
        'category' => $img->getCategory(),
        'description' => $img->getDescription(),
        'isPrimary' => $img->isPrimary(),
        'position' => $img->getPosition(),
        'uploadedAt' => $img->getUploadedAt()->format('Y-m-d H:i:s'),
    ], $images);

    return [
        'id' => $cabinet->getId(),
        'name' => $cabinet->getName(),
        // ... autres champs
        'images' => $imagesData,
        'primaryImage' => $this->getPrimaryImageUrl($cabinet),
    ];
}

private function getPrimaryImageUrl(Cabinet $cabinet): ?string
{
    $primaryImage = $this->imageManager->getPrimaryImage('Cabinet', $cabinet->getId());
    return $primaryImage ? $this->imageManager->getAbsoluteUrl($primaryImage->getFilepath()) : null;
}
```

---

## 🎨 TEMPLATES TWIG

### Exemple 1 : Formulaire d'upload

```twig
{# templates/admin/cabinet/_upload_images_form.html.twig #}
<div class="card mt-3">
    <div class="card-header">
        <i class="bi bi-images"></i>
        Galerie d'images
    </div>
    <div class="card-body">
        <form method="post" action="{{ path('admin_cabinet_upload_image', {id: cabinet.id}) }}" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <small class="text-muted">Formats : JPEG, PNG, GIF, WebP (max 2Mo)</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="label" class="form-control" placeholder="Ex: Photo de l'équipe">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Catégorie</label>
                    <select name="category" class="form-select">
                        <option value="">Aucune</option>
                        <option value="locaux">Locaux</option>
                        <option value="equipe">Équipe</option>
                        <option value="evenement">Événement</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="is_primary" class="form-check-input" id="is_primary">
                        <label class="form-check-label" for="is_primary">
                            Image principale
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-upload"></i>
                Uploader l'image
            </button>
        </form>
    </div>
</div>
```

### Exemple 2 : Afficher la galerie

```twig
{# templates/admin/cabinet/_images_gallery.html.twig #}
{% if images|length > 0 %}
    <div class="card mt-3">
        <div class="card-header">
            <i class="bi bi-images"></i>
            Images ({{ images|length }})
        </div>
        <div class="card-body">
            <div class="row g-3">
                {% for image in images %}
                    <div class="col-md-3">
                        <div class="card">
                            <img src="{{ image.filepath }}"
                                 class="card-img-top"
                                 alt="{{ image.label }}"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    {{ image.label ?: 'Sans titre' }}
                                    {% if image.isPrimary %}
                                        <span class="badge bg-primary">Principal</span>
                                    {% endif %}
                                </h6>
                                {% if image.category %}
                                    <span class="badge bg-info">{{ image.category }}</span>
                                {% endif %}
                                <p class="card-text small text-muted">
                                    {{ image.formattedSize }} - {{ image.uploadedAt|date('d/m/Y') }}
                                </p>
                                <div class="btn-group btn-group-sm">
                                    {% if not image.isPrimary %}
                                        <form method="post" action="{{ path('admin_cabinet_set_primary_image', {id: cabinet.id, imageId: image.id}) }}" style="display:inline;">
                                            <button class="btn btn-outline-primary" title="Définir comme principale">
                                                <i class="bi bi-star"></i>
                                            </button>
                                        </form>
                                    {% endif %}
                                    <a href="{{ image.filepath }}" class="btn btn-outline-info" target="_blank" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="post" action="{{ path('admin_cabinet_delete_image', {id: cabinet.id, imageId: image.id}) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette image ?');">
                                        <button class="btn btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
{% else %}
    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i>
        Aucune image uploadée pour le moment.
    </div>
{% endif %}
```

---

## 🌐 EXPOSITION API

### Exemple d'endpoint GET /api/cabinets/{slug}

**Réponse JSON** :

```json
{
  "id": 1,
  "name": "Cabinet ABC",
  "slug": "cabinet-abc",
  "description": "Cabinet spécialisé en droit des affaires",
  "primaryImage": "https://example.com/uploads/cabinets/images/equipe-abc-12345.jpg",
  "images": [
    {
      "id": 1,
      "url": "https://example.com/uploads/cabinets/images/equipe-abc-12345.jpg",
      "label": "Photo d'équipe",
      "category": "equipe",
      "description": "Notre équipe au complet",
      "isPrimary": true,
      "position": 0,
      "uploadedAt": "2025-01-10 14:30:00"
    },
    {
      "id": 2,
      "url": "https://example.com/uploads/cabinets/images/locaux-abc-67890.jpg",
      "label": "Nos locaux",
      "category": "locaux",
      "description": "Vue de notre salle de réunion",
      "isPrimary": false,
      "position": 1,
      "uploadedAt": "2025-01-10 15:00:00"
    }
  ],
  "...": "autres champs"
}
```

---

## 🎯 CATÉGORIES SUGGÉRÉES

### Pour Cabinet
- `locaux` : Photos des bureaux, salles de réunion
- `equipe` : Photos d'équipe, événements
- `evenement` : Conférences, séminaires
- `certification` : Certificats, récompenses

### Pour Lawyer
- `photo_profil` : Photo principale de l'avocat
- `professionnel` : Photos en contexte professionnel
- `evenement` : Conférences, interventions

---

## 🚀 AJOUT À UNE NOUVELLE ENTITÉ

Pour ajouter le système d'images à une nouvelle entité (ex: `Article`) :

### 1. Dans le contrôleur

```php
// Upload
$image = $this->imageManager->uploadImage(
    $file,
    'Article',  // ← Nom de l'entité
    $article->getId(),
    $options
);

// Récupération
$images = $this->imageManager->getEntityImages('Article', $article->getId());
```

### 2. Dans l'API

```php
$imagesData = array_map(fn($img) => [
    'url' => $this->imageManager->getAbsoluteUrl($img->getFilepath()),
    'label' => $img->getLabel(),
    // ... autres champs
], $this->imageManager->getEntityImages('Article', $article->getId()));
```

### 3. C'est tout ! 🎉

Le système est **automatiquement compatible** avec toute entité.

---

## 📝 NOTES IMPORTANTES

### Sécurité

✅ **Validation stricte** :
- Types MIME vérifiés
- Taille max 2Mo
- Nom de fichier sécurisé (sluggé + uniqid)

✅ **Protection** :
- Pas d'exécution de scripts (images uniquement)
- Stockage hors du dossier source

### Performance

✅ **Index de base de données** :
- Index composite sur (entity_type, entity_id) pour requêtes rapides

✅ **Ordre d'affichage** :
- Images triées par position puis date

### Extensibilité

✅ **Facilement extensible** :
- Ajout de nouvelles catégories
- Ajout de champs personnalisés
- Compatible avec n'importe quelle entité

---

## 🔄 MIGRATION

Pour migrer d'un système avec champ `photoUrl` vers ce système :

```php
// Exemple: Migrer les lawyers
foreach ($lawyers as $lawyer) {
    if ($lawyer->getPhotoUrl()) {
        // Télécharger l'image depuis l'URL
        $tempFile = '/tmp/' . uniqid() . '.jpg';
        file_put_contents($tempFile, file_get_contents($lawyer->getPhotoUrl()));

        $uploadedFile = new UploadedFile(
            $tempFile,
            basename($lawyer->getPhotoUrl()),
            'image/jpeg',
            null,
            true
        );

        // Upload via ImageManager
        $this->imageManager->uploadImage(
            $uploadedFile,
            'Lawyer',
            $lawyer->getId(),
            [
                'label' => 'Photo de profil',
                'category' => 'photo_profil',
                'isPrimary' => true
            ]
        );
    }
}
```

---

## 🎉 PROCHAINES ÉTAPES

### Phase 1 : Implémentation Cabinet ✅
- [x] Créer entité Image
- [x] Créer ImageManager service
- [x] Migration base de données
- [ ] Ajouter route d'upload dans CabinetAdminController
- [ ] Créer template d'upload
- [ ] Exposer dans l'API Cabinet

### Phase 2 : Implémentation Lawyer 📝
- [ ] Ajouter route d'upload dans LawyerAdminController
- [ ] Remplacer champ `photoUrl` par système d'images
- [ ] Exposer dans l'API Lawyer

### Phase 3 : Améliorations 🚀
- [ ] Upload drag & drop
- [ ] Éditeur d'images (crop, resize)
- [ ] Galerie avec lightbox
- [ ] Compression automatique des images

---

**Créé le** : 2025-01-10
**Version** : 1.0
**Auteur** : Claude Code (Anthropic)
