<?php
/**
 * 🗄️ SCRIPT PHP D'INITIALISATION BASE DE DONNÉES POSTGRESQL
 * Projet: devops_cours_fr
 * Auteur: bertrandngoufack@gmail.com
 * Date: <?= date('Y-m-d H:i:s') ?>
 * 
 * Ce script crée la base de données et les tables
 * pour le projet devops_cours_fr
 */

echo "🗄️ INITIALISATION DE LA BASE DE DONNÉES POSTGRESQL\n";
echo "==================================================\n\n";

// Configuration de la base de données
$config = [
    'host' => '100.69.65.33',
    'port' => '15432',
    'user' => 'userpostgres',
    'password' => 'Bateau123',
    'database' => 'codeigniter4_users'
];

echo "🔍 Test de connexion à PostgreSQL...\n";

try {
    // Connexion à PostgreSQL (sans spécifier de base de données)
    $dsn = "pgsql:host={$config['host']};port={$config['port']}";
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
    exit(1);
}

// Vérification si la base de données existe
echo "🔍 Vérification de l'existence de la base de données...\n";
$stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '{$config['database']}'");
$dbExists = $stmt->fetch();

if (!$dbExists) {
    echo "📊 Création de la base de données '{$config['database']}'...\n";
    
    try {
        $pdo->exec("CREATE DATABASE {$config['database']}");
        echo "✅ Base de données créée avec succès\n\n";
    } catch (PDOException $e) {
        echo "❌ Erreur lors de la création de la base de données: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "✅ Base de données '{$config['database']}' existe déjà\n\n";
}

// Connexion à la base de données spécifique
echo "🔗 Connexion à la base de données '{$config['database']}'...\n";
try {
    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
    $pdo = new PDO($dsn, $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion à la base de données: " . $e->getMessage() . "\n";
    exit(1);
}

// Création des tables
echo "📋 Création des tables...\n\n";

// Table users
echo "📦 Création de la table 'users'...\n";
$createUsersTable = "
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

try {
    $pdo->exec($createUsersTable);
    echo "✅ Table 'users' créée avec succès\n";
} catch (PDOException $e) {
    echo "❌ Erreur lors de la création de la table 'users': " . $e->getMessage() . "\n";
}

// Table sessions (pour CodeIgniter)
echo "📦 Création de la table 'ci_sessions'...\n";
$createSessionsTable = "
CREATE TABLE IF NOT EXISTS ci_sessions (
    id VARCHAR(128) PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data TEXT
);
";

try {
    $pdo->exec($createSessionsTable);
    echo "✅ Table 'ci_sessions' créée avec succès\n";
} catch (PDOException $e) {
    echo "❌ Erreur lors de la création de la table 'ci_sessions': " . $e->getMessage() . "\n";
}

echo "\n📊 Insertion des données de test...\n";

// Insertion de l'utilisateur admin
echo "👤 Création de l'utilisateur admin...\n";
$adminPassword = password_hash('password', PASSWORD_DEFAULT);
$insertAdmin = "
INSERT INTO users (username, email, password, nom, prenom, telephone, role) 
VALUES ('admin', 'admin@devops.fr', :password, 'Administrateur', 'Système', '+33123456789', 'admin')
ON CONFLICT (username) DO NOTHING;
";

try {
    $stmt = $pdo->prepare($insertAdmin);
    $stmt->execute(['password' => $adminPassword]);
    echo "✅ Utilisateur admin créé avec succès\n";
} catch (PDOException $e) {
    echo "❌ Erreur lors de la création de l'admin: " . $e->getMessage() . "\n";
}

// Insertion d'utilisateurs de test
echo "👥 Création d'utilisateurs de test...\n";
$testUsers = [
    ['john.doe', 'john.doe@example.com', 'Doe', 'John', '+33123456790'],
    ['jane.smith', 'jane.smith@example.com', 'Smith', 'Jane', '+33123456791'],
    ['bob.wilson', 'bob.wilson@example.com', 'Wilson', 'Bob', '+33123456792'],
    ['alice.brown', 'alice.brown@example.com', 'Brown', 'Alice', '+33123456793'],
    ['charlie.davis', 'charlie.davis@example.com', 'Davis', 'Charlie', '+33123456794'],
    ['diana.miller', 'diana.miller@example.com', 'Miller', 'Diana', '+33123456795'],
    ['edward.garcia', 'edward.garcia@example.com', 'Garcia', 'Edward', '+33123456796'],
    ['fiona.rodriguez', 'fiona.rodriguez@example.com', 'Rodriguez', 'Fiona', '+33123456797'],
    ['george.martinez', 'george.martinez@example.com', 'Martinez', 'George', '+33123456798'],
    ['helen.anderson', 'helen.anderson@example.com', 'Anderson', 'Helen', '+33123456799']
];

$insertUser = "
INSERT INTO users (username, email, password, nom, prenom, telephone, role) 
VALUES (:username, :email, :password, :nom, :prenom, :telephone, 'user')
ON CONFLICT (username) DO NOTHING;
";

$testPassword = password_hash('password123', PASSWORD_DEFAULT);
$insertedCount = 0;

foreach ($testUsers as $user) {
    try {
        $stmt = $pdo->prepare($insertUser);
        $stmt->execute([
            'username' => $user[0],
            'email' => $user[1],
            'password' => $testPassword,
            'nom' => $user[2],
            'prenom' => $user[3],
            'telephone' => $user[4]
        ]);
        $insertedCount++;
    } catch (PDOException $e) {
        echo "⚠️ Erreur lors de la création de l'utilisateur {$user[0]}: " . $e->getMessage() . "\n";
    }
}

echo "✅ {$insertedCount} utilisateurs de test créés\n";

// Vérification finale
echo "\n🔍 Vérification finale...\n";
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn();
echo "📊 Nombre total d'utilisateurs: {$userCount}\n";

$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "📋 Tables créées: " . implode(', ', $tables) . "\n";

echo "\n🎉 INITIALISATION DE LA BASE DE DONNÉES TERMINÉE !\n";
echo "==================================================\n";
echo "✅ Base de données: {$config['database']}\n";
echo "✅ Tables créées: " . count($tables) . "\n";
echo "✅ Utilisateurs créés: {$userCount}\n";
echo "✅ Admin: admin / password\n";
echo "✅ Utilisateurs de test: password123\n";
echo "\n🚀 L'application est prête à être utilisée !\n";
echo "🌐 URL: http://100.69.65.33:8081\n";
?>
