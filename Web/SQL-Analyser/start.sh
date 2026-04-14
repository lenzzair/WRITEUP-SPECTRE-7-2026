#!/bin/bash

# Dossier data DB
mkdir -p /var/run/mysqld
chown -R mysql:mysql /var/run/mysqld

# Initialisation DB si vide
if [ ! -d "/var/lib/mysql/mysql" ]; then
  mysqld --initialize-insecure --user=mysql
fi

# Démarrage MySQL en arrière-plan
mysqld --user=mysql &
MYSQL_PID=$!

# Attendre que MySQL soit prêt
until mysqladmin ping --silent; do
  sleep 1
done

# Setup base et user
mysql -uroot <<EOF
CREATE DATABASE IF NOT EXISTS sqli_lab;
CREATE USER IF NOT EXISTS 'sqli_user'@'localhost' IDENTIFIED BY 'sqli_pass';
GRANT ALL PRIVILEGES ON sqli_lab.* TO 'sqli_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Import du flag
mysql -uroot sqli_lab </init.sql

# Lancer Apache au premier plan (process principal)
apache2-foreground
