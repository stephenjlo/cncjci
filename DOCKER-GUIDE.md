# 🐳 Guide Docker Compose - Symfony avec MySQL et Adminer

## 📦 Ce que contient ce setup

✅ **PHP 8.2-FPM** - Pour exécuter Symfony  
✅ **Nginx** - Serveur web  
✅ **MySQL 8.0** - Base de données  
✅ **Adminer** - Interface web pour gérer MySQL  
✅ **Composer** - Installé automatiquement  

---

## 🚀 Installation et démarrage (3 étapes)

### 1. Copier les fichiers dans ton projet

```bash
cd ~/projets/symfony-app

# Copier le docker-compose.yml à la racine
cp /chemin/vers/docker-compose.yml .

# Créer le dossier docker et la config Nginx
mkdir -p docker/nginx
cp /chemin/vers/default.conf docker/nginx/

# Créer le fichier .env.local
cp .env.local.docker .env.local
```

### 2. Démarrer les conteneurs

```bash
# Lancer tous les services
docker-compose up -d

# Voir les logs (optionnel)
docker-compose logs -f
```

### 3. Initialiser la base de données

```bash
# Entrer dans le conteneur PHP
docker-compose exec php bash

# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# (Optionnel) Charger les fixtures
php bin/console doctrine:fixtures:load

# Sortir du conteneur
exit
```

---

## 🌐 Accès aux services

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Application Symfony** | http://localhost:8000 | - |
| **Adminer (BDD)** | http://localhost:8080 | Voir ci-dessous |
| **MySQL** | localhost:3306 | Voir ci-dessous |

### 🔑 Identifiants Adminer

```
Système : MySQL
Serveur : mysql
Utilisateur : symfony
Mot de passe : symfony_password
Base de données : symfony_db
```

**OU en tant que root :**
```
Utilisateur : root
Mot de passe : root
```

---

## 🛠️ Commandes utiles

### Gestion des conteneurs

```bash
# Démarrer les services
docker-compose up -d

# Arrêter les services
docker-compose down

# Voir les logs
docker-compose logs -f

# Voir les logs d'un service spécifique
docker-compose logs -f php
docker-compose logs -f nginx

# Redémarrer un service
docker-compose restart php

# Voir l'état des services
docker-compose ps
```

### Exécuter des commandes Symfony

```bash
# Entrer dans le conteneur PHP
docker-compose exec php bash

# OU exécuter directement une commande
docker-compose exec php php bin/console doctrine:migrations:migrate
docker-compose exec php php bin/console cache:clear
docker-compose exec php composer install
```

### Commandes base de données

```bash
# Créer la base
docker-compose exec php php bin/console doctrine:database:create

# Voir le schéma
docker-compose exec php php bin/console doctrine:schema:validate

# Générer une migration
docker-compose exec php php bin/console make:migration

# Exécuter les migrations
docker-compose exec php php bin/console doctrine:migrations:migrate

# Réinitialiser la base (ATTENTION: efface tout!)
docker-compose exec php php bin/console doctrine:database:drop --force
docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate
```

### Installer des dépendances

```bash
# Installer un package
docker-compose exec php composer require vendor/package

# Mettre à jour les dépendances
docker-compose exec php composer update
```

---

## 🔧 Configuration personnalisée

### Changer les ports

Éditer `docker-compose.yml` :

```yaml
nginx:
  ports:
    - "8080:80"  # Change 8000 en 8080

adminer:
  ports:
    - "9090:8080"  # Change 8080 en 9090
```

### Changer les identifiants MySQL

Éditer `docker-compose.yml` :

```yaml
mysql:
  environment:
    MYSQL_ROOT_PASSWORD: ton_mot_de_passe_root
    MYSQL_DATABASE: ton_nom_de_base
    MYSQL_USER: ton_utilisateur
    MYSQL_PASSWORD: ton_mot_de_passe
```

**N'oublie pas de mettre à jour `.env.local` également !**

---

## 📊 Structure des fichiers

```
ton-projet/
├── docker-compose.yml          # Configuration Docker
├── docker/
│   └── nginx/
│       └── default.conf        # Config Nginx
├── .env.local                  # Variables d'environnement
├── public/                     # Point d'entrée Symfony
├── src/                        # Code source
├── var/                        # Cache et logs
└── vendor/                     # Dépendances
```

---

## 🐛 Résolution des problèmes

### Port déjà utilisé

```bash
# Vérifier quel processus utilise le port
sudo lsof -i :8000
sudo lsof -i :3306

# Changer le port dans docker-compose.yml
```

### Permission denied sur var/

