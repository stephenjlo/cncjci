# CNCJCI - Guide Docker

## 🐳 Configuration Docker

Ce projet utilise Docker Compose avec :
- **PHP** 8.2.29
- **MySQL/MariaDB** 10.11.2
- **Symfony** 6.4.26

---

## 🚀 Démarrage Rapide

### 1. Démarrer les conteneurs
```bash
docker-compose up -d
```

### 2. Corriger les permissions (si nécessaire)
```bash
./fix-permissions.sh
```

### 3. Installer les dépendances
```bash
docker-compose exec php composer install
```

### 4. Vider le cache
```bash
docker-compose exec php php bin/console cache:clear
```

### 5. Vérifier les migrations
```bash
docker-compose exec php php bin/console doctrine:migrations:status
```

### 6. Exécuter les migrations
```bash
docker-compose exec php php bin/console doctrine:migrations:migrate
```

---

## 🔧 Commandes Utiles

### Cache
```bash
# Vider le cache
docker-compose exec php php bin/console cache:clear

# Warmup du cache
docker-compose exec php php bin/console cache:warmup
```

### Base de données
```bash
# Statut des migrations
docker-compose exec php php bin/console doctrine:migrations:status

# Exécuter les migrations
docker-compose exec php php bin/console doctrine:migrations:migrate

# Valider le schéma
docker-compose exec php php bin/console doctrine:schema:validate

# Exécuter une requête SQL
docker-compose exec php php bin/console doctrine:query:sql "SELECT * FROM user"
```

### Debug
```bash
# Informations sur l'application
docker-compose exec php php bin/console about

# Lister les routes
docker-compose exec php php bin/console debug:router

# Détails d'une route
docker-compose exec php php bin/console debug:router app_change_password

# Lister les services
docker-compose exec php php bin/console debug:container

# Événements enregistrés
docker-compose exec php php bin/console debug:event kernel.request
```

### Logs
```bash
# Voir les logs PHP
docker-compose logs -f php

# Voir les logs de la base de données
docker-compose logs -f mysql

# Logs Symfony
tail -f var/log/dev.log
```

---

## ⚠️ Problèmes Courants

### Permission denied sur src/Service ou src/EventSubscriber

**Erreur** :
```
RecursiveDirectoryIterator::__construct(/var/www/html/src/Service):
Failed to open directory: Permission denied
```

**Solution** :
```bash
./fix-permissions.sh
docker-compose exec php php bin/console cache:clear
```

### Migration SQLite au lieu de MySQL

**Erreur** :
```
SQLSTATE[42000]: Syntax error... near 'AUTOINCREMENT NOT NULL'
```

**Cause** : Migrations générées avec SQLite au lieu de MySQL.

**Solution** :
1. Supprimer les migrations incorrectes
2. Regénérer avec `docker-compose exec php php bin/console make:migration`
3. Vérifier que la syntaxe utilise `AUTO_INCREMENT` (MySQL) et non `AUTOINCREMENT` (SQLite)

### Le champ mustChangePassword n'existe pas

**Vérification** :
```bash
docker-compose exec php php bin/console doctrine:query:sql "SHOW COLUMNS FROM user"
```

**Si absent**, exécuter :
```bash
docker-compose exec php php bin/console doctrine:migrations:migrate
```

**Si la migration échoue mais le champ existe** :
```bash
# Marquer manuellement la migration comme exécutée
docker-compose exec php php bin/console doctrine:migrations:version \
  DoctrineMigrations\\Version20251107003000 --add
```

---

## 📦 Structure du Projet

```
cncjci/
├── config/             # Configuration Symfony
├── migrations/         # Migrations Doctrine
├── public/             # Point d'entrée web
├── src/
│   ├── Controller/     # Contrôleurs (Admin + API)
│   ├── Entity/         # Entités Doctrine
│   ├── EventSubscriber/# Subscribers (mustChangePassword)
│   ├── Form/           # Formulaires Symfony
│   ├── Repository/     # Repositories Doctrine
│   ├── Security/       # Voters et security
│   └── Service/        # Services métier (UserCreation)
├── templates/          # Templates Twig
├── var/                # Cache, logs
├── docker-compose.yml  # Configuration Docker
├── fix-permissions.sh  # Script de correction des permissions
└── CLAUDE.md           # Documentation complète
```

---

## 🔐 Système d'Authentification

### Utilisateur par défaut
- **Email** : admin@example.com
- **Mot de passe** : (voir dans la base de données)
- **Rôle** : ROLE_SUPER_ADMIN

### Création d'un avocat
Lorsque vous créez un avocat via `/admin/lawyers/new`, un compte User est automatiquement créé :
- **Login** : Email de l'avocat
- **Mot de passe par défaut** : `ChangeMe2024!`
- **Changement obligatoire** : À la première connexion

### Désignation d'un responsable de cabinet
1. Éditer un cabinet : `/admin/cabinets/{id}/edit`
2. Sélectionner un avocat comme "Responsable (Managing Partner)"
3. L'avocat est automatiquement promu en **ROLE_RESPO_CABINET**
4. Son compte User est associé au cabinet

---

## 🌍 URLs de l'Application

### API Publique
- `GET /api/lawyers` - Liste des avocats
- `GET /api/lawyers/{slug}` - Détails d'un avocat
- `GET /api/cabinets` - Liste des cabinets
- `GET /api/cabinets/{slug}` - Détails d'un cabinet
- `GET /api/specialties` - Liste des spécialités

### Back-office Admin
- `/` - Redirection vers le dashboard
- `/login` - Connexion
- `/admin` - Dashboard
- `/admin/cabinets` - Gestion des cabinets
- `/admin/lawyers` - Gestion des avocats
- `/admin/lawyers/me` - Profil avocat (auto-édition)
- `/admin/cabinet-types` - Gestion des types de cabinet
- `/admin/specialties` - Gestion des spécialités juridiques
- `/change-password` - Changement de mot de passe obligatoire

---

## 🧪 Tests

### Validation de l'application
```bash
# Valider les templates Twig
docker-compose exec php php bin/console lint:twig templates/

# Valider la syntaxe YAML
docker-compose exec php php bin/console lint:yaml config/

# Valider le schéma de base de données
docker-compose exec php php bin/console doctrine:schema:validate
```

### Tester l'API
```bash
# Lister les avocats
curl http://localhost/api/lawyers

# Détails d'un avocat
curl http://localhost/api/lawyers/jean-dupont

# Lister les cabinets
curl http://localhost/api/cabinets
```

---

## 📚 Documentation Complète

Voir **CLAUDE.md** pour :
- Architecture détaillée
- Modèle de données
- Système de sécurité (Voters)
- Historique des modifications
- Guide de développement

---

## 🆘 Support

En cas de problème :
1. Vérifier les logs : `docker-compose logs -f php`
2. Vider le cache : `docker-compose exec php php bin/console cache:clear`
3. Consulter CLAUDE.md pour les détails techniques
4. Utiliser `fix-permissions.sh` pour les problèmes de permissions

---

**Dernière mise à jour** : 2025-11-07
**Version** : 1.0.0
**Statut** : Production Ready ✅
