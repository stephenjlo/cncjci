#!/bin/bash

# Script de validation des améliorations du module Cabinet
# Usage: ./validate-cabinet-improvements.sh

echo "🔍 Validation des améliorations du module Cabinet..."
echo ""

# Couleurs pour les messages
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Compteur d'erreurs
ERRORS=0

# Fonction pour afficher le résultat d'un test
check_result() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ $1${NC}"
    else
        echo -e "${RED}❌ $1${NC}"
        ((ERRORS++))
    fi
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📝 1. Vérification syntaxe PHP"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

docker-compose exec -T php php -l src/Service/FileUploadService.php > /dev/null 2>&1
check_result "FileUploadService.php"

docker-compose exec -T php php -l src/Form/PhoneType.php > /dev/null 2>&1
check_result "PhoneType.php"

docker-compose exec -T php php -l src/Form/EmailAddressType.php > /dev/null 2>&1
check_result "EmailAddressType.php"

docker-compose exec -T php php -l src/Form/CabinetType.php > /dev/null 2>&1
check_result "CabinetType.php"

docker-compose exec -T php php -l src/EventSubscriber/AddressValidationSubscriber.php > /dev/null 2>&1
check_result "AddressValidationSubscriber.php"

docker-compose exec -T php php -l src/Controller/Admin/CabinetAdminController.php > /dev/null 2>&1
check_result "CabinetAdminController.php"

docker-compose exec -T php php -l src/Controller/Api/CabinetController.php > /dev/null 2>&1
check_result "CabinetController.php"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎨 2. Vérification templates Twig"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

docker-compose exec -T php php bin/console lint:twig templates/admin/cabinet/ > /dev/null 2>&1
check_result "Templates Cabinet (form.html.twig, index.html.twig)"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 3. Vérification services et paramètres"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

docker-compose exec -T php php bin/console debug:container FileUploadService > /dev/null 2>&1
check_result "Service FileUploadService enregistré"

docker-compose exec -T php php bin/console debug:container --parameter=uploads_directory > /dev/null 2>&1
check_result "Paramètre uploads_directory défini"

docker-compose exec -T php php bin/console debug:container --parameter=default_cabinet_logo > /dev/null 2>&1
check_result "Paramètre default_cabinet_logo défini"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🗺️ 4. Vérification routes"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

docker-compose exec -T php php bin/console debug:router admin_cabinet_index > /dev/null 2>&1
check_result "Route admin_cabinet_index"

docker-compose exec -T php php bin/console debug:router admin_cabinet_new > /dev/null 2>&1
check_result "Route admin_cabinet_new"

docker-compose exec -T php php bin/console debug:router admin_cabinet_edit > /dev/null 2>&1
check_result "Route admin_cabinet_edit"

docker-compose exec -T php php bin/console debug:router admin_lawyer_index > /dev/null 2>&1
check_result "Route admin_lawyer_index"

docker-compose exec -T php php bin/console debug:router app_api_cabinet_list > /dev/null 2>&1
check_result "Route app_api_cabinet_list"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "💾 5. Vérification Doctrine"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

docker-compose exec -T php php bin/console doctrine:schema:validate > /dev/null 2>&1
check_result "Schéma Doctrine valide et synchronisé"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📁 6. Vérification dossiers et permissions"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

docker-compose exec -T php test -d /var/www/html/public/uploads
check_result "Dossier /public/uploads existe"

docker-compose exec -T php test -d /var/www/html/public/uploads/cabinets
check_result "Dossier /public/uploads/cabinets existe"

docker-compose exec -T php test -w /var/www/html/public/uploads/cabinets
check_result "Dossier /public/uploads/cabinets est accessible en écriture"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🧹 7. Clear cache"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

docker-compose exec -T php php bin/console cache:clear > /dev/null 2>&1
check_result "Cache Symfony cleared"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 Résultat final"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✅ Tous les tests sont passés avec succès !${NC}"
    echo ""
    echo -e "${YELLOW}📋 Prochaines étapes :${NC}"
    echo "1. Tester la création d'un cabinet dans l'interface (/admin/cabinets/new)"
    echo "2. Vérifier l'API : curl http://localhost:8000/api/cabinets"
    echo "3. Uploader un logo et vérifier le dossier /public/uploads/cabinets/"
    echo ""
    echo "📖 Voir TEST-CABINET-IMPROVEMENTS.md pour les tests fonctionnels détaillés"
    exit 0
else
    echo -e "${RED}❌ $ERRORS erreur(s) détectée(s)${NC}"
    echo ""
    echo -e "${YELLOW}🔍 Vérifiez les logs :${NC}"
    echo "docker-compose logs -f php"
    exit 1
fi
