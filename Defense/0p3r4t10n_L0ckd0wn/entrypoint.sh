#!/bin/bash

mkdir -p /opt/secret

cat > /opt/secret/credentials.env << 'CREDEOF'
# Credentials base Delta (NE PAS LAISSER ACCESSIBLE)
db_user=delta_admin
db_pass=S7{LF1_TR4V3RS4L_EXP0S3D_BY_L0GS}
api_key=sk-delta-7f3a9c2b1e
CREDEOF

chmod 644 /opt/secret/credentials.env
chown -R www-data:www-data /var/www/html

/usr/sbin/sshd
echo "[ENTRYPOINT] SSHd lancé"

/usr/local/bin/checker.sh >> /var/log/apache2/checker.log 2>&1 &
echo "[ENTRYPOINT] Checker lancé (PID $!)"
exec apache2-foreground
