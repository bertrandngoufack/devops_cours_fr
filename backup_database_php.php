<?php
/**
 * 🗄️ SCRIPT PHP DE BACKUP BASE DE DONNÉES POSTGRESQL
 * Projet: devops_cours_fr
 * Auteur: bertrandngoufack@gmail.com
 * Date: <?= date('Y-m-d H:i:s') ?>
 * 
 * Ce script fait le backup de la base de données PostgreSQL
 * qui tourne dans un conteneur Docker
 */

echo "🗄️ BACKUP DE LA BASE DE DONNÉES POSTGRESQL AVEC PHP\n";
echo "==================================================\n\n";

// Configuration de la base de données
$config = [
    'host' => '100.69.65.33',
    'port' => '15432',
    'user' => 'userpostgres',
    'password' => 'Bateau123',
    'database' => 'codeigniter4_users'
];

// Configuration du backup
$backupDir = 'database_backups';
$timestamp = date('Ymd_His');
$backupFile = "{$backupDir}/devops_cours_backup_{$timestamp}.sql";
$metaFile = "{$backupDir}/devops_cours_backup_{$timestamp}.meta";

// Création du dossier de backup
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "📁 Dossier de backup créé: {$backupDir}\n";
}

echo "🔍 Test de connexion à la base de données...\n";

