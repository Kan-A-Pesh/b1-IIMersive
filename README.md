# IIMersive

> 🖥️ **Live demo:** [iimersive.kan-a-pesh.ml](https://iimersive.kan-a-pesh.ml/)
\
> ⚠️ **Disclaimer:** Ce projet est en cours de développement. Il peut contenir des bugs.

## 📖 À propos

IIMersive est un projet scolaire de fin de première année à l'IIM.\
Il s'agit d'un réseau social permettant de partager des textes et des images, d'y ajouter des tags, commentaires et likes.

Le projet est réalisé avec un front-end en HTML/CSS/JS natif, sauf pour les pages de connexion et d'inscription qui sont réalisées à l'aide de Bootstrap.\
Le back-end est réalisé en PHP natif, avec une base de données MySQL.

Pour plus d'informations sur l'avancement du projet, vous pouvez consulter le [Trello](https://trello.com/b/rL5aaxOq/iimersive-todo).

## ⚡️ Installation

Le projet nécessite un serveur web Apache, PHP et MySQL.

Il est possible d'installer le projet avec Docker, ce qui permet de ne pas avoir à installer et à configurer un serveur Apache et PHP.\

Cependant, il est nécessaire de configurer un serveur de base de données MySQL manuellement.\
Pour cela, il vous suffit de créer une base de données, puis d'exécuter le script SQL `api/iimersive.sql`.\
Puis, il vous suffit de renseigner les informations de connexion à la base de données dans le fichier `api/config.php`.

### 🐋 Docker

Vous pouvez utiliser Docker pour installer le projet.\
Pour cela, il vous suffit de cloner le projet, puis de lancer la commande suivante :

```bash
# Lancement de l'environnement
docker compose up -d
```

Vous pouvez ensuite accéder au projet à l'adresse `http://localhost:5555/`.

Pour stopper et supprimer le conteneur, vous pouvez utiliser les commandes suivantes :

```bash
# Arrêt du conteneur
docker compose stop 
```

### 📦 Manuel

Vous pouvez également installer le projet manuellement.\
Pour cela, il est nécessaire d'installer un serveur web Apache et PHP.\

Vous devez ensuite cloner le projet dans le dossier de votre serveur web.

## 📜 Documentation

[ENDPOINTS.md](api/ENDPOINTS.md) : Liste des endpoints de l'API\
[MEDIA.md](media/MEDIA.md) : Fonctionnement du système de médias

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

Pour plus d'informations sur le suivi du projet, vous pouvez consulter le [Trello](https://trello.com/b/rL5aaxOq/iimersive-todo).

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