```bash
# Depuis le conteneur PHP
docker-compose exec php chmod -R 777 var/
```

### Composer ne fonctionne pas

```bash
# Réinstaller composer dans le conteneur
docker-compose exec php bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
exit
```

### La base de données n'est pas créée

```bash
# Vérifier que MySQL est démarré
docker-compose ps

# Voir les logs MySQL
docker-compose logs mysql

# Se connecter à MySQL directement
docker-compose exec mysql mysql -uroot -proot

# Dans MySQL :
SHOW DATABASES;
CREATE DATABASE IF NOT EXISTS symfony_db;
exit;
```

### L'application ne se charge pas

```bash
# Vérifier les logs Nginx
docker-compose logs nginx

# Vérifier les logs PHP
docker-compose logs php

# Vérifier les permissions
docker-compose exec php ls -la public/
```

---

## 🔄 Workflow de développement

### Démarrage de la journée

```bash
# Démarrer les conteneurs
docker-compose up -d

# Vérifier que tout tourne
docker-compose ps
```

### Pendant le développement

```bash
# Tes fichiers sont synchronisés automatiquement !
# Édite ton code normalement avec ton IDE

# Nettoyer le cache si nécessaire
docker-compose exec php php bin/console cache:clear
```

### Fin de journée

```bash
# Arrêter les conteneurs (garde les données)
docker-compose down

# OU les laisser tourner (ils consomment peu)
```

---

## 🗑️ Nettoyage complet

### Supprimer tout (conteneurs + volumes + données)

```bash
# ATTENTION: Ceci efface la base de données !
docker-compose down -v

# Supprimer les images Docker (optionnel)
docker-compose down --rmi all -v
```

### Réinitialiser seulement la base

```bash
# Arrêter les services
docker-compose down

# Supprimer seulement le volume MySQL
docker volume rm symfony-app_mysql-data

# Redémarrer
docker-compose up -d
```

---

## ⚡ Script d'installation rapide

Créer un fichier `setup.sh` :

```bash
#!/bin/bash
set -e

echo "🐳 Installation de l'environnement Docker..."

# Démarrer les services
docker-compose up -d

echo "⏳ Attente du démarrage de MySQL..."
sleep 10

# Installer les dépendances
echo "📦 Installation des dépendances Composer..."
docker-compose exec -T php composer install

# Créer la base de données
echo "🗃️ Création de la base de données..."
docker-compose exec -T php php bin/console doctrine:database:create --if-not-exists

# Exécuter les migrations
echo "🔄 Exécution des migrations..."
docker-compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction

# Charger les fixtures (optionnel)
echo "📊 Chargement des fixtures..."
docker-compose exec -T php php bin/console doctrine:fixtures:load --no-interaction || true

echo "✅ Installation terminée !"
echo ""
echo "🌐 Application : http://localhost:8000"
echo "🗄️ Adminer : http://localhost:8080"
echo ""
echo "Identifiants Adminer :"
echo "  Serveur : mysql"
echo "  Utilisateur : symfony"
echo "  Mot de passe : symfony_password"
echo "  Base : symfony_db"
```

Utilisation :
```bash
chmod +x setup.sh
./setup.sh
```

---

## 📚 Commandes à retenir

| Action | Commande |
|--------|----------|
| Démarrer | `docker-compose up -d` |
| Arrêter | `docker-compose down` |
| Logs | `docker-compose logs -f` |
| Entrer dans PHP | `docker-compose exec php bash` |
| Console Symfony | `docker-compose exec php php bin/console` |
| Composer | `docker-compose exec php composer` |
| Redémarrer | `docker-compose restart` |

---

## ✅ Checklist de démarrage

- [ ] Copier `docker-compose.yml` à la racine
- [ ] Créer `docker/nginx/default.conf`
- [ ] Créer `.env.local` avec la config MySQL
- [ ] Lancer `docker-compose up -d`
- [ ] Créer la base : `docker-compose exec php php bin/console doctrine:database:create`
- [ ] Migrer : `docker-compose exec php php bin/console doctrine:migrations:migrate`
- [ ] Tester : http://localhost:8000
- [ ] Accéder à Adminer : http://localhost:8080

---

## 🎉 Avantages de cette solution

✅ **Isolation complète** - Rien à installer sur ta machine  
✅ **MySQL identique** au serveur - Pas de problèmes de compatibilité  
✅ **Adminer intégré** - Interface graphique pour la BDD  
✅ **Hot reload** - Changements de code instantanés  
✅ **Portable** - Fonctionne sur n'importe quelle machine  
✅ **Un seul fichier** - docker-compose.yml  

---

**Tu es prêt à développer ! 🚀**
