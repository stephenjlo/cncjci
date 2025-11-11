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

## 📞 CONTACT ET SUPPORT

Pour toute question ou clarification sur cette analyse :
- Consulter la documentation Symfony : https://symfony.com/doc/current/index.html
- Vérifier les logs : `docker-compose logs -f php`
- Consulter les routes : `docker-compose exec php php bin/console debug:router`

---

**Document généré le** : 2025-01-XX
**Version du projet** : Symfony 6.4
**Auteur de l'analyse** : Claude Code (Anthropic)
