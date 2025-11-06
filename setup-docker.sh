#!/bin/bash
set -e

echo "🐳 Installation de l'environnement Docker pour Symfony..."
echo ""

# Démarrer les services
echo "📦 Démarrage des conteneurs..."
docker-compose up -d

echo "⏳ Attente du démarrage de MySQL (15 secondes)..."
sleep 15

# Vérifier que les services sont démarrés
echo "✅ Vérification des services..."
docker-compose ps

# Installer les dépendances
echo ""
echo "📦 Installation des dépendances Composer..."
docker-compose exec -T php composer install --no-interaction

# Créer la base de données
echo ""
echo "🗃️ Création de la base de données..."
docker-compose exec -T php php bin/console doctrine:database:create --if-not-exists

# Exécuter les migrations
echo ""
echo "🔄 Exécution des migrations..."
docker-compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction

# Charger les fixtures (optionnel - peut échouer si pas configuré)
echo ""
echo "📊 Chargement des fixtures (optionnel)..."
docker-compose exec -T php php bin/console doctrine:fixtures:load --no-interaction 2>/dev/null || echo "⚠️  Pas de fixtures configurées (normal)"

# Nettoyer le cache
echo ""
echo "🧹 Nettoyage du cache..."
docker-compose exec -T php php bin/console cache:clear

echo ""
echo "================================================"
echo "✅ Installation terminée avec succès !"
echo "================================================"
echo ""
echo "🌐 Application Symfony : http://localhost:8000"
echo "🗄️  Adminer (MySQL) : http://localhost:8080"
echo ""
echo "📊 Identifiants Adminer :"
echo "   Système : MySQL"
echo "   Serveur : mysql"
echo "   Utilisateur : symfony"
echo "   Mot de passe : symfony_password"
echo "   Base de données : symfony_db"
echo ""
echo "💡 Commandes utiles :"
echo "   docker-compose logs -f     # Voir les logs"
echo "   docker-compose exec php bash   # Entrer dans le conteneur"
echo "   docker-compose down        # Arrêter les services"
echo ""
