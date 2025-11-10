# ANALYSE PROJET CNCJCI - Application Symfony

## 📋 TABLE DES MATIÈRES

1. [Contexte du Projet](#contexte-du-projet)
2. [Architecture et Technologies](#architecture-et-technologies)
3. [État Actuel du Projet](#état-actuel-du-projet)
4. [Points Fonctionnels Validés](#points-fonctionnels-validés)
5. [Problèmes Identifiés](#problèmes-identifiés)
6. [Plan d'Implémentation](#plan-dimplémentation)
   - [Phase 1 : Cabinet](#phase-1--cabinet)
   - [Phase 2 : Lawyer](#phase-2--lawyer)

---

## 🎯 CONTEXTE DU PROJET

### Vue d'ensemble
Application Symfony 6.4 avec deux volets principaux :
- **API publique** : Exposition des données pour un frontend (liste des cabinets juridiques et avocats)
- **Back-office** : Gestion des données par différents acteurs avec des droits spécifiques

### Objectifs métier
1. **Afficher** la liste des personnels juridiques (lawyers) et leurs cabinets associés
2. **Afficher** la liste des cabinets avec leur responsable et l'ensemble des avocats
3. **Gérer** ces données via un back-office sécurisé selon 3 profils utilisateurs

### Acteurs du système

#### 1. SUPER_ADMIN
- Créer, modifier, consulter les cabinets juridiques
- Rattacher des lawyers aux cabinets
- Désigner le responsable d'un cabinet
- Créer, modifier, consulter les lawyers
- Rattacher les lawyers à un cabinet (lors de la création ou modification)

#### 2. RESPO_CABINET (Responsable de Cabinet)
- Créer, modifier, consulter les lawyers
- Rattacher les lawyers **uniquement à son propre cabinet**
- Ne peut pas gérer d'autres cabinets

#### 3. LAWYER (Avocat)
- Modifier **uniquement son profil personnel** (informations non sensibles)
- Ne peut pas modifier son cabinet de rattachement
- Ne peut pas modifier son numéro au barreau

---

## 🏗️ ARCHITECTURE ET TECHNOLOGIES

### Stack technique
- **Framework** : Symfony 6.4
- **PHP** : ≥ 8.1
- **ORM** : Doctrine ORM 3.5
- **Base de données** : Configuration via Doctrine DBAL 3
- **Templating** : Twig 2/3
- **Sécurité** : Symfony Security Bundle
- **CORS** : Nelmio CORS Bundle
- **Frontend** : Bootstrap 5, Select2, Leaflet (OpenStreetMap)

### Structure du projet
```
src/
├── Controller/
│   ├── Api/                      # Contrôleurs API publics
│   │   ├── CabinetController.php
│   │   ├── LawyerController.php
│   │   └── SpecialtyController.php
│   └── Admin/                    # Contrôleurs Back-office
│       ├── CabinetAdminController.php
│       ├── LawyerAdminController.php
│       ├── DashboardController.php
│       └── ...
├── Entity/                       # Entités Doctrine
│   ├── Cabinet.php
│   ├── Lawyer.php
│   ├── User.php
│   ├── Address.php
│   ├── Phone.php
│   ├── EmailAddress.php
│   ├── CabinetType.php
│   └── Specialty.php
├── Form/                         # Formulaires Symfony
│   ├── CabinetType.php
│   ├── LawyerType.php
│   ├── AddressType.php
│   ├── PhoneType.php
│   └── EmailAddressType.php
├── Security/
│   └── Voter/                    # Voters pour les permissions
│       ├── CabinetVoter.php
│       └── LawyerVoter.php
├── Service/                      # Services métier
│   ├── UserCreationService.php
│   └── FileUploadService.php
└── EventSubscriber/              # Event Subscribers
    ├── MustChangePasswordSubscriber.php
    └── AddressValidationSubscriber.php

templates/
└── admin/                        # Templates Twig back-office
    ├── base.html.twig
    ├── dashboard.html.twig
    ├── _osm_address_map.html.twig
    ├── cabinet/
    │   ├── index.html.twig
    │   ├── form.html.twig
    │   └── lawyers.html.twig
    └── lawyer/
        ├── index.html.twig
        ├── form.html.twig
        └── profile.html.twig
```

---

## ✅ ÉTAT ACTUEL DU PROJET

### Modèle de données

#### Entité Cabinet
**Champs principaux :**
- `id`, `name`, `slug`, `website`, `description`, `logoUrl`, `isActive`
- `typeEntity` (ManyToOne → CabinetType) : Type de cabinet (Cabinet, SCP, SCPA)
- `managingPartner` (ManyToOne → Lawyer) : Responsable du cabinet
- `address` (ManyToOne → Address) : Adresse complète
- `phones` (OneToMany → Phone) : Collection de téléphones
- `emails` (OneToMany → EmailAddress) : Collection d'emails
- `lawyers` (OneToMany → Lawyer) : Avocats rattachés

**Champs dépréciés (compatibilité) :**
- `type`, `email`, `phone`, `oldAddress`, `city`, `lat`, `lng`

#### Entité Lawyer
**Champs principaux :**
- `id`, `firstName`, `lastName`, `slug`, `barNumber`, `biography`, `photoUrl`
- `cabinet` (ManyToOne → Cabinet) : Cabinet de rattachement
- `address` (ManyToOne → Address) : Adresse complète
- `phones` (OneToMany → Phone) : Collection de téléphones
- `emails` (OneToMany → EmailAddress) : Collection d'emails
- `specialties` (ManyToMany → Specialty) : Spécialités juridiques

**Champs dépréciés (compatibilité) :**
- `email`, `phone`, `city`

#### Entité User
**Champs :**
- `id`, `email` (login), `password`, `roles[]`, `firstName`, `lastName`
- `isActive` : Statut du compte
- `mustChangePassword` : Force le changement de mot de passe à la première connexion
- `lawyer` (OneToOne → Lawyer) : Profil lawyer associé (si c'est un lawyer)
- `cabinet` (ManyToOne → Cabinet) : Cabinet associé (si RESPO_CABINET)

#### Hiérarchie des rôles (security.yaml)
```yaml
role_hierarchy:
  ROLE_LAWYER: ROLE_USER
  ROLE_RESPO_CABINET: [ROLE_USER, ROLE_LAWYER]
  ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_LAWYER, ROLE_RESPO_CABINET, ROLE_ADMIN]
```

### Sécurité et Contrôle d'accès

#### Access Control (security.yaml)
```yaml
access_control:
  - { path: ^/api, roles: PUBLIC_ACCESS }           # API publique
  - { path: ^/login, roles: PUBLIC_ACCESS }         # Page de login
  - { path: ^/admin/lawyers/me, roles: ROLE_LAWYER }
  - { path: ^/admin/lawyers, roles: ROLE_RESPO_CABINET }
  - { path: ^/admin/cabinets, roles: ROLE_SUPER_ADMIN }
  - { path: ^/admin, roles: ROLE_USER }
```

#### Voters implémentés

**CabinetVoter** (src/Security/Voter/CabinetVoter.php:9)
- `CABINET_VIEW` : Tous les utilisateurs connectés
- `CABINET_EDIT` : SUPER_ADMIN ou RESPO_CABINET (son propre cabinet uniquement)
- `CABINET_DELETE` : SUPER_ADMIN uniquement
- `CABINET_MANAGE_LAWYERS` : SUPER_ADMIN ou RESPO_CABINET (son propre cabinet)

**LawyerVoter** (src/Security/Voter/LawyerVoter.php:9)
- `LAWYER_VIEW` : Tous les utilisateurs connectés
- `LAWYER_EDIT` : SUPER_ADMIN, RESPO_CABINET (lawyers de son cabinet), ou LAWYER (son propre profil)
- `LAWYER_DELETE` : SUPER_ADMIN ou RESPO_CABINET (lawyers de son cabinet)

### Fonctionnalités implémentées

#### 1. API Publique ✅
**Endpoints Cabinet** (src/Controller/Api/CabinetController.php:17)
- `GET /api/cabinets` : Liste paginée avec filtres (name, type, city)
- `GET /api/cabinets/{slug}` : Détails d'un cabinet

**Endpoints Lawyer** (src/Controller/Api/LawyerController.php:13)
- `GET /api/lawyers` : Liste paginée avec filtres (name, cabinet, city, specialty)
- `GET /api/lawyers/{slug}` : Détails d'un avocat

**Sérialiseurs :**
- Gestion intelligente des champs dépréciés (fallback vers anciens champs)
- Exposition des relations (cabinet ↔ lawyers, managingPartner)
- Gestion des URLs absolues pour les logos/photos

#### 2. Back-office Cabinet ✅

**Routes implémentées** (src/Controller/Admin/CabinetAdminController.php:19-21)
- `GET /admin/cabinets` : Liste des cabinets (SUPER_ADMIN)
- `GET /admin/cabinets/new` : Formulaire de création (SUPER_ADMIN)
- `POST /admin/cabinets/new` : Création d'un cabinet (SUPER_ADMIN)
- `GET /admin/cabinets/{id}/edit` : Formulaire de modification (SUPER_ADMIN)
- `POST /admin/cabinets/{id}/edit` : Modification d'un cabinet (SUPER_ADMIN)
- `GET /admin/cabinets/{id}/lawyers` : Gestion des avocats du cabinet (SUPER_ADMIN)
- `POST /admin/cabinets/{id}/lawyers` : Actions sur les avocats (attach, detach, designate)
- `POST /admin/cabinets/{id}/toggle` : Activer/Désactiver un cabinet (SUPER_ADMIN)

**Fonctionnalités :**
- Création/modification avec formulaire complet
- Collections dynamiques pour phones et emails (JavaScript)
- Upload de logo avec gestion des contraintes
- Désignation du responsable de cabinet
- Rattachement d'avocats (à la création ou via page dédiée)
- Auto-promotion en RESPO_CABINET lors de la désignation

#### 3. Back-office Lawyer ✅

**Routes implémentées** (src/Controller/Admin/LawyerAdminController.php:16-17)
- `GET /admin/lawyers` : Liste des lawyers (RESPO_CABINET filtre par son cabinet)
- `GET /admin/lawyers/new` : Formulaire de création
- `POST /admin/lawyers/new` : Création d'un lawyer + compte User automatique
- `GET /admin/lawyers/{id}/edit` : Formulaire de modification
- `POST /admin/lawyers/{id}/edit` : Modification d'un lawyer
- `GET /admin/lawyers/me` : Profil personnel (ROLE_LAWYER)
- `POST /admin/lawyers/me` : Modification du profil personnel (ROLE_LAWYER)

**Fonctionnalités :**
- Création avec rattachement automatique au cabinet (pour RESPO_CABINET)
- Modification limitée pour LAWYER (pas de cabinet, pas de barNumber)
- Collections dynamiques pour phones, emails, specialties

#### 4. Gestion des Utilisateurs ✅

**UserCreationService** (src/Service/UserCreationService.php:9)
- `createUserForLawyer(Lawyer $lawyer)` : Crée automatiquement un compte User lors de la création d'un Lawyer
  - Email : Email principal du lawyer
  - Login : Email du lawyer
  - Mot de passe par défaut : `ChangeMe2024!`
  - Rôle : `ROLE_LAWYER`
  - Flag : `mustChangePassword = true` (force le changement à la première connexion)
  - Rattachement au cabinet si existant

- `promoteToRespoCabinet(User $user)` : Promotion d'un User en RESPO_CABINET

**MustChangePasswordSubscriber** (src/EventSubscriber/MustChangePasswordSubscriber.php:12)
- Intercepte toutes les requêtes
- Redirige vers `/change-password` si `mustChangePassword = true`
- Routes autorisées : `app_change_password`, `app_logout`, profiler

#### 5. Localisation OpenStreetMap ✅

**Template réutilisable** (templates/admin/_osm_address_map.html.twig:1)
- Carte Leaflet interactive
- Plugin Geocoder avec barre de recherche intégrée
- Marqueur draggable pour ajuster la position
- Géocodage inverse (clic → adresse automatique)
- Recherche d'adresse avec autocomplétion
- Filtrage par pays (Côte d'Ivoire)
- Mise à jour automatique des champs lat/lng

**Intégration :**
- Formulaire Cabinet : templates/admin/cabinet/form.html.twig:208
- Formulaire Lawyer : templates/admin/lawyer/form.html.twig:151

#### 6. Upload de fichiers ✅

**FileUploadService** (src/Service/FileUploadService.php:8)
- `upload(UploadedFile $file, string $subfolder)` : Upload avec slug du nom
- `delete(string $fileUrl)` : Suppression de fichier
- `getDefaultCabinetLogo()` : Logo par défaut
- `getAbsoluteUrl(?string $url)` : Conversion URL relative → absolue (pour API)

**Configuration** (config/services.yaml:32-35)
```yaml
App\Service\FileUploadService:
  arguments:
    $uploadsDirectory: '%kernel.project_dir%/public/uploads'
    $baseUrl: '%env(default::APP_BASE_URL)%'
```

**Contraintes de validation** (src/Form/CabinetType.php:80-91)
- Formats acceptés : JPEG, PNG, GIF, WebP
- Taille max : 2 Mo
- Stockage : `public/uploads/cabinets/`

#### 7. Route par défaut ✅

**Configuration** (config/routes.yaml:2-7)
```yaml
root:
  path: /
  controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController
  defaults:
    route: 'admin_dashboard'
    permanent: false
```
- La racine `/` redirige vers le dashboard du back-office

---

## 🔴 PROBLÈMES IDENTIFIÉS

### ❌ CRITIQUE : OpenStreetMap - Recherche non fonctionnelle dans formulaire Lawyer

**Localisation** : templates/admin/lawyer/form.html.twig:219-317

**Problème :**
Le template `lawyer/form.html.twig` **ne réutilise PAS le template `_osm_address_map.html.twig`** qui contient le plugin Geocoder fonctionnel. À la place, il implémente une carte Leaflet basique avec :
- Un système de recherche "fait maison" incomplet (lignes 297-314)
- Pas de barre de recherche visible dans l'interface
- Pas d'intégration du plugin Leaflet Control Geocoder
- Console.log() au lieu d'une UI pour afficher les résultats

**Conséquences :**
- L'utilisateur ne peut pas rechercher une adresse lors de la création/modification d'un lawyer
- Doit cliquer manuellement sur la carte pour placer le marqueur
- Expérience utilisateur incohérente entre Cabinet et Lawyer

**Solution attendue :**
Utiliser le même include que dans `cabinet/form.html.twig` :
```twig
{% include 'admin/_osm_address_map.html.twig' with {'map_id': 'lawyer-address-map'} %}
```

---

### ❌ MOYEN : Upload de pièces jointes pour Cabinet non fonctionnel

**Contexte :**
L'utilisateur mentionne : "Les pièces jointes implémentées au niveau de cabinet ne fonctionnent pas."

**Analyse du code actuel :**
- Le formulaire `CabinetType` (src/Form/CabinetType.php:72-93) gère uniquement le champ `logoFile`
- **Aucun champ pour "pièces jointes" (documents joints)** n'est présent dans :
  - Le formulaire `CabinetType.php`
  - L'entité `Cabinet.php`
  - Le template `cabinet/form.html.twig`

**Besoin potentiel identifié :**
Il semble manquer une fonctionnalité pour permettre d'uploader plusieurs documents/fichiers joints au cabinet (ex: documents juridiques, certifications, etc.).

**État :** Fonctionnalité non implémentée

**Action requise :**
1. Clarifier avec l'utilisateur :
   - Quel type de pièces jointes ? (PDF, Word, images, etc.)
   - Combien de fichiers par cabinet ?
   - À quoi servent ces documents ? (affichage public API, back-office uniquement ?)
2. Ajouter une nouvelle entité `CabinetDocument` (ou collection de fichiers)
3. Implémenter l'upload multiple
4. Ajouter les champs dans le formulaire

---

### ⚠️ MINEUR : Formulaires Twig - Expérience utilisateur améliorable

#### 1. Cabinet Form - Gestion des avocats à la modification

**Contexte** (src/Form/CabinetType.php:96-136)
```php
// Responsable et avocats - seulement à la création (pas en modification)
$isCreation = !($cabinet && $cabinet->getId());

if ($isCreation) {
    // Champs managingPartner et lawyers disponibles
}
```

**Problème :**
- En **création** : On peut désigner un responsable et rattacher des avocats
- En **modification** : Ces champs disparaissent, l'utilisateur doit passer par `/admin/cabinets/{id}/lawyers`

**Impact :** Navigation supplémentaire, pas intuitif

**Solution possible :**
- Option A : Afficher ces champs même en modification
- Option B : Ajouter un bouton "Gérer les avocats" bien visible dans le formulaire d'édition
- Option C : Afficher un résumé des avocats rattachés + lien vers la page de gestion

#### 2. Formulaire Lawyer - Positionnement de la carte

**Localisation** : templates/admin/lawyer/form.html.twig:151

**Problème :**
```twig
<div class="card-body">
    {{ form_row(form.address) }}
    <div id="address-map"></div>
</div>
```
La carte est rendue directement dans le `<div id="address-map"></div>` mais :
- Pas de styles définis pour la hauteur (contrairement au template `_osm_address_map.html.twig`)
- Possible problème d'affichage si la hauteur n'est pas définie

**Solution :** Appliquer les mêmes styles que dans `_osm_address_map.html.twig`

#### 3. Collections phones/emails - Validation côté client

**Contexte** : templates/admin/cabinet/form.html.twig:422
```javascript
if (itemCount <= 1) {
    alert('Vous devez conserver au moins un élément');
    return;
}
```

**Problème :**
- Validation côté client avec `alert()` (UX datée)
- Pas de validation Symfony côté serveur pour garantir au moins 1 phone et 1 email

**Solutions :**
- Remplacer `alert()` par Bootstrap Toasts ou messages d'erreur inline
- Ajouter une contrainte Symfony `Count` dans les entités

---

### ⚠️ MINEUR : Gestion du mot de passe par défaut

**Contexte** : src/Service/UserCreationService.php:54
```php
$defaultPassword = 'ChangeMe2024!';
```

**Problème :**
Le mot de passe par défaut est **hardcodé** dans le service. Cela pose plusieurs questions :
1. Tous les utilisateurs ont le même mot de passe temporaire → risque de sécurité si quelqu'un le devine
2. Pas de notification par email (le créateur doit communiquer manuellement le mot de passe)

**Recommandations :**
1. **Option A (recommandée)** : Générer un mot de passe aléatoire unique par utilisateur
   - Exemple : `bin2hex(random_bytes(8))` → `3f7a9b2c5e8d1f4a`
   - Afficher le mot de passe dans un flash message après création
   - L'admin le communique à l'utilisateur

2. **Option B** : Système d'invitation par email
   - Générer un token d'activation unique
   - Envoyer un email avec un lien pour définir son mot de passe
   - Plus sécurisé mais nécessite configuration email

---

### ✅ POINTS VALIDÉS (Fonctionnent correctement)

#### ✅ Création automatique de compte User
**Workflow validé** (src/Controller/Admin/LawyerAdminController.php:92-99) :
1. Admin crée un Lawyer dans le back-office
2. `UserCreationService::createUserForLawyer()` est appelé automatiquement
3. Un User est créé avec :
   - Email = email principal du Lawyer
   - Password = `ChangeMe2024!` (hashé)
   - Role = `ROLE_LAWYER`
   - Flag `mustChangePassword = true`
4. Flash message informant l'admin du mot de passe par défaut

#### ✅ Désignation du responsable de cabinet
**Workflow validé** (src/Controller/Admin/CabinetAdminController.php:249-282) :
1. **À la création** (src/Controller/Admin/CabinetAdminController.php:96-109) :
   - Le SUPER_ADMIN sélectionne un Lawyer dans le champ `managingPartner`
   - Le Lawyer est rattaché automatiquement au Cabinet
   - Son compte User est promu en `ROLE_RESPO_CABINET`

2. **En modification via `/admin/cabinets/{id}/lawyers`** (action `designate`) :
   - Le SUPER_ADMIN clique sur "Désigner comme responsable"
   - L'ancien responsable est rétrogradé (perte du rôle `ROLE_RESPO_CABINET`)
   - Le nouveau responsable est promu

#### ✅ Rattachement d'un Lawyer à un Cabinet
**Workflow validé** :
1. **À la création du Lawyer** (src/Controller/Admin/LawyerAdminController.php:72-78) :
   - SUPER_ADMIN : Peut choisir n'importe quel cabinet dans le formulaire
   - RESPO_CABINET : Le champ `cabinet` est pré-rempli et désactivé (son propre cabinet)

2. **À la modification du Lawyer** (src/Form/LawyerType.php:64-69) :
   - SUPER_ADMIN : Peut changer le cabinet
   - RESPO_CABINET : Le champ `cabinet` est désactivé (ne peut pas transférer à un autre cabinet)
   - LAWYER : Le champ `cabinet` n'est pas affiché (self_edit mode)

3. **Via la page de gestion des avocats du cabinet** (src/Controller/Admin/CabinetAdminController.php:232-236) :
   - Action `attach` : Rattache un avocat existant au cabinet
   - Action `detach` : Détache un avocat du cabinet (sauf s'il est responsable)

#### ✅ OpenStreetMap dans formulaire Cabinet
**Fonctionnement validé** (templates/admin/_osm_address_map.html.twig:24) :
- Carte Leaflet avec plugin Geocoder
- Barre de recherche fonctionnelle
- Géocodage inverse au clic
- Marqueur draggable
- Mise à jour automatique des champs lat/lng/city/line1

---

## 📅 PLAN D'IMPLÉMENTATION

### 🔹 PHASE 1 : CABINET

#### Tâche 1.1 : Clarifier les "pièces jointes"
**Priorité** : HAUTE
**Estimation** : 30 min (discussion) + développement selon besoin

**Actions :**
1. Interroger l'utilisateur :
   - Type de documents attendus (PDF, images, etc.) ?
   - Usage : API publique ou back-office uniquement ?
   - Nombre de fichiers par cabinet ?
2. Selon la réponse :
   - Si fonctionnalité non nécessaire : Documenter et clore
   - Si nécessaire : Passer aux tâches 1.2 à 1.5

#### Tâche 1.2 : Créer l'entité CabinetDocument
**Priorité** : MOYENNE (dépend de 1.1)
**Estimation** : 1h

**Implémentation :**
```php
// src/Entity/CabinetDocument.php
#[ORM\Entity]
class CabinetDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $filename;

    #[ORM\Column(length: 255)]
    private string $fileUrl;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $label = null; // "Certification", "Document légal", etc.

    #[ORM\Column(length: 50)]
    private string $mimeType;

    #[ORM\Column]
    private int $fileSize;

    #[ORM\Column]
    private \DateTimeImmutable $uploadedAt;

    #[ORM\ManyToOne(targetEntity: Cabinet::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Cabinet $cabinet;

    // Getters/Setters...
}
```

**Mise à jour de Cabinet.php :**
```php
#[ORM\OneToMany(targetEntity: CabinetDocument::class, mappedBy: 'cabinet', cascade: ['persist', 'remove'])]
private Collection $documents;
```

**Migration :**
```bash
docker-compose exec php php bin/console make:migration
docker-compose exec php php bin/console doctrine:migrations:migrate
```

#### Tâche 1.3 : Ajouter le champ documents au formulaire
**Priorité** : MOYENNE
**Estimation** : 1h30

**Fichiers à modifier :**
- `src/Form/CabinetDocumentType.php` (nouveau)
- `src/Form/CabinetType.php` (ajout du champ collection)
- `templates/admin/cabinet/form.html.twig` (section upload)

**Exemple :**
```php
// src/Form/CabinetType.php
$builder->add('documents', CollectionType::class, [
    'entry_type' => FileType::class,
    'allow_add' => true,
    'allow_delete' => true,
    'by_reference' => false,
    'label' => 'Documents joints (PDF, Word, images)',
]);
```

#### Tâche 1.4 : Implémenter l'upload multiple
**Priorité** : MOYENNE
**Estimation** : 2h

**Fichiers à modifier :**
- `src/Controller/Admin/CabinetAdminController.php` (gestion upload)
- `src/Service/FileUploadService.php` (méthode `uploadMultiple()`)

**Logique :**
```php
// Dans le contrôleur
foreach ($form->get('documents')->getData() as $file) {
    if ($file instanceof UploadedFile) {
        $fileUrl = $this->fileUploadService->upload($file, 'cabinets/documents');
        $document = new CabinetDocument();
        $document->setFilename($file->getClientOriginalName());
        $document->setFileUrl($fileUrl);
        $document->setMimeType($file->getMimeType());
        $document->setFileSize($file->getSize());
        $document->setCabinet($cabinet);
        $document->setUploadedAt(new \DateTimeImmutable());
        $this->em->persist($document);
    }
}
```

#### Tâche 1.5 : Exposer les documents dans l'API (si nécessaire)
**Priorité** : BASSE
**Estimation** : 30 min

**Fichier** : `src/Controller/Api/CabinetController.php`

```php
// Dans serializeCabinetDetail()
'documents' => array_map(fn($doc) => [
    'id' => $doc->getId(),
    'label' => $doc->getLabel(),
    'filename' => $doc->getFilename(),
    'url' => $this->fileUploadService->getAbsoluteUrl($doc->getFileUrl()),
    'mimeType' => $doc->getMimeType(),
    'fileSize' => $doc->getFileSize(),
    'uploadedAt' => $doc->getUploadedAt()->format('Y-m-d H:i:s'),
], $cabinet->getDocuments()->toArray()),
```

#### Tâche 1.6 : Améliorer l'UX du formulaire Cabinet (modifications)
**Priorité** : BASSE
**Estimation** : 1h

**Objectif :** Faciliter la gestion des avocats lors de la modification

**Option retenue :** Afficher un encadré récapitulatif avec lien

**Fichier** : `templates/admin/cabinet/form.html.twig`

**Implémentation :**
```twig
{% if cabinet.id %}
    <div class="card mb-3">
        <div class="card-header">
            <i class="bi bi-people"></i>
            Avocats rattachés
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>{{ cabinet.lawyers|length }}</strong> avocat(s) rattaché(s)
            </p>
            {% if cabinet.managingPartner %}
                <p class="mb-3">
                    <i class="bi bi-person-badge text-primary"></i>
                    Responsable : <strong>{{ cabinet.managingPartner.fullName }}</strong>
                </p>
            {% endif %}
            <a href="{{ path('admin_cabinet_lawyers', {'id': cabinet.id}) }}"
               class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square"></i>
                Gérer les avocats et le responsable
            </a>
        </div>
    </div>
{% endif %}
```

---

### 🔹 PHASE 2 : LAWYER

#### Tâche 2.1 : Corriger l'implémentation OpenStreetMap
**Priorité** : CRITIQUE ⚠️
**Estimation** : 15 min

**Fichier** : `templates/admin/lawyer/form.html.twig`

**Problème actuel (ligne 151) :**
```twig
<div class="card-body">
    {{ form_row(form.address) }}
    <div id="address-map"></div>  {# Carte custom sans recherche #}
</div>
```

**Correction :**
```twig
<div class="card-body">
    {{ form_row(form.address) }}
    {% include 'admin/_osm_address_map.html.twig' with {'map_id': 'lawyer-address-map'} %}
</div>
```

**Suppression :** Retirer le code JavaScript custom (lignes 219-317)

**Test :**
1. Accéder à `/admin/lawyers/new`
2. Vérifier la présence de la barre de recherche Geocoder
3. Tester la recherche d'une adresse à Abidjan
4. Vérifier le drag du marqueur
5. Valider que les champs lat/lng/line1/city sont bien remplis

#### Tâche 2.2 : Ajouter les styles manquants pour la carte
**Priorité** : MOYENNE
**Estimation** : 5 min

**Fichier** : `templates/admin/lawyer/form.html.twig`

**Ajouter dans le block stylesheets :**
```twig
{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" />
{% endblock %}
```

**Note :** Ces styles sont déjà inclus dans `_osm_address_map.html.twig` mais il est bon de les déclarer explicitement.

#### Tâche 2.3 : Améliorer la validation des collections
**Priorité** : BASSE
**Estimation** : 1h

**Objectif :** Garantir qu'un Lawyer ou Cabinet a au moins 1 email et 1 téléphone

**Implémentation :**

**A. Contraintes Symfony :**
```php
// src/Entity/Cabinet.php
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\OneToMany(targetEntity: Phone::class, mappedBy: 'cabinet', cascade: ['persist', 'remove'])]
#[Assert\Count(min: 1, minMessage: 'Vous devez ajouter au moins un numéro de téléphone')]
private Collection $phones;

#[ORM\OneToMany(targetEntity: EmailAddress::class, mappedBy: 'cabinet', cascade: ['persist', 'remove'])]
#[Assert\Count(min: 1, minMessage: 'Vous devez ajouter au moins une adresse email')]
private Collection $emails;
```

**B. UX côté client (remplacer alert) :**
```javascript
// templates/admin/cabinet/form.html.twig (ligne 422)
if (itemCount <= 1) {
    // Créer un toast Bootstrap
    const toastHtml = `
        <div class="toast align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Vous devez conserver au moins un élément
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    // Afficher le toast...
    return;
}
```

#### Tâche 2.4 : Améliorer la génération du mot de passe par défaut
**Priorité** : MOYENNE
**Estimation** : 30 min

**Fichier** : `src/Service/UserCreationService.php`

**Option A : Mot de passe aléatoire**
```php
public function createUserForLawyer(Lawyer $lawyer): ?User
{
    // ... code existant ...

    // Générer un mot de passe aléatoire sécurisé
    $randomPassword = $this->generateRandomPassword();
    $hashedPassword = $this->passwordHasher->hashPassword($user, $randomPassword);
    $user->setPassword($hashedPassword);

    // Forcer le changement de mot de passe à la première connexion
    $user->setMustChangePassword(true);

    $this->em->persist($user);
    $this->em->flush();

    // Retourner le mot de passe en clair (pour que le contrôleur puisse l'afficher)
    $user->plainPassword = $randomPassword; // Propriété temporaire non persistée

    return $user;
}

private function generateRandomPassword(int $length = 12): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    $max = strlen($chars) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }

    return $password;
}
```

**Mise à jour du contrôleur :**
```php
// src/Controller/Admin/LawyerAdminController.php (ligne 93)
$user = $this->userCreationService->createUserForLawyer($lawyer);

if ($user) {
    $plainPassword = $user->plainPassword ?? 'ChangeMe2024!'; // Fallback
    $this->addFlash('success', sprintf(
        'Avocat créé avec succès. Compte créé avec le mot de passe : <strong>%s</strong><br>
        <small class="text-muted">À communiquer à l\'utilisateur. Il devra le changer à la première connexion.</small>',
        $plainPassword
    ));
}
```

#### Tâche 2.5 : Améliorer l'affichage du profil Lawyer (self_edit)
**Priorité** : BASSE
**Estimation** : 30 min

**Fichier** : `templates/admin/lawyer/profile.html.twig`

**Problème actuel :** Template simple, pourrait être plus informatif

**Améliorations possibles :**
1. Afficher clairement les champs modifiables vs non modifiables
2. Ajouter un encadré "Informations du cabinet" (lecture seule)
3. Afficher le rôle de l'utilisateur
4. Message d'aide expliquant les restrictions

**Exemple :**
```twig
<div class="alert alert-info mb-3">
    <i class="bi bi-info-circle"></i>
    <strong>Note :</strong> Vous pouvez modifier votre profil, biographie, photo et coordonnées.
    Pour modifier votre numéro au barreau ou votre cabinet de rattachement, contactez votre responsable de cabinet.
</div>

{% if lawyer.cabinet %}
    <div class="card mb-3 bg-light">
        <div class="card-header">
            <i class="bi bi-building"></i>
            Mon Cabinet
        </div>
        <div class="card-body">
            <h5>{{ lawyer.cabinet.name }}</h5>
            <p class="mb-0 text-muted">
                {% if lawyer.cabinet.managingPartner.id == lawyer.id %}
                    <span class="badge bg-primary">Responsable de cabinet</span>
                {% else %}
                    Responsable : {{ lawyer.cabinet.managingPartner.fullName }}
                {% endif %}
            </p>
        </div>
    </div>
{% endif %}
```

---

## 📊 RÉCAPITULATIF DES PRIORITÉS

### 🔴 Critique (À faire en priorité)
1. **Tâche 2.1** : Corriger OpenStreetMap dans formulaire Lawyer (15 min)

### 🟠 Haute
2. **Tâche 1.1** : Clarifier les "pièces jointes" Cabinet (discussion 30 min)

### 🟡 Moyenne (selon résultats de 1.1)
3. **Tâche 1.2 à 1.4** : Implémenter les pièces jointes Cabinet (4h30 total)
4. **Tâche 2.4** : Améliorer génération mot de passe (30 min)
5. **Tâche 2.2** : Ajouter styles manquants carte (5 min)

### 🟢 Basse (Améliorations UX)
6. **Tâche 1.6** : UX formulaire Cabinet (1h)
7. **Tâche 2.3** : Validation collections (1h)
8. **Tâche 2.5** : Améliorer profil Lawyer (30 min)
9. **Tâche 1.5** : Exposer documents dans API (30 min, si nécessaire)

---

## 🚀 ORDRE D'EXÉCUTION RECOMMANDÉ

### Sprint 1 (1-2 heures)
1. ✅ **Tâche 2.1** : Corriger OpenStreetMap Lawyer (15 min)
2. ✅ **Tâche 2.2** : Ajouter styles manquants (5 min)
3. ✅ **Tâche 1.1** : Discussion pièces jointes (30 min)

### Sprint 2 (Si pièces jointes nécessaires : 5 heures)
4. ✅ **Tâche 1.2** : Créer entité CabinetDocument (1h)
5. ✅ **Tâche 1.3** : Ajouter champ documents formulaire (1h30)
6. ✅ **Tâche 1.4** : Implémenter upload multiple (2h)
7. ✅ **Tâche 1.5** : Exposer dans API (30 min, optionnel)

### Sprint 3 (Améliorations : 3-4 heures)
8. ✅ **Tâche 2.4** : Améliorer mot de passe (30 min)
9. ✅ **Tâche 1.6** : UX formulaire Cabinet (1h)
10. ✅ **Tâche 2.3** : Validation collections (1h)
11. ✅ **Tâche 2.5** : Améliorer profil Lawyer (30 min)

---

## 📝 NOTES TECHNIQUES IMPORTANTES

### Commandes Docker utiles
```bash
# Nettoyer le cache Symfony
docker-compose exec -T php php bin/console cache:clear

# Créer une migration
docker-compose exec php php bin/console make:migration

# Exécuter les migrations
docker-compose exec php php bin/console doctrine:migrations:migrate

# Créer une entité
docker-compose exec php php bin/console make:entity

# Lister les routes
docker-compose exec php php bin/console debug:router

# Vérifier les permissions
docker-compose exec php php bin/console debug:security
```

### Structure de la base de données actuelle

**Tables principales :**
- `cabinet` : Cabinets juridiques
- `lawyer` : Avocats
- `user` : Comptes utilisateurs
- `cabinet_type` : Types de cabinets (Cabinet, SCP, SCPA)
- `specialty` : Spécialités juridiques
- `address` : Adresses complètes
- `phone` : Téléphones (liaison Cabinet/Lawyer)
- `email_address` : Emails (liaison Cabinet/Lawyer)
- `lawyer_specialty` : Table de liaison Many-to-Many

### Sécurité

**Points validés :**
- ✅ Hashage des mots de passe avec `auto` algorithm (Argon2 ou bcrypt)
- ✅ CSRF activé sur les formulaires
- ✅ Voters pour contrôle d'accès granulaire
- ✅ Hiérarchie de rôles correctement configurée
- ✅ Force le changement de mot de passe à la première connexion

**Points d'attention :**
- ⚠️ Mot de passe par défaut identique pour tous → Recommandation d'utiliser un générateur aléatoire
- ⚠️ Pas de notification par email (admin doit communiquer manuellement)

### API publique

**Caractéristiques :**
- ✅ Endpoints `/api/cabinets` et `/api/lawyers` fonctionnels
- ✅ Pagination implémentée
- ✅ Filtres de recherche opérationnels
- ✅ CORS configuré (Nelmio CORS Bundle)
- ✅ Serializers gèrent les champs dépréciés (fallback)
- ✅ URLs absolues pour les images/logos

---

## 🎯 VALIDATION FINALE

### Checklist de validation Phase 1 (Cabinet)

- [ ] Les "pièces jointes" sont clarifiées avec l'utilisateur
- [ ] Si nécessaire : Entité `CabinetDocument` créée et migrée
- [ ] Si nécessaire : Upload multiple fonctionnel dans le formulaire
- [ ] Si nécessaire : Documents exposés dans l'API (si requis)
- [ ] Formulaire d'édition affiche un lien vers la gestion des avocats
- [ ] Test création Cabinet avec logo + documents
- [ ] Test modification Cabinet avec remplacement de logo
- [ ] Test désignation d'un responsable → promotion RESPO_CABINET
- [ ] Test rattachement/détachement d'avocats

### Checklist de validation Phase 2 (Lawyer)

- [ ] Template `lawyer/form.html.twig` utilise `_osm_address_map.html.twig`
- [ ] Barre de recherche Geocoder visible et fonctionnelle
- [ ] Recherche d'adresse retourne des résultats sélectionnables
- [ ] Clic sur la carte place un marqueur et remplit les champs
- [ ] Marqueur draggable met à jour les coordonnées
- [ ] Test création Lawyer → compte User créé automatiquement
- [ ] Mot de passe par défaut affiché dans le flash message
- [ ] Test première connexion → redirection vers changement de mot de passe
- [ ] Test RESPO_CABINET → ne peut créer que des lawyers de son cabinet
- [ ] Test LAWYER → peut modifier uniquement son profil (pas cabinet, pas barNumber)
- [ ] Validation collections (au moins 1 email, 1 phone) fonctionne
- [ ] Messages d'erreur UX agréables (toasts au lieu d'alert)

---

## 📞 CONTACT ET SUPPORT

Pour toute question ou clarification sur cette analyse :
- Consulter la documentation Symfony : https://symfony.com/doc/current/index.html
- Vérifier les logs : `docker-compose logs -f php`
- Consulter les routes : `docker-compose exec php php bin/console debug:router`

---

**Document généré le** : 2025-01-XX
**Version du projet** : Symfony 6.4
**Auteur de l'analyse** : Claude Code (Anthropic)
