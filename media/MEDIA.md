# Media Folder

Les identifiants des ressources sont des chaînes de caractères aléatoires de **16 caractères**,
générées par le serveur lors de l'upload de la ressource.

Les extensions dépendent du type de ressource envoyée, pour un maximum de **4 caractères**.

## Limites

Les ressources envoyées ne doivent pas dépasser **2 Mo** (**8Mo** pour les vidéos) à l'upload.
Une fois le fichier uploadé, il est redimensionné si nécessaire, compressé et nettoyé pour effacer les métadonnées.

## Types de ressources

### Images

Les images sont envoyées au serveur avec l'extension `.png`, `.jpg`, `.jpeg` ou `.gif`.

### Vidéos

Les vidéos sont envoyées au serveur avec l'extension `.mp4`, `.webm` ou `.ogg`.

### Sons

Les sons sont envoyées au serveur avec l'extension `.mp3` ou `.wav`.

## Récupération des ressources

Les ressources sont disponibles à la racine du serveur, sous le chemin `/media/` suivi de l'identifiant de la ressource.

A la récuperation d'un ressource, le serveur renvoie un texte de 20 caractères,
les 16 premiers étant l'identifiant de la ressource, et les 4 derniers étant l'extension de la ressource.
*Exemple:* `87fdd8aa3aeafd7c png` (identifiant: `87fdd8aa3aeafd7c`, extension: `.png`).

La ressource est ensuite disponible à l'adresse `/media/87fdd8aa3aeafd7c.png`.

## Upload de ressources

Les ressources sont envoyées au serveur avec les différents endpoints nécessitant une ressource, elles sont alors uploadées en Base64 dans le corps de la requête.

*Exemple d'upload d'une image en Base64*:

```json
{
    "image": "data:image/png;base64,iVBORw0KGgo..."
}
```
