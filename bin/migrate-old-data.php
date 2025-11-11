#!/usr/bin/env php
<?php
/**
 * Script de migration des données de api_bd_old.sql vers le nouveau schéma
 *
 * Ce script migre les données en conservant:
 * - Les adresses (table address)
 * - Les cabinets avec leurs champs anciens + nouvelles collections (phone, email)
 * - Les lawyers avec leurs champs anciens + nouvelles collections (phone, email)
 * - Les relations lawyer ↔ cabinet
 * - Les spécialités des lawyers
 *
 * Usage: php bin/migrate-old-data.php
 */

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->bootEnv(__DIR__ . '/../.env');

echo "═══════════════════════════════════════════════════════════════\n";
echo "  MIGRATION DONNÉES api_bd_old.sql → Nouveau Schéma\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Récupérer les informations de connexion depuis DATABASE_URL
$databaseUrl = $_ENV['DATABASE_URL'] ?? '';
if (empty($databaseUrl)) {
    die("❌ DATABASE_URL non définie dans .env\n");
}

// Parser DATABASE_URL (format: mysql://user:password@host:port/dbname?params)
// Retirer les paramètres GET (serverVersion, charset, etc.)
$databaseUrlClean = preg_replace('/\?.*$/', '', $databaseUrl);

if (!preg_match('#^mysql://([^:]+):([^@]+)@([^:/]+)(?::(\d+))?/(.+)#', $databaseUrlClean, $matches)) {
    die("❌ Format DATABASE_URL invalide. Format attendu: mysql://user:password@host:port/dbname\n");
}

list(, $user, $password, $host, $port, $dbname) = $matches;
$port = $port ?: '3306'; // Port par défaut si non spécifié

echo "📦 Connexion à la base de données...\n";
echo "   Host: $host:$port\n";
echo "   Database: $dbname\n\n";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage() . "\n");
}

echo "✅ Connecté avec succès\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 1 : Charger les fichiers SQL
// ═══════════════════════════════════════════════════════════════
echo "📖 Lecture des fichiers SQL...\n";

$oldSqlFile = __DIR__ . '/../public/api_bd_old.sql';
$newSqlFile = __DIR__ . '/../public/api_bd_new.sql';

if (!file_exists($oldSqlFile)) {
    die("❌ Fichier $oldSqlFile introuvable\n");
}

if (!file_exists($newSqlFile)) {
    die("❌ Fichier $newSqlFile introuvable\n");
}

echo "   ✓ api_bd_old.sql trouvé\n";
echo "   ✓ api_bd_new.sql trouvé\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 2 : Parser api_bd_old.sql et extraire les données
// ═══════════════════════════════════════════════════════════════
echo "🔍 Extraction des données de l'ancien schéma...\n";

$oldSql = file_get_contents($oldSqlFile);

// Fonction pour extraire les INSERT d'une table (robuste pour apostrophes et caractères spéciaux)
function extractInserts(string $sql, string $tableName): array {
    $pattern = "/INSERT INTO `$tableName` \([^)]+\) VALUES\s*\n((?:.+?));/s";
    if (preg_match($pattern, $sql, $matches)) {
        $valuesString = $matches[1];

        // Parser les lignes de values avec gestion correcte des strings SQL
        $rows = [];

        // Utiliser une approche caractère par caractère pour parser correctement
        $currentRow = [];
        $currentValue = '';
        $inString = false;
        $escaped = false;
        $depth = 0;

        $chars = str_split($valuesString);
        $len = count($chars);

        for ($i = 0; $i < $len; $i++) {
            $char = $chars[$i];
            $nextChar = ($i + 1 < $len) ? $chars[$i + 1] : null;

            if ($escaped) {
                $currentValue .= $char;
                $escaped = false;
                continue;
            }

            if ($char === '\\') {
                $currentValue .= $char;
                $escaped = true;
                continue;
            }

            if ($char === "'" && !$inString) {
                $inString = true;
                $currentValue .= $char;
                continue;
            }

            if ($char === "'" && $inString) {
                // Vérifier si c'est un échappement SQL '' ou la fin de la string
                if ($nextChar === "'") {
                    $currentValue .= $char . $nextChar;
                    $i++; // Skip next quote
                    continue;
                } else {
                    $inString = false;
                    $currentValue .= $char;
                    continue;
                }
            }

            if ($inString) {
                $currentValue .= $char;
                continue;
            }

            // Hors d'une string
            if ($char === '(') {
                $depth++;
                if ($depth === 1) {
                    $currentRow = [];
                    $currentValue = '';
                    continue;
                }
            }

            if ($char === ')') {
                $depth--;
                if ($depth === 0) {
                    // Fin d'une ligne de valeurs
                    $currentRow[] = parseValue($currentValue);
                    $rows[] = $currentRow;
                    $currentValue = '';
                    continue;
                }
            }

            if ($char === ',' && $depth === 1) {
                // Séparateur de valeurs
                $currentRow[] = parseValue($currentValue);
                $currentValue = '';
                continue;
            }

            $currentValue .= $char;
        }

        return $rows;
    }
    return [];
}

