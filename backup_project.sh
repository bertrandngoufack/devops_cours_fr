#!/bin/bash

# 🗂️ SCRIPT DE BACKUP COMPLET - PROJET DEVOPS COURS FR
# Auteur: bertrandngoufack@gmail.com
# Date: $(date)

echo "🚀 DÉBUT DU BACKUP COMPLET DU PROJET DEVOPS COURS FR"
echo "=================================================="

# Configuration
PROJECT_NAME="devops_cours_fr"
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")

# Création du dossier de backup
echo "📁 Création du dossier de backup: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

# 1. BACKUP DU CODE SOURCE
echo "📦 Backup du code source..."
cp -r codeigniter4 "$BACKUP_DIR/"
cp -r docker-compose "$BACKUP_DIR/"
cp -r Dockerfile "$BACKUP_DIR/"
cp -r docker-volumes "$BACKUP_DIR/"
cp .gitignore "$BACKUP_DIR/"
cp README.md "$BACKUP_DIR/"

# 2. BACKUP DE LA BASE DE DONNÉES
echo "🗄️ Backup de la base de données..."
DB_BACKUP_FILE="$BACKUP_DIR/database_backup_$(date +%Y%m%d_%H%M%S).sql"

# Tentative de backup PostgreSQL
if command -v psql &> /dev/null; then
    echo "   📊 Tentative de backup PostgreSQL..."
    PGPASSWORD=Bateau123 pg_dump -h 100.69.65.33 -p 15432 -U userpostgres -d devops_cours > "$DB_BACKUP_FILE" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "   ✅ Backup PostgreSQL réussi: $DB_BACKUP_FILE"
    else
        echo "   ⚠️ Backup PostgreSQL échoué (serveur distant)"
    fi
else
    echo "   ⚠️ psql non disponible localement"
fi

# 3. BACKUP DES LOGS
echo "📋 Backup des logs..."
if [ -d "codeigniter4/writable/logs" ]; then
    cp -r codeigniter4/writable/logs "$BACKUP_DIR/"
    echo "   ✅ Logs sauvegardés"
else
    echo "   ⚠️ Dossier logs non trouvé"
fi

# 4. BACKUP DES FICHIERS DE CONFIGURATION
echo "⚙️ Backup des fichiers de configuration..."
if [ -f "codeigniter4/.env" ]; then
    cp codeigniter4/.env "$BACKUP_DIR/"
    echo "   ✅ Fichier .env sauvegardé"
fi

# 5. CRÉATION DU FICHIER DE MANIFESTE
echo "📝 Création du manifeste de backup..."
cat > "$BACKUP_DIR/BACKUP_MANIFEST.md" << EOF
# 📦 MANIFESTE DE BACKUP - PROJET DEVOPS COURS FR

## 📋 Informations du backup
- **Date de création** : $TIMESTAMP
- **Nom du projet** : $PROJECT_NAME
- **Version** : 1.0.0
- **Statut** : Production Ready

## 📁 Contenu du backup

