# TECHNUM

Site de la marque TECHNUM, servi sur [bytechnum.com](https://bytechnum.com) : ce que fait TECHNUM, les produits en service et leur état, les autres réalisations, les services et le formulaire de contact.

## Pile

PHP 8.4 sans framework, gabarits PHP, une feuille CSS et un script écrits à la main, aucune base de données, aucun cookie. Dépendances de production : PHPMailer et phpdotenv.

## Démarrer en local

1. Installer les dépendances : `composer install` puis `npm ci`.
2. Copier `.env.example` en `.env`, puis régler `APP_ENV=local`, `MAIL_TRANSPORT=log` et une clé `APP_SECRET` générée avec `php -r "echo bin2hex(random_bytes(32));"`.
3. Lancer le serveur : `php -S 127.0.0.1:8080 -t public tools/dev-router.php`.
4. Ouvrir http://127.0.0.1:8080. En local, les demandes du formulaire s'écrivent dans `storage/logs/mail-local.log`.

## Vérifier avant une pull request

```bash
composer check
npm run lint:js
npm run format:check
```

## Modifier le contenu

Les coordonnées, les produits, les réalisations et les services vivent dans `content/`. Chaque modification passe par une pull request : les tests contrôlent les champs, les adresses, les images et les textes interdits. Après un changement d'état d'un produit, mettre à jour `updatedAt` dans `content/site.php`.

## Mettre en ligne

Depuis `main` uniquement, sur le serveur : `cd ~/apps/technum && bash deploy.sh`. Le fichier `.env` du serveur n'est jamais commité. Le détail de l'architecture de déploiement est dans la spécification, section 9.

## Documents

- Conception : [docs/specs/2026-10-02-page-accueil-bytechnum-design.md](docs/specs/2026-10-02-page-accueil-bytechnum-design.md)
- Plan de réalisation : [docs/plans/2026-10-02-page-accueil-bytechnum.md](docs/plans/2026-10-02-page-accueil-bytechnum.md)
- Règles de contribution : [CONTRIBUTING.md](CONTRIBUTING.md)