// Helper pour parser une valeur SQL
function parseValue(string $value): mixed {
    $value = trim($value);

    if ($value === 'NULL') {
        return null;
    }

    // String entre quotes
    if (preg_match("/^'(.*)'$/s", $value, $m)) {
        // Unescape SQL quotes
        return str_replace("''", "'", $m[1]);
    }

    // Nombre
    if (is_numeric($value)) {
        return $value;
    }

    return $value;
}

// Extraire les colonnes d'une table depuis CREATE TABLE
function extractColumns(string $sql, string $tableName): array {
    $pattern = "/CREATE TABLE `$tableName` \(\s*\n(.*?)\n\) ENGINE/s";
    if (preg_match($pattern, $sql, $matches)) {
        $columnsBlock = $matches[1];
        $lines = explode("\n", $columnsBlock);
        $columns = [];
        foreach ($lines as $line) {
            if (preg_match('/^\s*`(\w+)`\s+/', $line, $m)) {
                $columns[] = $m[1];
            }
        }
        return $columns;
    }
    return [];
}

$addressColumns = extractColumns($oldSql, 'address');
$addressData = extractInserts($oldSql, 'address');

$cabinetColumns = extractColumns($oldSql, 'cabinet');
$cabinetData = extractInserts($oldSql, 'cabinet');

$lawyerColumns = extractColumns($oldSql, 'lawyer');
$lawyerData = extractInserts($oldSql, 'lawyer');

echo "   ✓ Address: " . count($addressData) . " lignes\n";
echo "   ✓ Cabinet: " . count($cabinetData) . " lignes\n";
echo "   ✓ Lawyer: " . count($lawyerData) . " lignes\n\n";

// Fonction helper pour créer un array associatif colonne => valeur
function rowToAssoc(array $columns, array $values): array {
    $result = [];
    foreach ($columns as $i => $col) {
        $result[$col] = $values[$i] ?? null;
    }
    return $result;
}

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 3 : Confirmation avant migration
// ═══════════════════════════════════════════════════════════════
echo "⚠️  ATTENTION: Cette opération va:\n";
echo "   - Vider les tables actuelles (TRUNCATE)\n";
echo "   - Migrer " . count($addressData) . " adresses\n";
echo "   - Migrer " . count($cabinetData) . " cabinets\n";
echo "   - Migrer " . count($lawyerData) . " lawyers\n";
echo "   - Créer les collections (phones, emails) depuis les champs anciens\n\n";

echo "Continuer ? (oui/non): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if (strtolower($line) !== 'oui') {
    die("❌ Migration annulée\n");
}

echo "\n🚀 Démarrage de la migration...\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 4 : Vider les tables actuelles
// ═══════════════════════════════════════════════════════════════
echo "🗑️  Vidage des tables...\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE phone');
$pdo->exec('TRUNCATE TABLE email_address');
$pdo->exec('TRUNCATE TABLE lawyer_specialty');
$pdo->exec('TRUNCATE TABLE lawyer');
$pdo->exec('TRUNCATE TABLE cabinet');
$pdo->exec('TRUNCATE TABLE address');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

