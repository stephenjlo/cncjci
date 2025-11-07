# 🔧 Correction Erreur 413 Request Entity Too Large

## 📋 Problème

Lors de l'upload d'un logo dans le formulaire de création de cabinet, l'erreur suivante apparaît :

```
413 Request Entity Too Large
nginx/1.29.2
```

## 🎯 Cause

La taille de la requête dépasse la limite configurée dans nginx (défaut: 1M).

## ✅ Solutions

### Solution 1 : Configuration Nginx dans Docker Compose

#### Étape 1 : Créer le fichier de configuration nginx

Créez le fichier `docker/nginx/nginx.conf` ou modifiez le fichier existant :

```nginx
http {
    # Augmenter la limite à 20M (ajustez selon vos besoins)
    client_max_body_size 20M;

    # Autres paramètres...
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    sendfile on;
    keepalive_timeout 65;

    include /etc/nginx/conf.d/*.conf;
}
```

OU dans le bloc server (`docker/nginx/conf.d/default.conf`) :

```nginx
server {
    listen 80;
    server_name localhost;

    # Augmenter la limite pour ce site
    client_max_body_size 20M;

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass php:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;

        # Limites pour PHP
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ \.php$ {
        return 404;
    }
}
```

#### Étape 2 : Créer le fichier de configuration PHP

Créez le fichier `docker/php/php.ini` :

```ini
; Limites d'upload
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M

; Autres paramètres recommandés
file_uploads = On
max_file_uploads = 20

; Affichage des erreurs (développement)
display_errors = On
display_startup_errors = On
error_reporting = E_ALL

; Timezone
date.timezone = Africa/Abidjan
```

#### Étape 3 : Modifier docker-compose.yml

Ajoutez les volumes pour monter les fichiers de configuration :

```yaml
services:
  nginx:
    image: nginx:latest
    ports:
      - "9002:80"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    depends_on:
      - php

  php:
    image: php:8.2-fpm
    volumes:
      - ./:/var/www/html
      - ./docker/php/php.ini:/usr/local/etc/php/php.ini
```

#### Étape 4 : Redémarrer les conteneurs

```bash
docker-compose down
docker-compose up -d
```

---

### Solution 2 : Modification à chaud (Temporaire)

Si vous ne pouvez pas redémarrer les conteneurs :

#### Pour Nginx :

```bash
# Entrer dans le conteneur nginx
docker-compose exec nginx bash

# Éditer la configuration
echo "client_max_body_size 20M;" > /etc/nginx/conf.d/upload.conf

# Recharger nginx
nginx -s reload

# Sortir
exit
```

#### Pour PHP :

```bash
# Entrer dans le conteneur php
docker-compose exec php bash

# Créer/modifier php.ini
cat > /usr/local/etc/php/php.ini << EOF
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
EOF

# Redémarrer PHP-FPM
kill -USR2 1

# Sortir
exit
```

---

### Solution 3 : Variables d'environnement (Recommandé pour Docker)

Modifiez `docker-compose.yml` pour utiliser des variables d'environnement :

```yaml
services:
  php:
    image: php:8.2-fpm
    environment:
      - PHP_UPLOAD_MAX_FILESIZE=20M
      - PHP_POST_MAX_SIZE=20M
      - PHP_MAX_EXECUTION_TIME=300
      - PHP_MEMORY_LIMIT=256M
    volumes:
      - ./:/var/www/html
```

Puis créez un script d'entrée personnalisé `docker/php/docker-entrypoint.sh` :

```bash
#!/bin/bash
set -e

# Créer php.ini avec les variables d'environnement
cat > /usr/local/etc/php/php.ini << EOF
upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE:-20M}
post_max_size = ${PHP_POST_MAX_SIZE:-20M}
max_execution_time = ${PHP_MAX_EXECUTION_TIME:-300}
memory_limit = ${PHP_MEMORY_LIMIT:-256M}
EOF

# Lancer PHP-FPM
exec php-fpm
```

---

## 🧪 Vérification

### Vérifier la configuration Nginx

```bash
docker-compose exec nginx nginx -t
docker-compose exec nginx grep -r "client_max_body_size" /etc/nginx/
```

### Vérifier la configuration PHP

```bash
docker-compose exec php php -i | grep -E "upload_max_filesize|post_max_size"
```

### Tester l'upload

Créez un fichier de test `test-upload.php` dans `public/` :

```php
<?php
phpinfo();
```

Accédez à `http://localhost:9002/test-upload.php` et cherchez :
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`

---

## 📊 Recommandations de Limites

| Cas d'usage | upload_max_filesize | post_max_size | Nginx client_max_body_size |
|-------------|---------------------|---------------|----------------------------|
| Logos uniquement | 5M | 10M | 10M |
| Images + Documents | 10M | 20M | 20M |
| Fichiers volumineux | 50M | 100M | 100M |

**Note** : `post_max_size` doit toujours être ≥ `upload_max_filesize` et Nginx `client_max_body_size` doit être ≥ `post_max_size`.

---

## 🔍 Débogage

### Logs Nginx

```bash
docker-compose logs nginx | tail -50
```

### Logs PHP

```bash
docker-compose logs php | tail -50
```

### Tester avec curl

```bash
# Créer un fichier de test de 5M
dd if=/dev/zero of=test.jpg bs=1M count=5

# Tenter l'upload
curl -F "file=@test.jpg" http://localhost:9002/admin/cabinets/new
```

---

## ✅ Checklist

- [ ] Configuration Nginx modifiée (`client_max_body_size`)
- [ ] Configuration PHP modifiée (`upload_max_filesize`, `post_max_size`)
- [ ] docker-compose.yml mis à jour avec les volumes
- [ ] Conteneurs redémarrés (`docker-compose up -d`)
- [ ] Configuration vérifiée (`nginx -t` et `php -i`)
- [ ] Test d'upload effectué
- [ ] Logs vérifiés (pas d'erreurs)

---

**Date** : 2025-11-07
**Status** : ✅ Documentation complète
