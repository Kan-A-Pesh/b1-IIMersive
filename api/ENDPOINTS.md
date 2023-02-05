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

## Authentification

### POST /api/auth/login

Authentifie un utilisateur.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| email | string | non* | L'adresse email de l'utilisateur |
| handle | string | non* | Le nom d'utilisateur de l'utilisateur |
| password | string | oui | Le mot de passe de l'utilisateur |

*Un des deux paramètres est obligatoire.

#### Réponse

```json
{
    "success": true,
    "code": 201,
    "message": "Created",
    "payload": {
        "sessionId": "4d461733-161f-4778-87fd-d8aa3aeafd7c"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'adresse email ou le nom d'utilisateur et/ou le mot de passe sont incorrects |
| 500 | Internal Server Error | Une erreur interne est survenue |

### POST /api/auth/logout

Déconnecte un utilisateur.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
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
| query | string | oui | La chaîne de caractères à rechercher dans les noms et les adresses email |
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
            "avatar": "https://domain.com/avatar.png"
        },
        {
            "handle": "janedoe",
            "name": "Jane Doe",
            "avatar": "https://domain.com/avatar.png"
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
        "avatar": "https://domain.com/avatar.png",
        "banner": "https://domain.com/banner.png"
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
        "sessionId": "4d461733-161f-4778-87fd-d8aa3aeafd7c"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 409 | Conflict | L'adresse email ou le nom d'utilisateur est déjà utilisé |
| 500 | Internal Server Error | Une erreur interne est survenue |

### PUT /api/users/:handle

Modifie les informations d'un utilisateur.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| name | string | non* | Le nom de l'utilisateur |
| biography | string | non* | La biographie de l'utilisateur |
| avatar | string | non* | L'URL de l'avatar de l'utilisateur |
| banner | string | non* | L'URL de la bannière de l'utilisateur |

*Un des paramètres est obligatoire.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": null
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 404 | Not Found | L'utilisateur n'existe pas |
| 500 | Internal Server Error | Une erreur interne est survenue |

## Posts

### GET /api/posts

Récupère la liste des posts.

#### Paramètres

| Paramètre | Type | Obligatoire | Description |
| --------- | ---- | ----------- | ----------- |
| query | string | oui | La chaîne de caractères à rechercher dans les titres et les descriptions |
| limit | integer | non | Le nombre de posts à récupérer (max. 25) |
| offset | integer | non | Le nombre de posts à ignorer |
| fromUser | string | non | Le nom d'utilisateur de l'auteur des posts à récupérer |
| excludeUser | string | non | Le nom d'utilisateur de l'auteur des posts à ignorer |
| replyTo | string | non | L'identifiant du post auquel les posts à récupérer sont des réponses |

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
    "payload": [
        {
            "id": "75324802",
            "author": "johndoe",
            "createdAt": "2020-01-01T00:00:00.000Z",
            "tag": 5,
            "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "medias": [
                "https://domain.com/image.png",
                "https://domain.com/video.mp4"
            ],
            "likes": 15,
            "liked": false,
            "comments": 0,
            "views": 1300,
            "replyTo": null
        },
        {
            "id": "12350123",
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
        "id": "75324802",
        "author": "johndoe",
        "createdAt": "2020-01-01T00:00:00.000Z",
        "tag": 5,
        "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        "medias": [
            "https://domain.com/image.png",
            "https://domain.com/video.mp4"
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
        "id": "75324802"
    }
}
```

#### Erreurs

| Code | Message | Description |
| ---- | ------- | ----------- |
| 400 | Bad Request | Un des paramètres est manquant ou invalide |
| 401 | Unauthorized | L'identifiant de session est invalide |
| 500 | Internal Server Error | Une erreur interne est survenue |

### DELETE /api/posts/:id

Supprime un post.

#### Paramètres

Aucun paramètre n'est requis.

#### Réponse

```json
{
    "success": true,
    "code": 200,
    "message": "OK",
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
    "code": 200,
    "message": "OK",
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