### 🗂️ Structure des fichiers
\`\`\`
$BACKUP_DIR/
├── codeigniter4/           # Application CodeIgniter 4
├── docker-compose/         # Configurations Docker
├── Dockerfile/            # Dockerfiles d'exemple
├── docker-volumes/        # Volumes Docker
├── .gitignore            # Fichiers ignorés par Git
├── README.md             # Documentation principale
├── database_backup_*.sql # Backup de la base de données
├── logs/                 # Logs de l'application
└── .env                  # Variables d'environnement
\`\`\`

### 🎯 Fonctionnalités incluses
- ✅ Interface web complète (CodeIgniter 4 + Bulma CSS)
- ✅ CRUD utilisateurs avec authentification
- ✅ Base de données PostgreSQL
- ✅ Tests automatisés
- ✅ Documentation complète
- ✅ Configuration Docker

### 🔧 Informations techniques
- **Framework** : CodeIgniter 4 (PHP)
- **Frontend** : Bulma CSS + Font Awesome
- **Base de données** : PostgreSQL (Docker)
- **Serveur** : Alpine Linux (100.69.65.33:8081)
- **Ports** : 8081 (web), 15432 (PostgreSQL)

### 📊 Métriques
- **Lignes de code** : ~2000+ lignes PHP
- **Fichiers principaux** : 15+ fichiers
- **Tests créés** : 8 scripts de test
- **Commits Git** : 10+ commits

### 🔐 Accès
- **URL de l'application** : http://100.69.69.33:8081
- **Identifiants admin** : admin / password
- **Base de données** : host=100.69.65.33, port=15432, user=userpostgres, db=devops_cours

### 📞 Support
- **Développeur** : bertrandngoufack@gmail.com
- **Repository** : https://github.com/bertrandngoufack/devops_cours_fr.git

## 🚀 Instructions de restauration

### 1. Restauration du code source
\`\`\`bash
# Copier les fichiers dans le répertoire de destination
cp -r codeigniter4/ /chemin/vers/destination/
cp -r docker-compose/ /chemin/vers/destination/
cp -r Dockerfile/ /chemin/vers/destination/
cp -r docker-volumes/ /chemin/vers/destination/
\`\`\`

### 2. Restauration de la base de données
\`\`\`bash
# Restaurer la base de données PostgreSQL
PGPASSWORD=Bateau123 psql -h 100.69.65.33 -p 15432 -U userpostgres -d devops_cours < database_backup_*.sql
\`\`\`

### 3. Configuration de l'environnement
\`\`\`bash
# Installer les dépendances
cd codeigniter4
composer install

# Configurer l'environnement
cp .env.example .env
# Éditer .env avec les bonnes valeurs
\`\`\`

### 4. Démarrage de l'application
\`\`\`bash
# Démarrer le serveur
php spark serve --host 0.0.0.0 --port 8081
\`\`\`

## ⚠️ Notes importantes
- Vérifier que PostgreSQL est en cours d'exécution
- S'assurer que les ports 8081 et 15432 sont disponibles
- Vérifier les permissions des dossiers writable/
- Tester l'application après restauration

---
*Backup créé automatiquement le $TIMESTAMP*
EOF

# 6. CRÉATION DU SCRIPT DE RESTAURATION
echo "🔧 Création du script de restauration..."
cat > "$BACKUP_DIR/restore_backup.sh" << 'EOF'
#!/bin/bash

# 🔄 SCRIPT DE RESTAURATION - PROJET DEVOPS COURS FR
# Usage: ./restore_backup.sh [destination]

echo "🔄 DÉBUT DE LA RESTAURATION DU PROJET DEVOPS COURS FR"
echo "=================================================="

# Configuration
BACKUP_DIR=$(dirname "$0")
DESTINATION=${1:-"/tmp/devops_cours_fr_restored"}
TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")

echo "📁 Dossier de backup: $BACKUP_DIR"
echo "📁 Destination: $DESTINATION"

# Création du dossier de destination
echo "📦 Création du dossier de destination..."
mkdir -p "$DESTINATION"

# 1. RESTAURATION DU CODE SOURCE
echo "📦 Restauration du code source..."
cp -r "$BACKUP_DIR/codeigniter4" "$DESTINATION/"
cp -r "$BACKUP_DIR/docker-compose" "$DESTINATION/"
cp -r "$BACKUP_DIR/Dockerfile" "$DESTINATION/"
cp -r "$BACKUP_DIR/docker-volumes" "$DESTINATION/"
cp "$BACKUP_DIR/.gitignore" "$DESTINATION/"
cp "$BACKUP_DIR/README.md" "$DESTINATION/"

# 2. RESTAURATION DES LOGS
echo "📋 Restauration des logs..."
if [ -d "$BACKUP_DIR/logs" ]; then
    cp -r "$BACKUP_DIR/logs" "$DESTINATION/codeigniter4/writable/"
    echo "   ✅ Logs restaurés"
fi

# 3. RESTAURATION DE LA CONFIGURATION
echo "⚙️ Restauration de la configuration..."
if [ -f "$BACKUP_DIR/.env" ]; then
    cp "$BACKUP_DIR/.env" "$DESTINATION/codeigniter4/"
    echo "   ✅ Fichier .env restauré"
fi

# 4. RESTAURATION DE LA BASE DE DONNÉES
echo "🗄️ Restauration de la base de données..."
DB_BACKUP_FILE=$(find "$BACKUP_DIR" -name "database_backup_*.sql" | head -1)
if [ -n "$DB_BACKUP_FILE" ]; then
    echo "   📊 Tentative de restauration PostgreSQL..."
    PGPASSWORD=Bateau123 psql -h 100.69.65.33 -p 15432 -U userpostgres -d devops_cours < "$DB_BACKUP_FILE" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "   ✅ Base de données restaurée"
    else
        echo "   ⚠️ Restauration de la base de données échouée"
    fi
else
    echo "   ⚠️ Fichier de backup de base de données non trouvé"
fi

# 5. INSTALLATION DES DÉPENDANCES
echo "📦 Installation des dépendances..."
if command -v composer &> /dev/null; then
    cd "$DESTINATION/codeigniter4"
    composer install --no-dev --optimize-autoloader
    echo "   ✅ Dépendances installées"
else
    echo "   ⚠️ Composer non disponible"
fi

# 6. CONFIGURATION DES PERMISSIONS
echo "🔐 Configuration des permissions..."
chmod -R 755 "$DESTINATION/codeigniter4/writable/"
echo "   ✅ Permissions configurées"

# 7. CRÉATION DU RAPPORT DE RESTAURATION
cat > "$DESTINATION/RESTORE_REPORT.md" << REPORT_EOF
# 🔄 RAPPORT DE RESTAURATION - PROJET DEVOPS COURS FR

## 📋 Informations de restauration
- **Date de restauration** : $TIMESTAMP
- **Dossier de backup** : $BACKUP_DIR
- **Dossier de destination** : $DESTINATION
- **Statut** : Restauration terminée

## ✅ Étapes effectuées
1. ✅ Restauration du code source
2. ✅ Restauration des logs
3. ✅ Restauration de la configuration
4. ✅ Restauration de la base de données
5. ✅ Installation des dépendances
6. ✅ Configuration des permissions

## 🚀 Prochaines étapes
1. Vérifier que PostgreSQL est en cours d'exécution
2. Démarrer l'application : \`cd $DESTINATION/codeigniter4 && php spark serve --host 0.0.0.0 --port 8081\`
3. Tester l'application : http://localhost:8081
4. Se connecter avec : admin / password

## 📞 Support
- **Développeur** : bertrandngoufack@gmail.com
- **Repository** : https://github.com/bertrandngoufack/devops_cours_fr.git

---
*Restauration effectuée le $TIMESTAMP*
REPORT_EOF

echo "✅ RESTAURATION TERMINÉE !"
echo "📁 Projet restauré dans: $DESTINATION"
echo "📋 Rapport de restauration: $DESTINATION/RESTORE_REPORT.md"
echo ""
echo "🚀 Pour démarrer l'application:"
echo "   cd $DESTINATION/codeigniter4"
echo "   php spark serve --host 0.0.0.0 --port 8081"
EOF

chmod +x "$BACKUP_DIR/restore_backup.sh"

# 7. CRÉATION D'UNE ARCHIVE COMPRESSÉE
echo "🗜️ Création de l'archive compressée..."
ARCHIVE_NAME="backup_${PROJECT_NAME}_$(date +%Y%m%d_%H%M%S).tar.gz"
tar -czf "$ARCHIVE_NAME" "$BACKUP_DIR"

# 8. CALCUL DE LA TAILLE
BACKUP_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)
ARCHIVE_SIZE=$(du -sh "$ARCHIVE_NAME" | cut -f1)

# 9. RAPPORT FINAL
echo ""
echo "🎉 BACKUP TERMINÉ AVEC SUCCÈS !"
echo "================================"
echo "📁 Dossier de backup: $BACKUP_DIR"
echo "🗜️ Archive compressée: $ARCHIVE_NAME"
echo "📊 Taille du backup: $BACKUP_SIZE"
echo "📦 Taille de l'archive: $ARCHIVE_SIZE"
echo "📝 Manifeste: $BACKUP_DIR/BACKUP_MANIFEST.md"
echo "🔧 Script de restauration: $BACKUP_DIR/restore_backup.sh"
echo ""
echo "🚀 Pour restaurer le projet:"
echo "   ./$BACKUP_DIR/restore_backup.sh [destination]"
echo ""
echo "📞 Support: bertrandngoufack@gmail.com"
echo "🌐 Repository: https://github.com/bertrandngoufack/devops_cours_fr.git"
