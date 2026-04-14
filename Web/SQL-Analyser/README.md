


---

#  CTF – Spectre 7

---

## Informations générales

| Champ                | Valeur                   |
| -------------------- | ------------------------ |
| **Nom du challenge** | SQL Analyzer             |
| **Auteur**           | lenzzair                 |
| **Difficulté**       | Easy                     |
| **Code challenge**   | web3_E3                 |

---

## Description du challenge

Le joueur accède à une interface web interne de Spectre-7 présentée comme un **outil d’analyse SQL** utilisé par un serveur de Command & Control.
L’authentification repose sur une requête SQL vulnérable.
L’interface affiche la requête exécutée ainsi que la réponse ou l’erreur SQL, simulant un **debug interne oublié**.

---

## Techniques utilisées

* SQL Injection (OR Injection)
* Authentification vulnérable
* PHP + MySQL (mysqli)
* Affichage volontaire des erreurs SQL
* Application web interne (C2)

---

## Création du challenge

* Application PHP volontairement non sécurisée
* Requête SQL construite dynamiquement sans filtrage
* Gestion des erreurs SQL pour éviter le crash et afficher le retour
* Interface graphique inspirée d’un outil interne Spectre-7

---

## Problèmes rencontrés

* Gestion des exceptions mysqli
* Éviter les fatal errors PHP lors d’erreurs SQL
* Initialisation correcte des variables (`$result`, `$sql_error`)

---

## 🗂️ Structure du projet

```
./
├── app/
│   └── html/
│       ├── login.php
│       ├── index.html
│       └── css/
├── docker/
│   └── docker-compose.yml
└── README.md
```

---

## Déploiement interne

```bash
docker build -t sqli-image .
docker run --rm --name sql_ctf -p 8080:80 sqli-image
```

* Services :

  * Web (PHP)
  * Base de données MySQL
* Base initialisée avec une table `users`
* Vérifier l’accès web et la connexion DB au démarrage

---

## Flag

Format : `S7{...}`
S7{1nj3ct10n_SQL_by_Qu3ry_L0g1c}

---

## Writeup interne (réservé à l’orga)

* Identifier l’entrée vulnérable (`username`, `password`)
* Observer la requête SQL affichée
* Exploiter une injection logique ( `'OR '1' = '1' -- -` )
* Comprendre l’impact des erreurs SQL affichées
* Valider l’accès ou extraire les données attendues
* Pièges : erreurs de syntaxe, commentaires SQL, ordre des conditions

https://github.com/user-attachments/assets/ad250344-e520-4160-bca2-9fd35a4d40d3
---
