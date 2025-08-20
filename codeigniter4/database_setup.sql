-- Script de création de la base de données et des utilisateurs
-- Connexion à PostgreSQL
\c postgres;

-- Création de la base de données
CREATE DATABASE codeigniter4_users;

-- Connexion à la nouvelle base de données
\c codeigniter4_users;

-- Création de la table utilisateurs
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de l'utilisateur admin
INSERT INTO users (nom, prenom, email, telephone, username, password, role) VALUES
('Admin', 'System', 'admin@example.com', '0123456789', 'admin', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Création de 10 utilisateurs de test
INSERT INTO users (nom, prenom, email, telephone, username, password, role) VALUES
('Dupont', 'Jean', 'jean.dupont@email.com', '0123456781', 'jean.dupont', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Martin', 'Marie', 'marie.martin@email.com', '0123456782', 'marie.martin', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Bernard', 'Pierre', 'pierre.bernard@email.com', '0123456783', 'pierre.bernard', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Petit', 'Sophie', 'sophie.petit@email.com', '0123456784', 'sophie.petit', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Robert', 'Lucas', 'lucas.robert@email.com', '0123456785', 'lucas.robert', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Richard', 'Emma', 'emma.richard@email.com', '0123456786', 'emma.richard', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Durand', 'Thomas', 'thomas.durand@email.com', '0123456787', 'thomas.durand', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Moreau', 'Julie', 'julie.moreau@email.com', '0123456788', 'julie.moreau', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Simon', 'Alexandre', 'alexandre.simon@email.com', '0123456790', 'alexandre.simon', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Michel', 'Camille', 'camille.michel@email.com', '0123456791', 'camille.michel', 'yIXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Affichage des utilisateurs créés
SELECT id, nom, prenom, email, username, role FROM users ORDER BY id;
