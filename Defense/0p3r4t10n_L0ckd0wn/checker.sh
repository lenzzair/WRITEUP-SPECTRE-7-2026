#!/bin/bash
# Validateur automatique — tourne toutes les 30s

FLAG3="S7{1NC1D3NT_R3SP0NS3_C0MPL3T3D}"
FLAG_FILE="/var/www/html/PATCH_VALIDATED.txt"

check_patch() {
  local HTACCESS="/var/www/html/.htaccess"

  # Test 1 : .htaccess présent
  if [ ! -f "$HTACCESS" ]; then
    echo "[CHECKER] $(date '+%H:%M:%S') FAIL — Aucun .htaccess trouvé à la racine"
    return 1
  fi

  # Test 2 : /admin.php doit être bloqué (403)
  HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/admin.php)
  if [ "$HTTP_CODE" != "403" ]; then
    echo "[CHECKER] $(date '+%H:%M:%S') FAIL — /admin.php retourne $HTTP_CODE (attendu: 403)"
    return 1
  fi

  # Test 3 : la page principale doit toujours être accessible (200)
  HOME_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/)
  if [ "$HOME_CODE" != "200" ]; then
    echo "[CHECKER] $(date '+%H:%M:%S') FAIL — index.php inaccessible ($HOME_CODE) — règle trop large"
    return 1
  fi

  # Succès
  echo "[CHECKER] $(date '+%H:%M:%S') SUCCESS — /admin.php bloqué, index accessible"

  if [ ! -f "$FLAG_FILE" ]; then
    cat >"$FLAG_FILE" <<FLAGEOF
========================================
  SPECTRE-7 NEUTRALISÉ — PATCH VALIDÉ
========================================

  ${FLAG3}

  Validé le : $(date)
  /admin.php : BLOQUÉ (403)
  index.php  : OK (200)

========================================
FLAGEOF
    echo "[CHECKER] FLAG déposé dans /var/www/html/PATCH_VALIDATED.txt"
  fi
}

echo "[CHECKER] Démarrage — vérification toutes les 30s"
while true; do
  check_patch
  sleep 30
done
