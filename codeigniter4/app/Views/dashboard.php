<!DOCTYPE html>
<html>
<head>
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar is-primary" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/dashboard">
                <i class="fas fa-tachometer-alt mr-2"></i>
                <strong>Tableau de bord</strong>
            </a>
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <span class="button is-light">
                        <i class="fas fa-user mr-2"></i>
                        <?= $username ?>
                    </span>
                    <a class="button is-danger" href="/auth/logout">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column is-8 is-offset-2">
                    <div class="card">
                        <div class="card-content">
                            <div class="content">
                                <h1 class="title">
                                    <i class="fas fa-home mr-2"></i>
                                    Bienvenue, <?= $username ?> !
                                </h1>
                                <p class="subtitle">
                                    Vous êtes connecté en tant que <strong><?= $role ?></strong>
                                </p>
                                
                                <div class="notification is-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Ceci est votre tableau de bord personnel. Vous pouvez accéder à vos fonctionnalités depuis ici.
                                </div>

                                <div class="columns">
                                    <div class="column">
                                        <div class="box has-text-centered">
                                            <i class="fas fa-user fa-3x has-text-primary mb-3"></i>
                                            <h3 class="title is-4">Profil</h3>
                                            <p>Gérez vos informations personnelles</p>
                                        </div>
                                    </div>
                                    <div class="column">
                                        <div class="box has-text-centered">
                                            <i class="fas fa-cog fa-3x has-text-info mb-3"></i>
                                            <h3 class="title is-4">Paramètres</h3>
                                            <p>Configurez vos préférences</p>
                                        </div>
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
