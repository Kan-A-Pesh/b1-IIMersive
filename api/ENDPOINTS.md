# Endpoints

Les endpoints sont des chemins d'accès à des ressources.
Chaque endpoints renvoie un objet JSON sous la forme suivante:

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": {
        "handle": "johndoe",
        "name": "John Doe",
        "email": "john.doe@domain.com"
    }
}
```

Ou, dans le cas d'une erreur:

```json
{
    "success": false,
    "code": 404,
    "message": "Not Found",
    "payload": null
}
```

## Ressources

Les ressources (images, médias, vidéos etc..) sont accessibles via le chemin `/media/<id>.<extension>`.

Les identifiants des ressources sont des chaînes de caractères aléatoires de **16 caractères**,
générées par le serveur lors de l'upload de la ressource.

Les extensions dépendent du type de ressource envoyée, pour un maximum de **4 caractères**.
Actuellement, seules les extensions suivantes sont autorisées:

- `.png`
- `.jpg`
- `.jpeg`
- `.gif`
- `.mp4`
- `.webm`
- `.ogg`
- `.mp3`
- `.wav`

Les ressources envoyées ne doivent pas dépasser **2 Mo** (**8Mo** pour les vidéos) à l'upload.
Une fois le fichier uploadé, il est redimensionné si nécessaire, compressé et nettoyé pour effacer les métadonnées.

A la récuperation d'un ressource, le serveur renvoie un texte de 20 caractères,
les 16 premiers étant l'identifiant de la ressource, et les 4 derniers étant l'extension de la ressource.
*Exemple:* `87fdd8aa3aeafd7c png` (identifiant: `87fdd8aa3aeafd7c`, extension: `.png`).

## Status

### GET /api/

Récupère le statut du serveur.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

Aucune réponse n'est renvoyée, le code HTTP est 204.

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 500 | Internal Server Error | Une erreur interne est survenue |

## Authentification

### GET /api/auth

Récupère les informations de la session de l'utilisateur.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": {
        "session_id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
        "expires": "2019-01-01T00:00:00.000Z",
        "user_handle": "johndoe"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 500 | Internal Server Error | Une erreur interne est survenue |

### POST /api/auth

Authentifie un utilisateur.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| handle | string | oui | L'handle de l'utilisateur |
| password | string | oui | Le mot de passe de l'utilisateur |

#### Réponse

```json
{
    "success": true,
    "code": 201,
    "message": "Created",
    "payload": {
        "session_id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
        "expires": "2019-01-01T00:00:00.000Z",
        "user_handle": "johndoe"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide ou l'utilisateur est déjà connecté |
| 401 | Unauthorized | L'handle d'utilisateur et/ou le mot de passe sont incorrects |
| 500 | Internal Server Error | Une erreur interne est survenue |

### PUT /api/auth

Renouvelle la session d'un utilisateur.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": {
        "session_id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
        "expires": "2019-01-01T00:00:00.000Z",
        "user_handle": "johndoe"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 500 | Internal Server Error | Une erreur interne est survenue |

### DELETE /api/auth

Déconnecte un utilisateur.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 204,
    "message": "No Content",
    "payload": null
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 500 | Internal Server Error | Une erreur interne est survenue |

## Utilisateurs

### GET /api/users

Récupère la liste des utilisateurs.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| query | string | non | La chaîne de caractères à rechercher dans les noms et les adresses email |
| limit | integer | non | Le nombre d'utilisateurs à récupérer (max. 25) |
| offset | integer | non | Le nombre d'utilisateurs à ignorer |

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": [
        {
            "handle": "johndoe",
            "name": "John Doe",
            "avatar_path": "4b4a523dc1e1a12b png"
        },
        {
            "handle": "janedoe",
            "name": "Jane Doe",
            "avatar_path": "4b4a523d4851a12bjpeg"
        }
    ]
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 500 | Internal Server Error | Une erreur interne est survenue |

### GET /api/users/:handle

Récupère les informations d'un utilisateur.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": {
        "handle": "johndoe",
        "name": "John Doe",
        "biography": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        "avatar_path": "4b4a523dc1e1a12b png",
        "banner_path": "4b4a5ab2b851a128jpeg",
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | L'utilisateur n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### POST /api/users

Crée un nouvel utilisateur.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| handle | string | oui | Le nom d'utilisateur de l'utilisateur |
| name | string | non | Le nom de l'utilisateur |
| email | string | oui | L'adresse email de l'utilisateur |
| password | string | oui | Le mot de passe de l'utilisateur |

#### Réponse

```json
{
    "success": true,
    "code": 201,
    "message": "Created",
    "payload": {
        "handle": "johndoe",
        "email": "john.doe@mail.com"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 409 | Conflict | Le nom d'utilisateur est déjà utilisé |
| 500 | Internal Server Error | Une erreur interne est survenue |

### PUT /api/users/:handle

Modifie les informations d'un utilisateur.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| email | string | non* | L'adresse email de l'utilisateur |
| name | string | non* | Le nom de l'utilisateur |
| biography | string | non* | La biographie de l'utilisateur |
| avatar | string | non* | L'URL de l'avatar de l'utilisateur |
| banner | string | non* | L'URL de la bannière de l'utilisateur |

*Un des paramètres est obligatoire.

#### Réponse

```json
{
    "success": true,
    "code": 204,
    "message": "No Content",
    "payload": null
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 403 | Forbidden | L'utilisateur n'a pas les droits d'accès |
| 404 | Not Found | L'utilisateur n'existe pas |
| 409 | Conflict | L'adresse email ou le nom d'utilisateur est déjà utilisé |
| 429 | Too Many Requests | Le nombre de requêtes a été dépassé |
| 500 | Internal Server Error | Une erreur interne est survenue |

## Messages

### GET /api/messages/latest

Récupère la liste des utilisateurs avec qui l'utilisateur a échangé.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| limit | integer | non | Le nombre de messages à récupérer (max. 25) |
| offset | integer | non | Le nombre de messages à ignorer |

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": [
        {
            "id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
            "author": "janedoe",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "created_at": "2019-01-01T00:00:00.000Z"
        },
        {
            "id": "75315733-501f-3727-88fd-d8aa354af674",
            "author": "johndoe",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "created_at": "2020-01-01T00:00:00.000Z"
        }
    ]
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 500 | Internal Server Error | Une erreur interne est survenue |

### GET /api/messages/:handle

Récupère la liste des messages échangés avec un utilisateur.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| limit | integer | non | Le nombre de messages à récupérer (max. 25) |
| offset | integer | non | Le nombre de messages à ignorer |

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": [
        {
            "id": "75315733-501f-3727-88fd-d8aa354af674",
            "author": "johndoe",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "created_at": "2020-01-01T00:00:00.000Z"
        },
        {
            "id": "75315733-501f-3727-88fd-d8aa354af674",
            "author": "johndoe",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "created_at": "2020-01-01T00:00:00.000Z"
        }
    ]
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | L'utilisateur n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### POST /api/messages/:handle

