#  CTF – Spectre-7

---

## Informations générales

| Champ                | Valeur                       |
| -------------------- | ---------------------------- |
| **Nom du challenge** | Access Cookie                |
| **Auteur**           | Lenzzair                     |
| **Difficulté**       | Easy                         |
| **Code challenge**   | web2_E1                      |

---

## 📝 Description du challenge

Petit exercice d’initiation à la **manipulation de cookies** côté client.
Après inscription, le service place un cookie `login` contenant un **JSON encodé en Base64** (champ `role` = `"guest"`). Le joueur doit repérer et décoder ce cookie, modifier le champ `role` en `"admin"`, réencoder le contenu en Base64, remettre le cookie et accéder à `/admin` pour récupérer le flag.

But pédagogique : montrer que **Base64 ≠ chiffrement** et que stocker des données sensibles côté client sans vérification serveur est une mauvaise pratique.

Scénario utilisateur : inscription → cookie → dashboard (affiche les champs décoratifs) → accès admin seulement si `role == "admin"`.

---

## 🛠️ Techniques utilisées

* Manipulation d’en-têtes HTTP (cookies).
* Base64 encoding/decoding de JSON.
* Particularités : champs « décoratifs » dans le cookie (`language`) — inutiles pour la logique d’accès, présents pour réalisme.


https://github.com/user-attachments/assets/8b008816-7f09-4077-aae8-1c45423122fd


---

## Création du challenge

* Langage / framework : Python + Flask.
* Routes :

  * `/register` (GET/POST) : formulaire, création du cookie `login` (JSON → Base64).
  * `/dashboard` : lecture du cookie et affichage des champs.
  * `/admin` : lecture du cookie et contrôle d’accès `role == "admin"` → affiche flag.
  * Gestion des erreurs (403, redirections).

* Cookie :

  * Nom : `login`
  * Format : `Base64( json )`, exemple décodé :

    ```json
    {
      "user": "alice",
      "role": "guest",
      "language": "fr",
    }
    ```
* Sécurité : pas de chiffrement, pas de signature (vulnérabilité volontaire).
---

## Problèmes rencontrés / points à surveiller

* Validation : cookie mal formé → `get_user()` doit retourner `None` et rediriger vers `/register` (éviter crash Jinja).
* Edge cases : champ `role` manquant, JSON invalide → gérer proprement (redirect / 403) afin d’éviter 500.
* Tester sur plusieurs navigateurs / CURL (encodage / padding Base64).

---

## Structure du projet

```
./
├── app/
│   ├── app.py
│   ├── requirements.txt
│   ├── Dockerfile
│   ├── templates/
│   │   ├── register.html
│   │   ├── dashboard.html
│   │   ├── admin.html
│   │   └── 403.html
│   └── static/
│       ├── css/
│       └── images/
├── docker-compose.yml
└── README.md
```

---

## Déploiement interne

### Docker (exemple)

`docker-compose.yml` (extrait)

```yaml
services:
  web2_E1:
    build: ./app
    container_name: access-cookie-web2_e1
    ports:
      - "80:80" # port non definitif
```

### Commandes utiles

```bash
# Build & run
docker compose up -d --build

# Voir logs
docker logs -f access-cookie-web2_e1

# Arrêt
docker compose down
```

### Vérifications rapides

* Accéder à l’UI :

  ```
  http://<ip-server>/register
  ```
* Tester l’admin sans cookie (doit 403 ou rediriger) :

  ```bash
  curl -i http://<ip-server>/admin
  ```

---

## Flag

Format : `S7{...}`

**Flag (test / interne)** :

```
S7{C00k13_M4n1pul4t10n_B4s1c}
```

Emplacement / méthode de génération :

* Variable `FLAG` dans `app.py` injectée dans template `admin.html`.
* `admin.html` ne s’affiche que si le cookie `login` décodé contient `"role": "admin"`.

---

## Writeup interne (réservé à l’orga)

### Résolution attendue — cheminement utilisateur

1. Aller sur `/register` et créer un compte (ex. `alice`). Le serveur renvoie un cookie `login`.
2. Récupérer la valeur du cookie depuis le navigateur (DevTools → Application → Cookies) ou via une requête `curl -I`/`--cookie-jar`.
3. Décoder le cookie (Base64 → JSON) :

   ```bash
   echo -n "COOKIE_VALUE" | base64 -d
   ```
   ou avec cyberchef.
   ```
   https://gchq.github.io/CyberChef/
   ```

4. Modifier le champ `"role":"guest"` → `"role":"admin"`.
5. Réencoder le JSON en Base64 :

   ```bash
   echo -n '{"user":"alice","role":"admin",...}' | base64
   ```
6. Réinjecter le cookie modifié dans le navigateur (DevTools) ou utiliser `curl` :

   ```bash
   curl -b "login=NEW_BASE64" http://<ip-server>/admin
   ```
7. La page `/admin` s’affiche et présente le flag.

---
