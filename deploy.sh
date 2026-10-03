#!/usr/bin/env bash
# Met à jour bytechnum.com depuis la branche main.
# À lancer sur le serveur : cd ~/apps/technum && bash deploy.sh
set -euo pipefail

cd "$(dirname "$0")"

echo "1/4 Récupération de main"
git fetch origin main
git checkout main
git pull --ff-only origin main

echo "2/4 Dépendances de production"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

echo "3/4 Dossiers d'écriture"
mkdir -p storage/logs storage/rate-limit
chmod 750 storage storage/logs storage/rate-limit

echo "4/4 Contrôle de syntaxe"
find src public content templates tools -name '*.php' -print0 | xargs -0 -n1 php -l > /dev/null

echo "Mise à jour terminée."
