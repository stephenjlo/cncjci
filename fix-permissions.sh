#!/bin/bash

# Script to fix permissions for Symfony project in Docker environment
# Run this script if you encounter "Permission denied" errors in src/ directories

echo "Fixing permissions for Symfony directories..."

# Fix src/ subdirectories
chmod -R 775 src/
chmod -R 775 config/
chmod -R 775 public/
chmod -R 775 templates/
chmod -R 775 migrations/

# Fix var/ and ensure Docker user can write
chmod -R 777 var/

# Fix files permissions
find src/ -type f -exec chmod 664 {} \;
find config/ -type f -exec chmod 664 {} \;
find templates/ -type f -exec chmod 664 {} \;
find migrations/ -type f -exec chmod 664 {} \;

# Make this script executable
chmod +x fix-permissions.sh

echo "✅ Permissions fixed successfully!"
echo ""
echo "You can now run:"
echo "  docker-compose exec php php bin/console cache:clear"
echo "  docker-compose exec php php bin/console doctrine:migrations:migrate"
