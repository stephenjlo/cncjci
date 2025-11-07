# 🧪 Tests des améliorations du module Cabinet

## ✅ Validation technique (Docker)

Toutes les commandes ont été testées avec succès dans l'environnement Docker :

```bash
# Vérification syntaxe PHP
docker-compose exec -T php php -l src/Service/FileUploadService.php
docker-compose exec -T php php -l src/Form/PhoneType.php
docker-compose exec -T php php -l src/Form/EmailAddressType.php
docker-compose exec -T php php -l src/Form/CabinetType.php
docker-compose exec -T php php -l src/EventSubscriber/AddressValidationSubscriber.php
docker-compose exec -T php php -l src/Controller/Admin/CabinetAdminController.php
docker-compose exec -T php php -l src/Controller/Api/CabinetController.php

# Vérification templates Twig
docker-compose exec -T php php bin/console lint:twig templates/admin/cabinet/

# Clear cache
docker-compose exec -T php php bin/console cache:clear

# Vérification routes
docker-compose exec -T php php bin/console debug:router | grep -E "(cabinet|lawyer)"

# Vérification services
docker-compose exec -T php php bin/console debug:container FileUploadService

# Vérification paramètres
docker-compose exec -T php php bin/console debug:container --parameter=uploads_directory
docker-compose exec -T php php bin/console debug:container --parameter=default_cabinet_logo

# Vérification schéma Doctrine
docker-compose exec -T php php bin/console doctrine:schema:validate
```

**Résultats** : ✅ Tous les tests techniques passent avec succès

---

## 📋 Tests fonctionnels à effectuer manuellement

### Test 1 : Création d'un cabinet SANS logo
**Scénario** :
1. Se connecter en tant que SUPER_ADMIN
2. Aller sur `/admin/cabinets/new`
3. Remplir uniquement :
   - Nom : "Cabinet Test"
   - Type : Sélectionner un type
   - Au moins 1 téléphone (Standard + numéro)
   - Au moins 1 email (Contact + email)
4. **Ne PAS uploader de logo**
5. Soumettre le formulaire

**Résultat attendu** :
- ✅ Cabinet créé avec succès
- ✅ Logo par défaut attribué : `https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png`
- ✅ Slug généré automatiquement : `cabinet-test`

**Vérification API** :
```bash
curl http://localhost:8000/api/cabinets/cabinet-test
```
Le champ `logoUrl` doit contenir l'URL du logo par défaut.

---

### Test 2 : Upload d'un logo custom
**Scénario** :
1. Modifier le cabinet créé précédemment
2. Uploader une image (PNG, JPEG, max 2Mo)
3. Soumettre

