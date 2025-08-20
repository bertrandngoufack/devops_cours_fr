<!DOCTYPE html>
<html>
<head>
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .highlight-row {
            background-color: #f0f8ff !important;
            border-left: 4px solid #3273dc !important;
            animation: highlightFade 2s ease-in-out;
        }
        
        @keyframes highlightFade {
            0% { background-color: #ffff99; }
            50% { background-color: #f0f8ff; }
            100% { background-color: #f0f8ff; }
        }
        
        .success-message {
            background-color: #48c774;
            color: white;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        
        .error-message {
            background-color: #f14668;
            color: white;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-users"></i> Gestion des utilisateurs
            </h1>
            
            <!-- Messages de succès/erreur -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            
            <div class="buttons">
                <a href="/admin/users/create" class="button is-primary">
                    <span class="icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span>Nouvel utilisateur</span>
                </a>
            </div>
            
            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php 
                            // Vérifier si cet utilisateur doit être surligné
                            $highlightId = $_GET['highlight'] ?? null;
                            $isHighlighted = ($highlightId && $user['id'] == $highlightId);
                            $rowClass = $isHighlighted ? 'highlight-row' : '';
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                <td><?= htmlspecialchars($user['prenom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['telephone']) ?></td>
                                <td>
                                    <span class="tag <?= $user['role'] === 'admin' ? 'is-danger' : 'is-info' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="buttons are-small">
                                        <a href="/admin/users/edit/<?= $user['id'] ?>" class="button is-warning">
                                            <span class="icon">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        </a>
                                        <a href="/admin/users/delete/<?= $user['id'] ?>" 
                                           class="button is-danger"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                            <span class="icon">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (empty($users)): ?>
                <div class="notification is-info">
                    <i class="fas fa-info-circle"></i> Aucun utilisateur trouvé.
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <script>
        // Script pour faire défiler vers l'utilisateur surligné
        document.addEventListener('DOMContentLoaded', function() {
            const highlightRow = document.querySelector('.highlight-row');
            if (highlightRow) {
                highlightRow.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Supprimer le paramètre highlight de l'URL après 3 secondes
                setTimeout(function() {
                    const url = new URL(window.location);
                    url.searchParams.delete('highlight');
                    window.history.replaceState({}, '', url);
                }, 3000);
            }
        });
    </script>
</body>
</html>
