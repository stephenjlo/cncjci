#!/bin/bash

echo "🔧 Correction de l'erreur 413 Request Entity Too Large"
echo "======================================================="
echo ""

# Fichier de configuration nginx
NGINX_CONF="/etc/nginx/nginx.conf"
NGINX_SITE_CONF="/etc/nginx/conf.d/default.conf"

# Fichier de configuration PHP
PHP_INI="/usr/local/etc/php/php.ini"
PHP_FPM_CONF="/usr/local/etc/php-fpm.d/www.conf"

echo "📝 Configuration actuelle:"
echo ""

# Vérifier la limite actuelle de nginx
if [ -f "$NGINX_CONF" ]; then
    echo "✅ Nginx configuration trouvée"
    grep -i "client_max_body_size" $NGINX_CONF || echo "⚠️  client_max_body_size non défini (défaut: 1M)"
fi

# Vérifier la limite actuelle de PHP
if [ -f "$PHP_INI" ]; then
    echo "✅ PHP configuration trouvée"
    grep "upload_max_filesize" $PHP_INI || echo "⚠️  upload_max_filesize non défini"
    grep "post_max_size" $PHP_INI || echo "⚠️  post_max_size non défini"
fi

echo ""
echo "🔧 Application des corrections..."
echo ""

# Instructions pour l'utilisateur
echo "📋 INSTRUCTIONS:"
echo ""
echo "1️⃣  NGINX - Ajoutez dans /etc/nginx/nginx.conf ou /etc/nginx/conf.d/default.conf:"
echo ""
echo "http {"
echo "    client_max_body_size 20M;"
echo "    ..."
echo "}"
echo ""
echo "OU dans le bloc server:"
echo ""
echo "server {"
echo "    client_max_body_size 20M;"
echo "    ..."
echo "}"
echo ""
echo "2️⃣  PHP - Ajoutez dans /usr/local/etc/php/php.ini:"
echo ""
echo "upload_max_filesize = 20M"
echo "post_max_size = 20M"
echo "max_execution_time = 300"
echo ""
echo "3️⃣  Redémarrez les services:"
echo ""
echo "docker-compose restart nginx"
echo "docker-compose restart php"
echo ""
echo "✅ Pour Docker Compose, modifiez directement le docker-compose.yml:"
echo ""
echo "services:"
echo "  nginx:"
echo "    volumes:"
echo "      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf"
echo ""
echo "  php:"
echo "    volumes:"
echo "      - ./docker/php/php.ini:/usr/local/etc/php/php.ini"
echo ""
echo "Puis relancez: docker-compose up -d"
echo ""
