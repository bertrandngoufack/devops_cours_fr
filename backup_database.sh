#!/bin/bash

# 🗄️ SCRIPT DE BACKUP BASE DE DONNÉES - PROJET DEVOPS COURS FR
# Auteur: bertrandngoufack@gmail.com
# Date: $(date)

echo "🗄️ BACKUP DE LA BASE DE DONNÉES POSTGRESQL"
echo "=========================================="

# Configuration
DB_HOST="100.69.65.33"
DB_PORT="15432"
DB_USER="userpostgres"
DB_PASSWORD="Bateau123"
DB_NAME="devops_cours"
BACKUP_DIR="database_backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/devops_cours_backup_${TIMESTAMP}.sql"

# Création du dossier de backup
echo "📁 Création du dossier de backup: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"

# Vérification de la connexion à la base de données
echo "🔍 Test de connexion à la base de données..."
PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "SELECT version();" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Connexion à la base de données réussie"
else
    echo "❌ Impossible de se connecter à la base de données"
    echo "   Host: $DB_HOST"
    echo "   Port: $DB_PORT"
    echo "   User: $DB_USER"
    echo "   Database: $DB_NAME"
    exit 1
fi

# Backup de la base de données
echo "📊 Création du backup de la base de données..."
echo "   Fichier: $BACKUP_FILE"

PGPASSWORD="$DB_PASSWORD" pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" \
    --verbose \
    --clean \
    --if-exists \
    --create \
    --no-owner \
    --no-privileges \
    > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "✅ Backup de la base de données réussi"
    
    # Calcul de la taille du fichier
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "📊 Taille du backup: $BACKUP_SIZE"
    
    # Création d'un fichier de métadonnées
    cat > "${BACKUP_FILE}.meta" << EOF
# 📋 MÉTADONNÉES DU BACKUP BASE DE DONNÉES
# Projet: devops_cours_fr
# Date: $(date)
# Fichier: $(basename "$BACKUP_FILE")

## 🔧 Configuration
- Host: $DB_HOST
- Port: $DB_PORT
- User: $DB_USER
- Database: $DB_NAME
- Taille: $BACKUP_SIZE

## 📊 Informations
- Type: PostgreSQL dump
- Format: SQL
- Compression: Aucune
- Inclus: Structure + Données

## 🚀 Instructions de restauration
\`\`\`bash
# Restaurer la base de données
PGPASSWORD=$DB_PASSWORD psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d postgres < $BACKUP_FILE
\`\`\`

## ⚠️ Notes importantes
- Vérifier que PostgreSQL est en cours d'exécution
- S'assurer que l'utilisateur a les droits suffisants
- Tester la restauration sur un environnement de test d'abord

---
*Backup créé le $(date)*
EOF
    
    echo "📝 Métadonnées créées: ${BACKUP_FILE}.meta"
    
    # Création d'une archive compressée
    echo "🗜️ Création de l'archive compressée..."
    gzip "$BACKUP_FILE"
    COMPRESSED_FILE="${BACKUP_FILE}.gz"
    COMPRESSED_SIZE=$(du -h "$COMPRESSED_FILE" | cut -f1)
    
    echo "✅ Archive compressée créée: $COMPRESSED_FILE"
    echo "📊 Taille compressée: $COMPRESSED_SIZE"
    
    # Nettoyage des anciens backups (garder les 5 plus récents)
    echo "🧹 Nettoyage des anciens backups..."
    ls -t "${BACKUP_DIR}/devops_cours_backup_*.sql.gz" 2>/dev/null | tail -n +6 | xargs -r rm
    
    # Rapport final
    echo ""
    echo "🎉 BACKUP DE LA BASE DE DONNÉES TERMINÉ !"
    echo "=========================================="
    echo "📁 Dossier: $BACKUP_DIR"
    echo "🗜️ Fichier: $(basename "$COMPRESSED_FILE")"
    echo "📊 Taille: $COMPRESSED_SIZE"
    echo "📝 Métadonnées: $(basename "${BACKUP_FILE}.meta")"
    echo ""
    echo "🚀 Pour restaurer:"
    echo "   gunzip $COMPRESSED_FILE"
    echo "   PGPASSWORD=$DB_PASSWORD psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d postgres < ${BACKUP_FILE}"
    
else
    echo "❌ Échec du backup de la base de données"
    exit 1
fi
