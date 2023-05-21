<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IIMersive</title>

    <meta name="description" content="IIMersive est un projet scolaire de fin de première année à l'IIM. Il s'agit d'un réseau social permettant de partager des textes et des images, d'y ajouter des tags, commentaires et likes.">
    <link rel="icon" href="/img/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="/img/favicon.png">

    <link rel="stylesheet" href="/css/root.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/components/index.css">

    <script src="https://cdn.socket.io/4.6.0/socket.io.min.js" integrity="sha384-c79GN5VsunZvi+Q/WObgk2in0CbZsHnjEqvFxC5DxHn9lTfNce2WW6h2pH6u/kF+" crossorigin="anonymous"></script>

    <script src="/js/api.js"></script>
    <script src="/js/socket.js"></script>
    <script src="/js/components/postLoader.js"></script>

    <script defer src="/js/theme.js"></script>
    <script defer src="/js/loader.js"></script>
    <script defer src="/js/navbar/navbar.js"></script>
    <script>
        const queryString = "<?php echo htmlspecialchars($_SERVER['QUERY_STRING']); ?>";
    </script>
</head>

<body>
    <header>
        <h2><span class="color-primary">IIM</span>ersive</h2>
        <div class="input">
            <input type="text" placeholder="Recherche" id="search-input">
            <img class="icons" src="/img/icons/search.svg" alt="🔎" id="search-submit">
        </div>
        <img class="icons menu" src="/img/icons/menu.svg" alt="☰">
    </header>

    <div id="app">
        <aside class="sidebar">
            <div class="profile-card anon-hidden">
                <img src="/img/placeholders/loading.gif" alt="Profile picture">
                <div class="profile-info">
                    <h2><span class="opacity-3">Chargement...</span></h2>
                    <a href="/profile">
                        <button class="bg-primary">
                            <h3>Mon profil</h3>
                        </button>
                    </a>
                </div>
            </div>

            <div class="login-card log-hidden">
                <p>Connectez-vous dès maintenant pour profiter de toutes les fonctionnalités de IIMersive !</p>
            </div>

            <div class="links">
                <a href="/login" class="log-hidden ext">
                    <div class="link">
                        <img class="icons" src="/img/icons/log-in.svg" alt="🏠">
                        <h2>Se connecter</h2>
                    </div>
                </a>
                <a href="/register" class="log-hidden ext">
                    <div class="link">
                        <img class="icons" src="/img/icons/star.svg" alt="🏠">
                        <h2>S'inscrire</h2>
                    </div>
                </a>
                <a href="/home">
                    <div class="link">
                        <img class="icons" src="/img/icons/home.svg" alt="🏠">
                        <h2>Accueil</h2>
                    </div>
                </a>
                <a href="/notifications" class="anon-hidden">
                    <div class="link">
                        <img class="icons" src="/img/icons/bell.svg" alt="🔔">
                        <h2>Notifications</h2>
                    </div>
                </a>
                <a href="/messages" class="anon-hidden">
                    <div class="link">
                        <img class="icons" src="/img/icons/message-circle.svg" alt="💬">
                        <h2>Messages</h2>
                    </div>
                </a>
                <a href="/settings">
                    <div class="link">
                        <img class="icons" src="/img/icons/settings.svg" alt="⚙️">
                        <h2>Paramètres</h2>
                    </div>
                </a>
                <div class="link color-secondary anon-hidden" id="logout-link">
                    <img src="/img/icons/log-out.svg" alt="🚪">
                    <h2>Se déconnecter</h2>
                </div>
            </div>
        </aside>

        <main id="main">

        </main>
    </div>
</body>

</html>