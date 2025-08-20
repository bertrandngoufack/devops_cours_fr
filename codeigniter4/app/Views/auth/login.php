<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <section class="hero is-primary is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-4">
                        <div class="card">
                            <div class="card-content">
                                <h1 class="title has-text-centered">Connexion</h1>
                                <form action="/auth/login" method="post">
                                    <div class="field">
                                        <label class="label">Identifiant</label>
                                        <div class="control">
                                            <input class="input" type="text" name="username" required>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Mot de passe</label>
                                        <div class="control">
                                            <input class="input" type="password" name="password" required>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="control">
                                            <button class="button is-primary is-fullwidth" type="submit">Se connecter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