echo "   ✓ Tables vidées\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 5 : Migrer les adresses
// ═══════════════════════════════════════════════════════════════
echo "📍 Migration des adresses...\n";

$addressMap = []; // old_id => new_id

foreach ($addressData as $row) {
    $addr = rowToAssoc($addressColumns, $row);

    $stmt = $pdo->prepare("
        INSERT INTO address (id, line1, line2, city, postal_code, country, lat, lng)
        VALUES (:id, :line1, :line2, :city, :postal_code, :country, :lat, :lng)
    ");

    $stmt->execute([
        'id' => $addr['id'],
        'line1' => $addr['line1'],
        'line2' => $addr['line2'],
        'city' => $addr['city'],
        'postal_code' => $addr['postal_code'],
        'country' => $addr['country'] ?? 'Côte d\'Ivoire',
        'lat' => $addr['lat'],
        'lng' => $addr['lng']
    ]);

    $addressMap[$addr['id']] = $addr['id'];
}

echo "   ✓ " . count($addressMap) . " adresses migrées\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 6 : Migrer les cabinets
// ═══════════════════════════════════════════════════════════════
echo "🏢 Migration des cabinets...\n";

$cabinetMap = []; // old_id => new_id
$managingPartnerMap = []; // cabinet_id => managing_partner_id (pour mise à jour ultérieure)

foreach ($cabinetData as $row) {
    $cab = rowToAssoc($cabinetColumns, $row);

    // Sauvegarder le managing_partner_id pour mise à jour après migration des lawyers
    if (!empty($cab['managing_partner_id'])) {
        $managingPartnerMap[$cab['id']] = $cab['managing_partner_id'];
    }

    $stmt = $pdo->prepare("
        INSERT INTO cabinet (
            id, name, slug, type, email, phone, website, description,
            logo_url, is_active, type_id, managing_partner_id, address_id,
            old_address, city, lat, lng
        ) VALUES (
            :id, :name, :slug, :type, :email, :phone, :website, :description,
            :logo_url, :is_active, :type_id, NULL, :address_id,
            :old_address, :city, :lat, :lng
        )
    ");

    // Vérifier si l'address_id existe dans les adresses migrées
    $resolvedAddressId = null;
    if (!empty($cab['address_id']) && isset($addressMap[$cab['address_id']])) {
        $resolvedAddressId = $addressMap[$cab['address_id']];
    }

    $stmt->execute([
        'id' => $cab['id'],
        'name' => $cab['name'],
        'slug' => $cab['slug'],
        'type' => $cab['type'] ?: 'Cabinet',
        'email' => $cab['email'],
        'phone' => $cab['phone'],
        'website' => $cab['website'],
        'description' => $cab['description'],
        'logo_url' => $cab['logo_url'],
        'is_active' => $cab['is_active'] ?? 1,
        'type_id' => $cab['type_id'],
        'address_id' => $resolvedAddressId,
        'old_address' => $cab['address'],
        'city' => $cab['city'],
        'lat' => $cab['lat'],
        'lng' => $cab['lng']
    ]);

    $cabinetId = $cab['id'];
    $cabinetMap[$cab['id']] = $cabinetId;

    // Créer collection email si email existe
    if (!empty($cab['email'])) {
        $pdo->prepare("
            INSERT INTO email_address (cabinet_id, label, email, is_primary, position)
            VALUES (:cabinet_id, 'Principal', :email, 1, 0)
        ")->execute([
            'cabinet_id' => $cabinetId,
            'email' => $cab['email']
        ]);
    }

    // Créer collection phone si phone existe
    if (!empty($cab['phone'])) {
        $pdo->prepare("
            INSERT INTO phone (cabinet_id, label, number, is_primary, position)
            VALUES (:cabinet_id, 'Standard', :number, 1, 0)
        ")->execute([
            'cabinet_id' => $cabinetId,
            'number' => $cab['phone']
        ]);
    }
}

