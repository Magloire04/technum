# Contribuer au site TECHNUM

## Branches

| Branche | Rôle |
| --- | --- |
| `main` | Production. Aucun commit direct : fusion par pull request uniquement. |
| `develop` | Intégration. Les branches de travail en partent et y reviennent. |
| `feature/TECHNUM-{issue}-{description}` | Nouvelle fonctionnalité, créée depuis `develop`. |
| `bugfix/TECHNUM-{issue}-{description}` | Correction non urgente, créée depuis `develop`. |
| `hotfix/TECHNUM-{issue}-{description}` | Correction urgente, créée depuis `main`, fusionnée dans `main` et `develop`. |
| `release/AAAA-MM-JJ` | Préparation d'une mise en ligne, fusionnée dans `main` et `develop`. |

Les issues GitHub du dépôt tiennent lieu de tickets : chaque branche porte le numéro de son issue. Description en kebab-case, en minuscules.

## Commits

Format Conventional Commits : `type(portée): description`, en minuscules, sans point final, 72 caractères au plus. Types : `feat`, `fix`, `docs`, `refactor`, `chore`, `test`, `style`, `perf`.

## Pull requests

- Une pull request fait une seule chose et vise moins de 400 lignes. Tout dépassement est justifié dans sa description.
- Titre : `[#numéro] Description courte`, 60 caractères au plus.
- Description : objectif, changements, tests et checklist de l'auteur, selon le modèle du dépôt.
- L'intégration continue doit être verte avant toute fusion.

## Écart documenté : contributeur unique

Le dépôt n'a qu'un contributeur. Chaque pull request est relue par son auteur avec la checklist, puis fusionnée par lui une fois l'intégration continue verte. Dès l'arrivée d'un second contributeur, une approbation explicite devient obligatoire.

Écart de démarrage : les premiers commits précèdent la création du dépôt GitHub et de ses issues. Ils n'ont donc pas de numéro de ticket.

## Vérifications locales

```bash
composer check
npm run lint:js
npm run format:check
```

## Sécurité

- Aucun secret dans le dépôt : `.env` reste sur le serveur, `.env.example` documente chaque variable.
- Toute valeur affichée passe par `e()`.
- Aucune ressource tierce, aucun cookie, aucun script ni style en ligne.
