# CTF – Spectre 7

---

## Informations générales

| Champ                | Valeur                              |
|----------------------|-------------------------------------|
| **Nom du challenge** | sp3ctr3_w3b                         |
| **Auteur**           | lenzzair                            |
| **Difficulté**       | Medium                              |
| **Branche**          | SOC & Réponse à Incident            |
| **Code challenge**   | BONUS-BT-01                         |


---

## Description du challenge

Challenge de type **Blue Team / Réponse à Incident** déclenché à T+1h30 du CTF principal.

Le joueur se connecte en SSH à un serveur web Apache compromis.
Il doit analyser des logs Apache réalistes pour reconstituer une attaque en trois phases :
énumération de répertoires, exploitation d'une LFI sur une page admin exposée, exfiltration de fichiers sensibles.

Une fois l'attaque reconstituée, il doit corriger la vulnérabilité via un `.htaccess`
sans modifier le code PHP ni la configuration Apache.
Un checker automatique valide le patch et délivre le flag final.

**Ce que le joueur apprend :**
- Lecture et analyse de logs Apache
- Reconnaissance d'un scan de type dirb/gobuster dans les logs
- Compréhension d'une Local File Inclusion (LFI)
- Remédiation via `.htaccess` (directive `<Files>`, `Require all denied`)

---

## Techniques utilisées

- **Vulnérabilité** : Local File Inclusion (LFI) sur paramètre `?file=` non sanitisé
- **Page exposée** : `/admin.php` — pas d'authentification, accessible publiquement
- **Protocoles** : HTTP (Apache 2.4), SSH (OpenSSH)
- **Ports** : 80 (Apache interne), 8080 (exposé Docker), 22 (SSH interne), 2222 (exposé Docker)
- **Fichiers importants** :
  - `/var/log/apache2/access_delta.log` — logs avec traces de l'attaque
  - `/var/www/html/admin.php` — page vulnérable (LFI)
  - `/opt/secret/credentials.env` — flag 1, données sensibles exfiltrées
  - `/var/www/html/PATCH_VALIDATED.txt` — flag 2, généré par le checker
- **Dépendances** : Docker, php:7.4-apache, openssh-server
- **Particularité** : les symlinks Apache (`access.log → /dev/stdout`) doivent être cassés dans le Dockerfile pour que les vrais logs soient écrits sur disque

---

## Création du challenge

- Base image `php:7.4-apache` — PHP 7.4 volontairement choisi (pas de filtres modernes)
- La LFI est introduite intentionnellement dans `admin.php` via `file_get_contents($_GET['file'])` sans aucune sanitisation
- Les logs `access_delta.log` sont **pré-construits** et copiés dans l'image au build : ils contiennent du trafic légitime le matin, puis un scan dirb (22 requêtes en 404) suivi de l'exploitation LFI
- Le checker `checker.sh` tourne en boucle toutes les 30s via l'`entrypoint.sh` et vérifie : présence du `.htaccess`, retour 403 sur `/admin.php`, retour 200 sur `/`
- SSH ajouté via `openssh-server` — user `player` avec droits d'écriture dans `/var/www/html` via le groupe `www-data`

---

## Problèmes rencontrés

- **Symlinks Apache** : l'image `php:7.4-apache` remplace `access.log` et `error.log` par des symlinks vers `/dev/stdout` et `/dev/stderr`. Les logs ne sont donc pas écrits sur disque. Fix : utiliser un nom de fichier différent (`access_delta.log`) dans la directive `CustomLog`, ou casser les symlinks dans le Dockerfile avec `rm -f` + `touch`.
- **Cache Docker** : le `RUN rm -f` sur les symlinks peut ne pas être réexécuté si le layer est en cache. Toujours rebuilder avec `--no-cache` après modification du Dockerfile.
- **Droits SSH** : l'user `player` doit être dans le groupe `www-data` ET le répertoire `/var/www/html` doit avoir les droits `g+w` pour permettre le dépôt du `.htaccess`.
- **sshd** : nécessite que `/var/run/sshd` existe avant le démarrage — à créer dans le Dockerfile.

---

## Structure du projet

```
./
├── config/
│   └── apache.conf          # VirtualHost avec AllowOverride All
├── logs/
│   └── access_delta.log     # Logs pré-construits avec traces d'attaque
├── www/
│   ├── index.php            # Page publique (sans intérêt)
│   ├── admin.php            # Page vulnérable — LFI intentionnelle
│   └── css/
├── checker.sh               # Validateur automatique du patch (toutes les 30s)
├── entrypoint.sh            # Lance sshd + checker + apache2-foreground
├── Dockerfile
├── docker-compose.yml
├── ENONCE.md                # Énoncé distribué aux joueurs
├── README.md                # Ce fichier — documentation interne orga
```

---

## Déploiement interne

```bash
# Build et lancement
docker-compose up -d --build
docker-compose down


```
---

## Flags

**Format :** `S7{...}`

| Étape | Flag   | Emplacement                              |
|-------|-----------------------------------------|------------------------------------------|
| 1     | `S7{LF1_TR4V3RS4L_EXP0S3D_BY_L0GS}`   | `/opt/secret/credentials.env`                  |
| 2    | `S7{1NC1D3NT_R3SP0NS3_C0MPL3T3D}`      | `/var/www/html/PATCH_VALIDATED.txt`      |

---

## Writeup interne (réservé orga)

### Étape 1 — Lecture des logs

```bash
cat /var/log/apache2/access_delta.log
```

Le joueur repère :
- IP attaquante : `10.13.37.42`
- User-Agent : `SpectreBot/1.7` → script automatisé
- 22 requêtes en 404 (scan dirb) puis un 200 sur `/admin.php`
- Requêtes LFI : `?file=../../../opt/secret/credentials.env`

Flag 1 récupéré via : `cat /opt/secret/credentials.env`

### Étape 2 — Exploitation LFI

Le joueur accède à `http://<IP>:8080/admin.php`
et utilise le paramètre `?file=` pour lire les fichiers :

```
/admin.php?file=../../../opt/secret/credentials.env
```

Fichiers exfiltrés par Spectre-7 (visibles dans les logs) :

1. `../../../etc/hosts`
2. `../../../etc/apache2/apache2.conf`
3. `../../../var/secret/credentials.txt`


### Étape 3 — Remédiation .htaccess

Le joueur crée un `.htaccess` :

```apache
<Files "admin.php">
    Require all denied
</Files>
```

Le checker valide en moins de 30s et écrit le flag 3 dans `PATCH_VALIDATED.txt`.

**Pièges possibles :**
- Joueur qui bloque tout avec `Require all denied` sans cibler `admin.php` → l'index devient inaccessible, le checker échoue au test 200
- Joueur qui modifie `admin.php` directement → la contrainte de l'énoncé l'interdit, mais ça fonctionnerait techniquement
- Joueur qui utilise `php_flag engine off` → valide aussi, le checker accepte tout ce qui retourne 403 sur `/admin.php`