**Résultat attendu** :
- ✅ Logo uploadé dans `/public/uploads/cabinets/`
- ✅ `logoUrl` mis à jour avec le chemin `/uploads/cabinets/nom-fichier-xxxxx.ext`
- ✅ Ancien logo par défaut NON supprimé (car c'est une URL externe)

**Vérification** :
```bash
docker-compose exec -T php ls -la /var/www/html/public/uploads/cabinets/
```

---

### Test 3 : Collections Téléphones et Emails
**Scénario** :
1. Créer un nouveau cabinet
2. Ajouter plusieurs téléphones :
   - Type : Standard, numéro : +225 XX XX XX XX
   - Type : Mobile, numéro : +225 YY YY YY YY
3. Ajouter plusieurs emails :
   - Type : Contact, email : contact@cabinet.ci
   - Type : Info, email : info@cabinet.ci
4. Soumettre

**Résultat attendu** :
- ✅ Le premier téléphone a `isPrimary = true`, les autres `false`
- ✅ Le premier email a `isPrimary = true`, les autres `false`
- ✅ Positions automatiquement définies (0, 1, 2...)
- ✅ Impossible de supprimer le dernier élément (alerte JavaScript)

**Vérification en BDD** :
```sql
SELECT * FROM phone WHERE cabinet_id = [ID] ORDER BY position;
SELECT * FROM email_address WHERE cabinet_id = [ID] ORDER BY position;
```

---

### Test 4 : Recherche OpenStreetMap
**Scénario** :
1. Créer un nouveau cabinet
2. Utiliser la barre de recherche de la carte
3. Rechercher : "Cocody, Abidjan"
4. Cliquer sur le résultat

**Résultat attendu** :
- ✅ Carte centrée sur Cocody
- ✅ Marqueur placé automatiquement
- ✅ Champs `line1`, `city`, `lat`, `lng` remplis automatiquement
- ✅ Marqueur draggable (peut être déplacé)

---

### Test 5 : Adresse vide non créée
**Scénario** :
1. Créer un cabinet
2. NE PAS remplir les champs d'adresse
3. NE PAS utiliser la carte
4. Soumettre

**Résultat attendu** :
- ✅ Cabinet créé
- ✅ AUCUNE entrée dans la table `address`
- ✅ `cabinet.address_id = NULL`

**Vérification** :
```sql
SELECT * FROM address WHERE id NOT IN (SELECT address_id FROM cabinet WHERE address_id IS NOT NULL);
-- Doit retourner 0 lignes orphelines
```

---

### Test 6 : Génération automatique du slug
**Scénario** :
1. Créer un cabinet avec nom : "Cabinet Martin & Associés"
2. **Laisser le champ slug VIDE**
3. Soumettre

**Résultat attendu** :
- ✅ Slug généré : `cabinet-martin-associes`
- ✅ Caractères spéciaux et espaces remplacés

---

### Test 7 : Actions dans le tableau de liste
**Scénario** :
1. Aller sur `/admin/cabinets`
2. Vérifier les actions disponibles pour chaque cabinet

**Résultat attendu** :
- ✅ Bouton "Modifier" (icône crayon + label)
- ✅ Bouton "Avocats" (icône personnes + label) → redirige vers `/admin/lawyers?cabinet=[ID]`
- ✅ Bouton "Activer/Désactiver" (icône play/pause)

**Test du filtrage** :
```
Cliquer sur "Avocats" pour un cabinet spécifique
→ Doit afficher uniquement les avocats de ce cabinet
```

---

### Test 8 : API avec logo par défaut
**Scénario** :
1. Appeler l'API : `GET /api/cabinets`
2. Appeler l'API : `GET /api/cabinets/{slug}`

**Résultat attendu** :
- ✅ Tous les cabinets ont un champ `logoUrl` non vide
- ✅ Cabinets sans logo custom ont l'URL par défaut
- ✅ Cabinets avec logo custom ont `/uploads/cabinets/...`

**Exemple de réponse JSON** :
```json
{
  "id": 1,
  "name": "Cabinet Test",
  "slug": "cabinet-test",
  "logoUrl": "https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png",
  "phones": [
    {
      "label": "Standard",
      "number": "+225 XX XX XX XX",
      "isPrimary": true,
      "position": 0
    }
  ],
  "emails": [
    {
      "label": "Contact",
      "email": "contact@cabinet.ci",
      "isPrimary": true,
      "position": 0
    }
  ]
}
```

---

## 🐛 Tests de régression

### Vérifier que les fonctionnalités existantes fonctionnent toujours :

1. ✅ Désignation du responsable de cabinet (managingPartner)
2. ✅ Promotion automatique en RESPO_CABINET
3. ✅ Filtrage des lawyers par cabinet pour RESPO_CABINET
4. ✅ Activation/Désactivation de cabinets
5. ✅ Recherche de cabinets par nom
6. ✅ Pagination (20 par page)

---

## 📊 Checklist finale

- [ ] Test 1 : Cabinet sans logo (logo par défaut)
- [ ] Test 2 : Upload logo custom
- [ ] Test 3 : Collections téléphones/emails
- [ ] Test 4 : Recherche OpenStreetMap
- [ ] Test 5 : Adresse vide non créée
- [ ] Test 6 : Génération automatique slug
- [ ] Test 7 : Actions tableau de liste
- [ ] Test 8 : API avec logo par défaut
- [ ] Tests de régression (fonctionnalités existantes)

---

## 🔧 Commandes utiles Docker

```bash
# Logs PHP en temps réel
docker-compose logs -f php

# Entrer dans le container PHP
docker-compose exec php bash

# Vérifier les permissions
docker-compose exec -T php ls -la /var/www/html/public/uploads/cabinets/

# Exécuter une commande Symfony
docker-compose exec -T php php bin/console [commande]

# Vider le cache
docker-compose exec -T php php bin/console cache:clear

# Voir les routes
docker-compose exec -T php php bin/console debug:router

# Voir les services
docker-compose exec -T php php bin/console debug:container [service]
```

---

**Date de création** : 2025-11-07
**Auteur** : Claude Code
**Version** : 1.0
