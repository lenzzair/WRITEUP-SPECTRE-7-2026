# CTF – Spectre‑7

## Documentation interne du challenge

---

## Informations générales

| Champ                | Valeur            |
| -------------------- | ----------------- |
| **Nom du challenge** | Robots            |
| **Auteur**           | lenzzair         |
| **Difficulté**       | Easy              |
| **Code challenge**   | web1_E1           |

---


## Description du challenge

Les joueurs seront sur le dark moodle avec comme première res une doc sur l'user-agent et comment s'en servir. Puis dans une deuxième res ils auront une doc sur le fichier robots.txt.


L’objectif est de trouver le **/robots.txt** et de comprendre que l’accès à la page cachée **/hidden-entry.html** dépend de l’en-tête HTTP `User-Agent` et qu’il faut le modifier pour contourner le filtrage.

---

## Techniques utilisées

* Manipulation de l’en-tête **HTTP `User-Agent`**
* Lecture du fichier **robots.txt**
* Utilisation de **curl** ou **Burp Suite**
* Page protégée : `/hidden-entry.html`

---

## Création du challenge

* Mise en place d’un serveur **Flask** minimal
* Création d’une route `/hidden-entry.html` vérifiant la valeur du User‑Agent
* Refus systématique des requêtes ne correspondant pas au User‑Agent attendu (HTTP 403)
* Création d’un fichier `/robots.txt` servant à orienter les joueurs vers la mécanique User‑Agent
* Conteneurisation via **Docker** avec l’image `python:3.11-slim`
* Déploiement via **docker-compose**

---

## Problèmes rencontrés

* Temps de build important dû au téléchargement initial de l’image Python

---

## Structure du projet

```
./
├── app/
│   ├── app.py
│   ├── requirements.txt
│   ├── templates/
│   │   └── hidden-entry.html
│   └── static/
│       └── css/
│           └── css_template_moodle.css
├── docker-compose.yml
└── README.md
```

---

## Déploiement interne

### Lancement

```bash
docker-compose up -d --build
```

### Accès


**Robots :**

```
http://127.0.0.1/robots.txt
```

### Vérification rapide

```bash
# Accès refusé
curl http://127.0.0.1/hidden-entry.html

# Accès autorisé
curl -A "SpectreBot/1.7" http://127.0.0.1/hidden-entry.html
```

---

## Flag

Format : `S7{...}`
Flag :

```
ctf{H1dd3n_R0b0t_Ag3nt}
```

Emplacement :
Affiché dans la page `/hidden-entry.html` lorsque le User‑Agent correct est utilisé.

---

## Writeup interne (réservé à l’orga)


https://github.com/user-attachments/assets/8d51e9cc-de86-4617-83f1-a3dfe0d70496


1. Le joueur visite le site principal.
2. Il consulte `/robots.txt`.
3. Il découvre une mention indiquant qu’un User‑Agent spécifique est autorisé à accéder à la page cachée.
4. Il modifie son User‑Agent avec `curl` ou via Burp Suite :

```bash
curl -A "SpectreBot/1.7" http://127.0.0.1/hidden-entry.html
```

5. La page protégée s’affiche et révèle le flag.

Aucune vulnérabilité complexe n’est exploitée :
le challenge est volontairement pédagogique et destiné à initier les participants aux **en-têtes HTTP** et aux **contrôles d’accès simples** côté serveur.