Envoie un message à un utilisateur.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| content | string | oui | Le contenu du message |

#### Réponse

```json
{
    "success": true,
    "code": 201,
    "message": "Created",
    "payload": {
        "id": "75315733-501f-3727-88fd-d8aa354af674",
        "author": "johndoe",
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        "created_at": "2020-01-01T00:00:00.000Z"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Le contenu du message est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | L'utilisateur n'existe pas |
| 429 | Too Many Requests | Le nombre de requêtes a été dépassé |
| 500 | Internal Server Error | Une erreur interne est survenue |

## Notifications

### GET /api/notifications

Récupère la liste des notifications de l'utilisateur.

#### Paramètres

Aucun paramètre n'est requis.

*⚠️ Uniquement les 50 dernières notifications sont conservées, les notifications plus anciennes sont automatiquement supprimées.*

*⚠️ Les notifications envoyées par l'API sont automatiquement marquées comme lues et supprimées.*

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": [
        {
            "id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
            "icon": "d8aa3aeafd7c4778 jpg",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "date": "2019-01-01T00:00:00.000Z"
        },
        {
            "id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
            "icon": "d8aa3aeafd7c4778 png",
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "date": "2020-01-01T00:00:00.000Z"
        }
    ]
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 500 | Internal Server Error | Une erreur interne est survenue |

## Posts

### GET /api/posts

Récupère la liste des posts.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| id | string | non | L'identifiant du post à récupérer |
| query | string | non | La chaîne de caractères à rechercher dans les titres et les descriptions |
| limit | integer | non | Le nombre de posts à récupérer (max. 25) |
| offset | integer | non | Le nombre de posts à ignorer |
| fromUser | string/array | non | Le(s) auteur(s) des posts à récupérer |
| excludeUser | string/array | non | Le(s) auteur(s) à exclure des posts à récupérer |
| replyTo | string | non | L'identifiant du post auquel les posts à récupérer sont des réponses |
| hasMedia | boolean | non | Si `true`, seuls les posts avec des médias seront récupérés |

\* `fromUser` ne peut pas contenir d'utilisateurs présents dans `excludeUser`.

\*\* `id` ne peut qu'être utilisé seul.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": [
        {
            "id": "a4d46173-161f-4778-87fd-d8aa3aeafd7c",
            "author": "johndoe",
            "createdAt": "2020-01-01T00:00:00.000Z",
            "tag": 5,
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "medias": [
                "d8aa3aeafd7c4778 jpg",
                "d8aa3aeafd7c4778 png"
            ],
            "likes": 15,
            "liked": false,
            "comments": 0,
            "views": 1300,
            "replyTo": null
        },
        {
            "id": "a4d46173-161f-4778-87fd-d8aa3aeafd7c",
            "author": "janedoe",
            "createdAt": "2020-01-01T00:00:00.000Z",
            "tag": 7,
            "content": "Curabitur euismod, nisl sit amet ultricies lacinia",
            "medias": null,
            "likes": 1,
            "liked": true,
            "comments": 45,
            "views": 150,
            "replyTo": null
        }
    ]
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | L'utilisateur / le post n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### GET /api/posts/:id

Récupère les informations d'un post.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": {
        "id": "a4d46173-161f-4778-87fd-d8aa3aeafd7c",
        "author": "johndoe",
        "createdAt": "2020-01-01T00:00:00.000Z",
        "tag": 5,
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        "medias": [
            "d8aa3aeafd7c4778 jpg",
            "d8aa3aeafd7c4778 png"
        ],
        "likes": 15,
        "liked": false,
        "comments": 0,
        "views": 1300,
        "replyTo": null
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | Le post n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### POST /api/posts

Crée un nouveau post.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| tag | integer | oui | Le tag du post |
| content | string | oui | Le contenu du post |
| medias | array | non | Les URLs des médias du post |
| replyTo | string | non | L'identifiant du post auquel le post est une réponse |

#### Réponse

```json
{
    "success": true,
    "code": 201,
    "message": "Created",
    "payload": {
        "id": "a4d46173-161f-4778-87fd-d8aa3aeafd7c",
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | Le post indiqué dans `replyTo` n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### DELETE /api/posts/:id

Supprime un post.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 204,
    "message": "No Content",
    "payload": null
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | Le post n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### POST /api/posts/:id/like

Ajoute un like au post.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 201,
    "message": "Created",
    "payload": null
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | Le post n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### DELETE /api/posts/:id/like

Supprime un like du post.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 204,
    "message": "No Content",
    "payload": null
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | Le post n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

### GET /api/coffee

Récupère les informations de la machine à café.
(Plus sérieusement, c'est une route pour tester les erreurs)

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": false,
    "code": 418,
    "message": "I'm a teapot",
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 418 | I'm a teapot | Ce n'est pas une machine à café :( |
