<!DOCTYPE html>
<html>
<head>
    <title>Créer un utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-8">
                    <h1 class="title">
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer un nouvel utilisateur
                    </h1>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="notification is-danger">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/users/create" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="columns">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Nom</label>
                                    <div class="control">
                                        <input class="input" type="text" name="nom" value="<?= old('nom') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Prénom</label>
                                    <div class="control">
                                        <input class="input" type="text" name="prenom" value="<?= old('prenom') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Email</label>
                                    <div class="control">
                                        <input class="input" type="email" name="email" value="<?= old('email') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Téléphone</label>
                                    <div class="control">
                                        <input class="input" type="text" name="telephone" value="<?= old('telephone') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Identifiant</label>
                                    <div class="control">
                                        <input class="input" type="text" name="username" value="<?= old('username') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Mot de passe</label>
                                    <div class="control">
                                        <input class="input" type="password" name="password" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Rôle</label>
                            <div class="control">
                                <div class="select">
                                    <select name="role" required>
                                        <option value="user" <?= old('role') === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button is-primary" type="submit">
                                    <i class="fas fa-save mr-2"></i>
                                    Créer l'utilisateur
                                </button>
                            </div>
                            <div class="control">
                                <a class="button is-light" href="/admin/users">
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
