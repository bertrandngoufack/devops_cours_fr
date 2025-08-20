<!DOCTYPE html>
<html>
<head>
    <title>Connexion réussie</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <section class="hero is-success is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-6">
                        <div class="card">
                            <div class="card-content">
                                <div class="content has-text-centered">
                                    <i class="fas fa-check-circle fa-5x has-text-success mb-4"></i>
                                    <h1 class="title has-text-success"><?= $message ?></h1>
                                    <p class="subtitle">Bienvenue, <strong><?= $username ?></strong> !</p>
                                    <p class="mb-4">Vous êtes connecté en tant que <span class="tag is-info"><?= $role ?></span></p>
                                    
                                    <div class="buttons is-centered">
                                        <?php if ($role === 'admin'): ?>
                                            <a href="/admin/users" class="button is-primary">
                                                <i class="fas fa-users mr-2"></i>
                                                Gestion des utilisateurs
                                            </a>
                                        <?php else: ?>
                                            <a href="/dashboard" class="button is-info">
                                                <i class="fas fa-tachometer-alt mr-2"></i>
                                                Tableau de bord
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="/auth/logout" class="button is-danger">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Déconnexion
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
