<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IIMersive</title>

    <link rel="stylesheet" href="/css/root.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/components/index.css">

    <script defer src="/js/theme.js"></script>
    <script defer src="/js/loader.js"></script>
    <script defer src="/js/navbar/navbar.js"></script>
    <script>
        const queryString = "<?php echo $_SERVER['QUERY_STRING']; ?>";
        const queryPath = queryString.split('/');
    </script>
</head>

<body>
    <nav>
        <h2><span class="color-primary">IIM</span>ersive</h2>
        <div class="input">
            <input type="text" placeholder="Recherche">
            <img class="icons" src="/img/icons/search.svg" alt="🔎">
        </div>
        <img class="icons menu" src="/img/icons/menu.svg" alt="☰">
    </nav>

    <div id="app">
        <section class="sidebar">
            <div class="profile-card">
                <img src="/img/defaults/profile_pic.png" alt="Profile picture">
                <div class="profile-info">
                    <h2>Profile-Name</h2>
                    <a href="/profile">
                        <button class="bg-primary">
                            <h3>Voir mon profil</h3>
                        </button>
                    </a>
                </div>
            </div>

            <div class="links">
                <a href="/home">
                    <div class="link">
                        <img class="icons" src="/img/icons/home.svg" alt="🏠">
                        <h2>Accueil</h2>
                    </div>
                </a>
                <a href="/notifications">
                    <div class="link">
                        <img class="icons" src="/img/icons/bell.svg" alt="🔔">
                        <h2>Notifications</h2>
                    </div>
                </a>
                <a href="/messages">
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
                <div class="link color-secondary">
                    <img src="/img/icons/log-out.svg" alt="🚪">
                    <h2>Se déconnecter</h2>
                </div>
            </div>
        </section>

        <main id="main">

        </main>
    </div>
</body>

</html>