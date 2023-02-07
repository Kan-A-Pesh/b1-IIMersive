# IIMersive

> 🖥️ **Live demo:** [iimersive.kan-a-pesh.ml](https://iimersive.kan-a-pesh.ml/)
\
> ⚠️ **Disclaimer:** Ce projet est en cours de développement. La démo en ligne contient pour l'instant uniquement la partie front-end.

## 📖 À propos

IIMersive est un projet scolaire de fin de première année à l'IIM.\
Il s'agit d'un réseau social permettant de partager des textes et des images, d'y ajouter des tags, commentaires et likes.

Le projet est réalisé avec un front-end en HTML/CSS/JS natif, sauf pour les pages de connexion et d'inscription qui sont réalisées à l'aide de Bootstrap.\
Le back-end est réalisé en PHP natif, avec une base de données MySQL.

## ⚡️ Installation

Le projet nécessite un serveur web Apache, PHP et MySQL.

### 🐋 Docker

Vous pouvez utiliser Docker pour installer le projet.\
Pour cela, il vous suffit de cloner le projet, puis de lancer la commande suivante :

```bash
docker run -d -p 5555:80 --mount type=bind,source="$(pwd)",target=/var/www/html php:apache
```

Vous pouvez ensuite accéder au projet à l'adresse `http://localhost:5555/`.

### 📦 Manuel

Vous pouvez également installer le projet manuellement.\
Pour cela, il est nécessaire d'installer un serveur web Apache et PHP et de configurer la base de données MySQL manuellement.

Les informations de connexion à la base de données sont à renseigner dans le fichier `api/config.php`.

## 📜 Documentation

[ENDPOINTS.md](api/ENDPOINTS.md) : Liste des endpoints de l'API

## 📝 Fonctionnalités

- Connexion/Inscription
- Création de posts
- Ajout de tags à la création d'un post
- Ajout de commentaires à un post
- Ajout de likes à un post
- Recherche de posts par tags, mots-clés ou utilisateurs
- Affichage des posts les plus populaires
- Affichage des comptes les plus populaires
- Bien plus à venir...

## 📂 Structure du projet

- `api/` : Contient les fichiers PHP permettant de gérer les requêtes AJAX
- `css/` : Contient les fichiers CSS
- `fonts/` : Contient les fichiers de polices
- `img/` : Contient les images
- `js/` : Contient les fichiers JS
- `pages/` : Contient les pages HTML (îles)
- `index.html` : Page d'accueil
- `login.html` : Page de connexion
- `register.html` : Page d'inscription
- `README.md` : Fichier README (ce fichier)

## 📚 Bibliothèques utilisées

- [Bootstrap](https://getbootstrap.com/) : Utilisé pour la page de connexion et d'inscription uniquement
- [Feather Icons](https://feathericons.com/) : Utilisé pour les icônes du site
- [Google Fonts](https://fonts.google.com/) : Utilisé pour les polices du site (Montserrat)

## 📝 License

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus d'informations.