echo "   ✓ " . count($cabinetMap) . " cabinets migrés\n";
echo "   ✓ Collections (emails, phones) créées\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 7 : Migrer les lawyers
// ═══════════════════════════════════════════════════════════════
echo "👔 Migration des lawyers...\n";

$lawyerMap = []; // old_id => new_id

foreach ($lawyerData as $row) {
    $law = rowToAssoc($lawyerColumns, $row);

    $stmt = $pdo->prepare("
        INSERT INTO lawyer (
            id, first_name, last_name, slug, bar_number, biography, photo_url,
            email, phone, city, cabinet_id, address_id
        ) VALUES (
            :id, :first_name, :last_name, :slug, :bar_number, :biography, :photo_url,
            :email, :phone, :city, :cabinet_id, :address_id
        )
    ");

    // Vérifier si l'address_id existe dans les adresses migrées
    $resolvedAddressId = null;
    if (!empty($law['address_id']) && isset($addressMap[$law['address_id']])) {
        $resolvedAddressId = $addressMap[$law['address_id']];
    }

    $stmt->execute([
        'id' => $law['id'],
        'first_name' => $law['first_name'],
        'last_name' => $law['last_name'],
        'slug' => $law['slug'],
        'bar_number' => $law['bar_number'],
        'biography' => $law['biography'],
        'photo_url' => $law['photo_url'],
        'email' => $law['email'],
        'phone' => $law['phone'],
        'city' => $law['city'],
        'cabinet_id' => $law['cabinet_id'],
        'address_id' => $resolvedAddressId
    ]);

    $lawyerId = $law['id'];
    $lawyerMap[$law['id']] = $lawyerId;

    // Créer collection email si email existe
    if (!empty($law['email'])) {
        $pdo->prepare("
            INSERT INTO email_address (lawyer_id, label, email, is_primary, position)
            VALUES (:lawyer_id, 'Professionnel', :email, 1, 0)
        ")->execute([
            'lawyer_id' => $lawyerId,
            'email' => $law['email']
        ]);
    }

    // Créer collection phone si phone existe
    if (!empty($law['phone'])) {
        $pdo->prepare("
            INSERT INTO phone (lawyer_id, label, number, is_primary, position)
            VALUES (:lawyer_id, 'Bureau', :number, 1, 0)
        ")->execute([
            'lawyer_id' => $lawyerId,
            'number' => $law['phone']
        ]);
    }
}

echo "   ✓ " . count($lawyerMap) . " lawyers migrés\n";
echo "   ✓ Collections (emails, phones) créées\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 8 : Mettre à jour les managing_partner_id des cabinets
// ═══════════════════════════════════════════════════════════════
echo "👔 Mise à jour des responsables de cabinets...\n";

foreach ($managingPartnerMap as $cabinetId => $managingPartnerId) {
    $pdo->prepare("
        UPDATE cabinet
        SET managing_partner_id = :managing_partner_id
        WHERE id = :cabinet_id
    ")->execute([
        'managing_partner_id' => $managingPartnerId,
        'cabinet_id' => $cabinetId
    ]);
}

echo "   ✓ " . count($managingPartnerMap) . " responsables de cabinet désignés\n\n";

// ═══════════════════════════════════════════════════════════════
// ÉTAPE 9 : Résumé
// ═══════════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ MIGRATION TERMINÉE AVEC SUCCÈS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📊 Résumé:\n";
echo "   • Adresses migrées: " . count($addressMap) . "\n";
echo "   • Cabinets migrés: " . count($cabinetMap) . "\n";
echo "   • Lawyers migrés: " . count($lawyerMap) . "\n\n";

echo "⚠️  ACTIONS SUIVANTES RECOMMANDÉES:\n";
echo "   1. Vérifier les données dans le back-office /admin\n";
echo "   2. Créer les comptes User pour les lawyers (bouton 'Créer compte')\n";
echo "   3. Tester l'API /api/lawyers et /api/cabinets\n";
echo "   4. Migrer les spécialités (lawyer_specialty) si nécessaire\n\n";

echo "🎉 Migration complétée!\n";