try {
    // Test de connexion
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
    $pdo = new PDO($dsn, $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test de la version PostgreSQL
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "✅ Connexion réussie à PostgreSQL\n";
    echo "📊 Version: " . substr($version, 0, 50) . "...\n\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "   Host: {$config['host']}\n";
    echo "   Port: {$config['port']}\n";
    echo "   User: {$config['user']}\n";
    echo "   Database: {$config['database']}\n";
    exit(1);
}

echo "📊 Création du backup de la base de données...\n";
echo "   Fichier: {$backupFile}\n\n";

try {
    // Récupération de la liste des tables
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
    ");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Tables trouvées: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "   - {$table}\n";
    }
    echo "\n";
    
    // Début du fichier de backup
    $backupContent = "-- 🗄️ BACKUP BASE DE DONNÉES POSTGRESQL\n";
    $backupContent .= "-- Projet: devops_cours_fr\n";
    $backupContent .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
    $backupContent .= "-- Base: {$config['database']}\n";
    $backupContent .= "-- Host: {$config['host']}:{$config['port']}\n\n";
    
    // Backup de chaque table
    foreach ($tables as $table) {
        echo "📦 Backup de la table: {$table}\n";
        
        // Structure de la table
        $stmt = $pdo->query("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = '{$table}' 
            ORDER BY ordinal_position
        ");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $backupContent .= "\n-- Structure de la table {$table}\n";
        $backupContent .= "DROP TABLE IF EXISTS {$table} CASCADE;\n";
        
        // Création de la table (approche manuelle)
        $backupContent .= "CREATE TABLE {$table} (\n";
        $columnDefs = [];
        foreach ($columns as $column) {
            $def = "    {$column['column_name']} {$column['data_type']}";
            if ($column['is_nullable'] === 'NO') {
                $def .= " NOT NULL";
            }
            if ($column['column_default'] !== null) {
                $def .= " DEFAULT {$column['column_default']}";
            }
            $columnDefs[] = $def;
        }
        $backupContent .= implode(",\n", $columnDefs) . "\n);\n\n";
        
        // Données de la table
        $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "   📊 {$count} enregistrements à sauvegarder\n";
            
            $backupContent .= "-- Données de la table {$table}\n";
            $backupContent .= "INSERT INTO {$table} VALUES\n";
            
            $stmt = $pdo->query("SELECT * FROM {$table}");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $insertValues = [];
            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        // Échapper les caractères spéciaux
                        $value = str_replace("'", "''", $value);
                        $values[] = "'{$value}'";
                    }
                }
                $insertValues[] = "(" . implode(', ', $values) . ")";
            }
            
            $backupContent .= implode(",\n", $insertValues) . ";\n\n";
        } else {
            echo "   📊 Table vide\n";
            $backupContent .= "-- Table {$table} vide\n\n";
        }
    }
    
    // Sauvegarde du fichier
    file_put_contents($backupFile, $backupContent);
    
    // Calcul de la taille
    $backupSize = filesize($backupFile);
    $backupSizeFormatted = formatBytes($backupSize);
    
    echo "✅ Backup de la base de données réussi\n";
    echo "📊 Taille du backup: {$backupSizeFormatted}\n";
    
    // Création du fichier de métadonnées
    $metaContent = "# 📋 MÉTADONNÉES DU BACKUP BASE DE DONNÉES\n";
    $metaContent .= "# Projet: devops_cours_fr\n";
    $metaContent .= "# Date: " . date('Y-m-d H:i:s') . "\n";
    $metaContent .= "# Fichier: " . basename($backupFile) . "\n\n";
    
    $metaContent .= "## 🔧 Configuration\n";
    $metaContent .= "- Host: {$config['host']}\n";
    $metaContent .= "- Port: {$config['port']}\n";
    $metaContent .= "- User: {$config['user']}\n";
    $metaContent .= "- Database: {$config['database']}\n";
    $metaContent .= "- Taille: {$backupSizeFormatted}\n\n";
    
    $metaContent .= "## 📊 Informations\n";
    $metaContent .= "- Type: PostgreSQL dump\n";
    $metaContent .= "- Format: SQL\n";
    $metaContent .= "- Compression: Aucune\n";
    $metaContent .= "- Inclus: Structure + Données\n";
    $metaContent .= "- Tables: " . count($tables) . "\n\n";
    
    $metaContent .= "## 🚀 Instructions de restauration\n";
    $metaContent .= "```bash\n";
    $metaContent .= "# Restaurer la base de données\n";
    $metaContent .= "PGPASSWORD={$config['password']} psql -h {$config['host']} -p {$config['port']} -U {$config['user']} -d postgres < {$backupFile}\n";
    $metaContent .= "```\n\n";
    
    $metaContent .= "## ⚠️ Notes importantes\n";
    $metaContent .= "- Vérifier que PostgreSQL est en cours d'exécution\n";
    $metaContent .= "- S'assurer que l'utilisateur a les droits suffisants\n";
    $metaContent .= "- Tester la restauration sur un environnement de test d'abord\n\n";
    
    $metaContent .= "---\n";
    $metaContent .= "*Backup créé le " . date('Y-m-d H:i:s') . "*\n";
    
    file_put_contents($metaFile, $metaContent);
    echo "📝 Métadonnées créées: " . basename($metaFile) . "\n";
    
    // Création d'une archive compressée
    echo "🗜️ Création de l'archive compressée...\n";
    $compressedFile = $backupFile . '.gz';
    $backupContent = file_get_contents($backupFile);
    file_put_contents($compressedFile, gzencode($backupContent, 9));
    
    $compressedSize = filesize($compressedFile);
    $compressedSizeFormatted = formatBytes($compressedSize);
    
    echo "✅ Archive compressée créée: " . basename($compressedFile) . "\n";
    echo "📊 Taille compressée: {$compressedSizeFormatted}\n";
    
    // Nettoyage des anciens backups (garder les 5 plus récents)
    echo "🧹 Nettoyage des anciens backups...\n";
    $oldBackups = glob("{$backupDir}/devops_cours_backup_*.sql.gz");
    if (count($oldBackups) > 5) {
        // Trier par date de modification (plus ancien en premier)
        usort($oldBackups, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        
        // Supprimer les plus anciens
        $toDelete = array_slice($oldBackups, 0, count($oldBackups) - 5);
        foreach ($toDelete as $file) {
            unlink($file);
            echo "   🗑️ Supprimé: " . basename($file) . "\n";
        }
    }
    
    // Rapport final
    echo "\n🎉 BACKUP DE LA BASE DE DONNÉES TERMINÉ !\n";
    echo "==========================================\n";
    echo "📁 Dossier: {$backupDir}\n";
    echo "🗜️ Fichier: " . basename($compressedFile) . "\n";
    echo "📊 Taille: {$compressedSizeFormatted}\n";
    echo "📝 Métadonnées: " . basename($metaFile) . "\n";
    echo "📋 Tables sauvegardées: " . count($tables) . "\n";
    echo "\n🚀 Pour restaurer:\n";
    echo "   gunzip {$compressedFile}\n";
    echo "   PGPASSWORD={$config['password']} psql -h {$config['host']} -p {$config['port']} -U {$config['user']} -d postgres < {$backupFile}\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du backup: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Fonction pour formater la taille en bytes
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
