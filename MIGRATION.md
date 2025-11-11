# Guide de Migration des Données Anciennes

Ce document explique comment migrer les données de l'ancien schéma (`api_bd_old.sql`) vers le nouveau schéma avec les collections.

## 📋 Contexte

L'ancien schéma stockait un seul email et un seul téléphone par entité (Cabinet/Lawyer). Le nouveau schéma utilise des **collections** permettant plusieurs emails et téléphones par entité.

## 🎯 Ce que fait le script

Le script `bin/migrate-old-data.php` effectue les opérations suivantes :

1. **Extraction** des données depuis `public/api_bd_old.sql`
2. **Migration** des adresses (table `address`)
3. **Migration** des cabinets avec :
   - Tous les champs anciens (conservés pour compatibilité)
   - Création automatique d'une collection `email_address` depuis le champ `email`
   - Création automatique d'une collection `phone` depuis le champ `phone`
4. **Migration** des lawyers avec :
   - Tous les champs anciens (conservés pour compatibilité)
   - Création automatique d'une collection `email_address` depuis le champ `email`
   - Création automatique d'une collection `phone` depuis le champ `phone`
5. **Conservation** des relations :
   - Lawyer ↔ Cabinet
   - Cabinet ↔ Address
   - Lawyer ↔ Address

## ⚠️ Pré-requis

1. **Base de données configurée** : `DATABASE_URL` dans `.env`
2. **Fichiers SQL présents** :
   - `public/api_bd_old.sql` (données sources)
   - `public/api_bd_new.sql` (schéma de référence)
3. **Tables créées** : Le schéma actuel doit être créé (`php bin/console doctrine:schema:create` ou migrations)

## 🚀 Exécution

### Étape 1 : Sauvegarder la base actuelle (IMPORTANT)

```bash
# Dump de la base actuelle (sécurité)
mysqldump -u [user] -p [database] > backup_avant_migration_$(date +%Y%m%d_%H%M%S).sql
```

### Étape 2 : Lancer le script

```bash
php bin/migrate-old-data.php
```

Le script vous demandera confirmation avant de procéder :

```
⚠️  ATTENTION: Cette opération va:
   - Vider les tables actuelles (TRUNCATE)
   - Migrer X adresses
   - Migrer X cabinets
   - Migrer X lawyers
   - Créer les collections (phones, emails) depuis les champs anciens

Continuer ? (oui/non):
```

Tapez `oui` pour confirmer.

### Étape 3 : Vérification

1. **Back-office** : Accédez à `/admin` et vérifiez :
   - Liste des cabinets
   - Liste des lawyers
   - Collections d'emails et téléphones

2. **API** : Testez les endpoints :
   ```bash
   curl http://localhost:9002/api/cabinets
   curl http://localhost:9002/api/lawyers
   ```

3. **Créer les comptes User** :
   - Allez dans `/admin/lawyers`
   - Pour chaque lawyer, cliquez sur "Créer compte" (si email présent)

## 📊 Mapping des Données

### Cabinet

| Ancien champ | Nouveau champ/collection | Note |
|--------------|-------------------------|------|
| `email` | `email` + collection `email_address` | Champ conservé + collection créée |
| `phone` | `phone` + collection `phone` | Champ conservé + collection créée |
| `address` | `old_address` + relation `address` | Champ renommé, relation maintenue |
| `lat`, `lng` | `lat`, `lng` + dans `address` | Conservés aux deux endroits |
| Tous les autres | Identiques | Pas de changement |

### Lawyer

| Ancien champ | Nouveau champ/collection | Note |
|--------------|-------------------------|------|
| `email` | `email` + collection `email_address` | Champ conservé + collection créée |
| `phone` | `phone` + collection `phone` | Champ conservé + collection créée |
| `city` | `city` + dans `address.city` | Conservé aux deux endroits |
| Tous les autres | Identiques | Pas de changement |

## 🔄 Fallback Automatique

L'API utilise un **fallback intelligent** :

- Si la collection `email_address` est vide → utilise le champ `email` ancien
- Si la collection `phone` est vide → utilise le champ `phone` ancien

Cela assure la **rétrocompatibilité** totale.

## 🛠️ Résolution de Problèmes

### Erreur "DATABASE_URL non définie"

```bash
# Vérifier le .env
cat .env | grep DATABASE_URL

# Si manquant, ajouter :
DATABASE_URL="mysql://user:password@localhost:3306/dbname?serverVersion=8.0"
```

### Erreur "Fichier SQL introuvable"

```bash
# Vérifier la présence des fichiers
ls -lh public/*.sql

# Les fichiers doivent être :
# - public/api_bd_old.sql
# - public/api_bd_new.sql
```

### Collections vides après migration

Le script crée automatiquement UNE collection par défaut pour chaque entité qui a un email/phone dans les anciens champs. Si après migration vous ne voyez pas les collections :

1. Vérifier que les anciens champs contenaient des données
2. Vérifier les logs du script
3. Requête SQL manuelle :
   ```sql
   SELECT * FROM email_address WHERE cabinet_id = 1;
   SELECT * FROM phone WHERE lawyer_id = 1;
   ```

## 📝 Notes Importantes

1. **Spécialités** : Le script NE MIGRE PAS les spécialités (`lawyer_specialty`). Si vos données anciennes contiennent cette table, vous devez :
   - Soit l'ajouter manuellement au script
   - Soit migrer cette table séparément

2. **Images** : La table `image` n'est pas migrée. Si vous avez des images à migrer, ajoutez la logique au script.

3. **Users** : Les comptes `User` ne sont PAS créés automatiquement. Utilisez le bouton "Créer compte" dans le back-office pour chaque lawyer.

4. **IDs préservés** : Les IDs des entités sont conservés, ce qui maintient toutes les relations étrangères.

## ✅ Checklist Post-Migration

- [ ] Vérifier le nombre d'entités migrées (adresses, cabinets, lawyers)
- [ ] Tester l'affichage des collections dans les formulaires
- [ ] Tester l'ajout/suppression d'emails et téléphones
- [ ] Tester l'API `/api/cabinets` et `/api/lawyers`
- [ ] Créer les comptes User pour les lawyers (bouton "Créer compte")
- [ ] Vérifier les relations Cabinet ↔ Lawyer
- [ ] Vérifier les coordonnées GPS sur la carte
- [ ] Tester une mise à jour complète d'un cabinet et d'un lawyer

## 🆘 Support

En cas de problème :
1. Consulter les logs d'erreur du script
2. Vérifier la structure des tables (`SHOW CREATE TABLE lawyer`)
3. Restaurer depuis le backup si nécessaire
4. Contacter l'équipe de développement

---

**Date de création** : 2025-01-11
**Version du script** : 1.0
**Auteur** : Claude Code (Anthropic)
