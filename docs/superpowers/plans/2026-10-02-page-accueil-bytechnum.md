# Page d'accueil de bytechnum.com : plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Objectif :** remplacer la page « Bientôt en ligne » de bytechnum.com par la page d'accueil de la marque TECHNUM : promesse, produits en service avec leur état, autres réalisations, services, méthode, fondateur et contact par formulaire, WhatsApp, téléphone ou e-mail.

**Architecture :** site PHP 8.4 sans framework. Un point d'entrée unique (`public/index.php`) passe chaque requête à un petit routeur, les contrôleurs rendent des gabarits PHP, le contenu éditorial vit dans des fichiers PHP versionnés (`content/`). Aucune base de données, aucun cookie, aucune ressource tierce. Le front tient dans une feuille CSS et un script écrits à la main.

**Pile technique :** PHP 8.4, Composer, PHPMailer, phpdotenv, PHPUnit, PHPStan, PHP CS Fixer, ESLint, Prettier, GitHub Actions, hébergement mutualisé Spaceship (LiteSpeed).

**Spec :** `docs/superpowers/specs/2026-10-02-page-accueil-bytechnum-design.md`

## Contraintes globales

- PHP 8.4 en production. En local, toutes les commandes PHP passent par `/c/wamp64/bin/php/php8.4.15/php.exe` (le `php` du PATH est en 8.5). `composer.json` fixe `config.platform.php` à `8.4.0`.
- Dépendances de production autorisées : `phpmailer/phpmailer`, `vlucas/phpdotenv`. Développement : `phpunit/phpunit`, `phpstan/phpstan`, `friendsofphp/php-cs-fixer`, et côté npm `eslint`, `@eslint/js`, `globals`, `prettier`. Rien d'autre.
- Palette du brand book : Charcoal `#373536`, Blue `#405FE0`, Blue Dark `#2846B9`, Ice `#E9EDFF`, Off White `#F7F8FC`, blanc, bordures `#E5E7ED`. Dérivés du Charcoal autorisés pour l'accessibilité : `#5E5C5D` (texte secondaire), `#8A8889` (bordure des champs). Seule couleur hors palette : `#B42318`, réservée aux erreurs de formulaire.
- Polices : Montserrat 600 et 700 pour les titres, la navigation et les boutons. Poppins 400 et 500 pour le texte. Fichiers woff2 servis par le site. Inter, Space Grotesk et Geist interdites.
- Textes : français, casse normale, aucun tiret cadratin ni demi-cadratin, aucun émoji, aucune étiquette en capitales, aucun chiffre inventé, aucun faux témoignage.
- Contenu interdit : les deux mandats clients confidentiels et le dossier ai-learning ne sont jamais mentionnés. Les noms confidentiels ne figurent pas dans le dépôt : un test les reconnaît par leur empreinte SHA-256.
- Coordonnées : `+229 01 50 61 73 00` (lien `tel:+2290150617300`), WhatsApp `https://wa.me/2290150617300`, `elisee.atonde@bytechnum.com`, `https://github.com/Magloire04`, « Porto-Novo, Bénin ».
- Sécurité : toute valeur affichée passe par `e()`. Content-Security-Policy stricte, donc aucun script en ligne, aucune balise `<style>` et aucun attribut `style` dans les gabarits. Secrets uniquement dans `.env`, jamais commité.
- Captures : uniquement des captures réelles des produits.
- Git : Gitflow (`main`, `develop`, branches `feature/TECHNUM-{issue}-{description}`), Conventional Commits en minuscules de 72 caractères au plus, messages passés par `git commit -F -` avec un heredoc, aucune mention d'outil d'IA, aucun trailer de co-auteur.
- Mise en ligne et toute écriture sur le serveur : uniquement sur accord explicite d'Elisée, au moment de le faire. Ne jamais lire de fichier de secrets de production.
- Toute tâche qui touche l'interface se termine par une vérification dans un vrai navigateur, sur ordinateur et sur téléphone.

## Points de vigilance

1. **Visiteurs derrière le proxy de l'hébergeur.** Si `REMOTE_ADDR` porte l'adresse du proxy, tous les visiteurs partagent la même limite de cinq demandes par heure. Attendu : chaque visiteur a sa propre limite. Tests : tâche 2 (adresse lue dans l'en-tête configuré), tâche 18 (limite par adresse), contrôle réel en tâche 26.
2. **Page mise en cache avec un vieux jeton.** Un cache LiteSpeed qui garde la page ferait expirer les jetons et perdre des demandes. Attendu : le HTML n'est jamais mis en cache et un jeton expiré produit un message clair, pas un rejet silencieux. Tests : tâche 3 (en-têtes `Cache-Control` et `X-LiteSpeed-Cache-Control`), tâche 20 (jeton expiré).
3. **Chemin de fichier statique erroné.** Une faute dans un chemin d'image ou de feuille de style casse l'affichage sans faire échouer les tests HTTP. Attendu : chaque adresse `/assets/...` de la page existe sur le disque. Test : tâche 11.
4. **Envoi sans JavaScript, après une erreur ou en double clic.** Attendu : le formulaire marche sans script, garde les valeurs saisies, place chaque erreur sous son champ, ramène le visiteur sur la section contact et n'envoie qu'une demande par clic. Tests : tâche 20 (action `/contact#contact`, valeurs conservées, `aria-describedby`), tâche 21 (bouton désactivé pendant l'envoi).
5. **Saisie hostile.** HTML ou script dans un champ, retour à la ligne dans le nom, champ envoyé en tableau, texte trop long, UTF-8 invalide. Attendu : affichage échappé, aucune injection d'en-tête, message d'erreur propre, jamais d'erreur 500. Tests : tâche 16 (nettoyage et validation), tâche 20 (réaffichage échappé).

## Conventions d'exécution

- Dossier du dépôt : `c:/wamp64/www/TECHNUM`. Chaque bloc de commandes commence par `cd /c/wamp64/www/TECHNUM`.
- Les commandes PHP utilisent `PHP84=/c/wamp64/bin/php/php8.4.15/php.exe`, à redéfinir dans chaque bloc, car l'état du shell ne persiste pas.
- Vérification complète avant chaque pull request :

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
```

- À partir du lot 4, ajouter `npm run lint:js` et `npm run format:check`.
- Les captures du navigateur sont enregistrées dans `c:\wamp64\www\TECHNUM\.playwright-mcp\`, dossier exclu de Git par `.git/info/exclude`. Le supprimer à la fin de chaque tâche qui l'utilise.
- Serveur local : `"$PHP84" -S 127.0.0.1:8080 -t public tools/dev-router.php`, lancé en arrière-plan, arrêté à la fin de la tâche.
- Les numéros d'issue sont attribués par GitHub au moment de la création. Chaque lot crée son issue, garde son numéro dans `.git/TECHNUM_ISSUE` et l'utilise pour la branche et la pull request.
- Fusion d'une pull request : seulement quand l'intégration continue est verte, avec `gh pr merge --merge --delete-branch`, conformément à l'écart « contributeur unique » documenté dans `CONTRIBUTING.md`.

## Carte des fichiers

| Fichier | Rôle | Tâche |
| --- | --- | --- |
| `README.md`, `CONTRIBUTING.md`, `.github/pull_request_template.md` | Présentation, règles de contribution, modèle de PR | 1, 24 |
| `composer.json`, `phpunit.xml.dist`, `phpstan.neon`, `.php-cs-fixer.dist.php`, `.gitattributes` | Outillage PHP | 2 |
| `src/Http/Request.php`, `src/Http/Response.php`, `src/Http/Router.php` | Requête, réponse avec en-têtes de sécurité, routage | 2, 3 |
| `src/View/helpers.php`, `src/View/View.php` | Échappement `e()`, rendu des gabarits | 4 |
| `src/Config.php`, `src/ConfigException.php`, `.env.example` | Lecture et validation de la configuration | 5 |
| `.github/workflows/ci.yml` | Intégration continue | 6, 14 |
| `public/assets/img/**`, `public/favicon.ico`, `public/apple-touch-icon.png` | Logos, icônes, captures des produits | 7, 8 |
| `public/assets/fonts/**` | Polices et licences | 8 |
| `src/Content/*.php` | Modèle de contenu, typographie, chargement validé | 9, 10 |
| `content/*.php` | Contenu éditorial | 10 |
| `templates/**` | Gabarits | 11, 20, 22, 23 |
| `src/Page/HomePage.php`, `src/Page/StructuredData.php` | Rendu de l'accueil, données structurées | 11, 20, 23 |
| `src/Controller/*.php` | Accueil, erreurs, contact, pages légales | 12, 20, 22 |
| `src/Application.php`, `public/index.php`, `public/.htaccess`, `public/500.html`, `tools/dev-router.php` | Assemblage et point d'entrée | 12, 20, 22 |
| `public/assets/css/site.css`, `public/assets/js/site.js` | Styles et améliorations progressives | 13, 14, 21, 22 |
| `package.json`, `eslint.config.mjs`, `.prettierrc.json`, `.prettierignore` | Outillage front | 14 |
| `src/Contact/*.php`, `src/Security/*.php`, `src/Clock/*.php` | Règles et envoi du formulaire | 16 à 20 |
| `public/robots.txt`, `public/sitemap.xml`, `public/assets/img/og-image.png` | Référencement | 23 |
| `deploy.sh` | Mise à jour sur le serveur | 24 |
| `tests/Unit/**`, `tests/Integration/**`, `tests/Support/**`, `tests/Fixtures/**` | Tests | toutes |

---

## Lot 0 : publication de la conception

### Tâche 1 : publier le dépôt et fusionner la conception

**Fichiers :**

- Créer : `README.md` (remplace le fichier vide), `CONTRIBUTING.md`, `.github/pull_request_template.md`

**Interfaces :** aucune.

- [ ] **Étape 1 : vérifier l'état local**

```bash
cd /c/wamp64/www/TECHNUM
git status --short --branch
git branch --list
gh repo view Magloire04/technum 2>&1 | head -1
```

Attendu : branche `docs/spec-page-accueil` propre, branches `main` et `develop` présentes, et `gh` répond que `Magloire04/technum` n'existe pas.

- [ ] **Étape 2 : écrire `README.md`**

```markdown
# TECHNUM

Site de la marque TECHNUM, servi sur [bytechnum.com](https://bytechnum.com).

Le site présente ce que fait TECHNUM, les produits en service et leur état, les autres réalisations et les services, puis reçoit les demandes de projet.

## Documents

- Conception : [docs/superpowers/specs/2026-10-02-page-accueil-bytechnum-design.md](docs/superpowers/specs/2026-10-02-page-accueil-bytechnum-design.md)
- Plan de réalisation : [docs/superpowers/plans/2026-10-02-page-accueil-bytechnum.md](docs/superpowers/plans/2026-10-02-page-accueil-bytechnum.md)
- Règles de contribution : [CONTRIBUTING.md](CONTRIBUTING.md)

## Pile

PHP 8.4 sans framework, gabarits PHP, une feuille CSS et un petit script écrits à la main. Hébergement mutualisé Spaceship.
```

- [ ] **Étape 3 : écrire `CONTRIBUTING.md`**

````markdown
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
````

- [ ] **Étape 4 : écrire `.github/pull_request_template.md`**

```markdown
## Objectif

Contexte et issue liée : #

## Changements

-

## Tests

- [ ] `composer check`
- [ ] `npm run lint:js` et `npm run format:check`
- [ ] Vérification dans un navigateur, sur ordinateur et sur téléphone

## Checklist auteur

- [ ] Code relu par moi-même
- [ ] Pas de `console.log` ni de code de débogage
- [ ] Pas de secret exposé
- [ ] Nommage conforme aux conventions
- [ ] Moins de 400 lignes, ou dépassement justifié
```

- [ ] **Étape 5 : committer sur la branche de conception**

```bash
cd /c/wamp64/www/TECHNUM
git add README.md CONTRIBUTING.md .github/pull_request_template.md docs/superpowers/plans/2026-10-02-page-accueil-bytechnum.md
git commit -F - <<'EOF'
docs: ajoute les règles de contribution et le plan de réalisation
EOF
```

- [ ] **Étape 6 : créer le dépôt GitHub public et pousser les branches**

```bash
cd /c/wamp64/www/TECHNUM
gh repo create Magloire04/technum --public --description "Site de la marque TECHNUM, servi sur bytechnum.com" --source . --remote origin
git push -u origin main
git push -u origin develop
git push -u origin docs/spec-page-accueil
gh repo edit Magloire04/technum --default-branch develop
```

Attendu : trois branches visibles avec `gh api repos/Magloire04/technum/branches --jq '.[].name'`.

- [ ] **Étape 7 : ouvrir l'issue et la pull request de conception, puis fusionner**

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(gh issue create --title "Conception de la page d'accueil de bytechnum.com" --body "Spécification validée le 2 octobre 2026 et plan de réalisation." | sed 's#.*/##')
gh pr create --base develop --head docs/spec-page-accueil --title "[#$ISSUE] Spécification et plan de la page d'accueil" --body-file - <<EOF
## Objectif

Publier la conception validée de bytechnum.com. Issue liée : #$ISSUE

## Changements

- Spécification de conception, validée par Elisée le 2 octobre 2026
- Plan de réalisation
- README, règles de contribution et modèle de pull request

## Tests

- [x] Documents relus, aucun code

## Checklist auteur

- [x] Relu par moi-même
- [x] Pas de secret exposé
EOF
gh pr merge --merge --delete-branch
git checkout develop
git pull --ff-only
```

Attendu : `develop` contient la spécification, le plan et `CONTRIBUTING.md`.

---

## Lot 1 : socle technique

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Socle technique du site" --body "Outillage PHP, requête et réponse HTTP, routeur, vues, configuration et intégration continue." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-socle-technique"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

Le numéro est conservé dans `.git/TECHNUM_ISSUE`, hors des fichiers suivis, pour la pull request du lot.

### Tâche 2 : outillage PHP et requête HTTP

**Fichiers :**

- Créer : `composer.json`, `phpunit.xml.dist`, `phpstan.neon`, `.php-cs-fixer.dist.php`, `.gitattributes`, `src/Http/Request.php`, `tests/Unit/Http/RequestTest.php`, `tests/Integration/.gitkeep`
- Modifier : `.gitignore`

**Interfaces :**

- Produit : `Technum\Http\Request` avec `__construct(string $method, string $path, array $query = [], array $body = [], string $clientIp = '')`, `static fromGlobals(array $server, array $query, array $body, string $clientIpHeader = ''): self`, `static normalizePath(string $path): string`, `input(string $key): string`, propriétés publiques en lecture seule `method`, `path`, `query`, `body`, `clientIp`.

- [ ] **Étape 1 : écrire `composer.json`**

```json
{
    "name": "magloire04/technum",
    "description": "Site de la marque TECHNUM, servi sur bytechnum.com",
    "type": "project",
    "license": "proprietary",
    "require": {
        "php": "^8.4"
    },
    "autoload": {
        "psr-4": {
            "Technum\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Technum\\Tests\\": "tests/"
        }
    },
    "config": {
        "platform": {
            "php": "8.4.0"
        },
        "sort-packages": true
    },
    "scripts": {
        "test": "phpunit",
        "analyse": "phpstan analyse --no-progress --memory-limit=512M",
        "format": "php-cs-fixer fix",
        "format:check": "php-cs-fixer fix --dry-run --diff",
        "check": [
            "@format:check",
            "@analyse",
            "@test"
        ]
    }
}
```

- [ ] **Étape 2 : installer les outils de développement**

```bash
cd /c/wamp64/www/TECHNUM
composer require --dev phpunit/phpunit phpstan/phpstan friendsofphp/php-cs-fixer
```

Attendu : `composer.lock` et `vendor/` créés, sans erreur de compatibilité PHP.

- [ ] **Étape 3 : écrire `phpunit.xml.dist`**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         cacheDirectory=".phpunit.cache"
         colors="true"
         failOnRisky="true"
         failOnWarning="true"
         failOnDeprecation="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         displayDetailsOnTestsThatTriggerWarnings="true">
    <testsuites>
        <testsuite name="unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
    <source restrictDeprecations="true" restrictNotices="true" restrictWarnings="true">
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

- [ ] **Étape 4 : écrire `phpstan.neon`**

```neon
parameters:
    level: 8
    paths:
        - src
        - tests
    excludePaths:
        - tests/Fixtures
    tmpDir: var/phpstan
```

- [ ] **Étape 5 : écrire `.php-cs-fixer.dist.php`**

```php
<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['vendor', 'node_modules', 'templates', 'storage', 'var', 'documentations', 'docs', 'tests/Fixtures'])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder($finder);
```

- [ ] **Étape 6 : écrire `.gitattributes` et compléter `.gitignore`**

`.gitattributes` :

```text
* text=auto eol=lf
*.png binary
*.ico binary
*.webp binary
*.woff2 binary
*.pdf binary
```

Ajouter à la fin de `.gitignore` :

```text
# Fichiers temporaires des outils
/var/
```

Puis créer le dossier des tests d'intégration et renormaliser les fins de ligne :

```bash
cd /c/wamp64/www/TECHNUM
mkdir -p tests/Integration && touch tests/Integration/.gitkeep
git add --renormalize .
```

- [ ] **Étape 7 : écrire le test qui échoue, `tests/Unit/Http/RequestTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Http\Request;

final class RequestTest extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function paths(): iterable
    {
        yield 'racine' => ['/', '/'];
        yield 'vide' => ['', '/'];
        yield 'barre finale' => ['/cgu/', '/cgu'];
        yield 'barres multiples' => ['//cgu//', '/cgu'];
        yield 'sous-chemin' => ['/a/b/', '/a/b'];
    }

    #[DataProvider('paths')]
    public function testNormalizePathRemovesSurroundingSlashes(string $input, string $expected): void
    {
        self::assertSame($expected, Request::normalizePath($input));
    }

    public function testFromGlobalsReadsMethodPathQueryAndBody(): void
    {
        $request = Request::fromGlobals(
            ['REQUEST_METHOD' => 'post', 'REQUEST_URI' => '/contact/?envoi=ok', 'REMOTE_ADDR' => '203.0.113.5'],
            ['envoi' => 'ok', 'liste' => ['a']],
            ['name' => 'Awa'],
        );

        self::assertSame('POST', $request->method);
        self::assertSame('/contact', $request->path);
        self::assertSame(['envoi' => 'ok'], $request->query);
        self::assertSame('203.0.113.5', $request->clientIp);
        self::assertSame('Awa', $request->input('name'));
    }

    public function testInputReturnsEmptyStringForMissingOrNonTextValues(): void
    {
        $request = new Request('POST', '/contact', [], ['name' => ['x']]);

        self::assertSame('', $request->input('name'));
        self::assertSame('', $request->input('email'));
    }

    public function testClientIpComesFromLastValidAddressOfConfiguredHeader(): void
    {
        $request = Request::fromGlobals(
            ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => '198.51.100.7, 203.0.113.9'],
            [],
            [],
            'HTTP_X_FORWARDED_FOR',
        );

        self::assertSame('203.0.113.9', $request->clientIp);
    }

    public function testClientIpFallsBackToRemoteAddrWhenHeaderIsInvalid(): void
    {
        $request = Request::fromGlobals(
            ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => 'inconnu'],
            [],
            [],
            'HTTP_X_FORWARDED_FOR',
        );

        self::assertSame('10.0.0.1', $request->clientIp);
    }

    public function testClientIpIgnoresHeaderWhenNotConfigured(): void
    {
        $request = Request::fromGlobals(
            ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => '203.0.113.9'],
            [],
            [],
        );

        self::assertSame('10.0.0.1', $request->clientIp);
    }
}
```

- [ ] **Étape 8 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter RequestTest
```

Attendu : échec, classe `Technum\Http\Request` introuvable.

- [ ] **Étape 9 : écrire `src/Http/Request.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Http;

/**
 * Requête HTTP réduite à ce dont le site a besoin.
 */
final class Request
{
    /**
     * @param array<string, string>   $query
     * @param array<array-key, mixed> $body
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query = [],
        public readonly array $body = [],
        public readonly string $clientIp = '',
    ) {
    }

    /**
     * @param array<array-key, mixed> $server
     * @param array<array-key, mixed> $query
     * @param array<array-key, mixed> $body
     */
    public static function fromGlobals(array $server, array $query, array $body, string $clientIpHeader = ''): self
    {
        $uriPath = parse_url(self::text($server['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $textQuery = [];
        foreach ($query as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $textQuery[$key] = $value;
            }
        }

        return new self(
            strtoupper(self::text($server['REQUEST_METHOD'] ?? 'GET')),
            self::normalizePath(is_string($uriPath) ? $uriPath : '/'),
            $textQuery,
            $body,
            self::resolveClientIp($server, $clientIpHeader),
        );
    }

    public static function normalizePath(string $path): string
    {
        $trimmed = trim($path, '/');

        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    public function input(string $key): string
    {
        return self::text($this->body[$key] ?? '');
    }

    /**
     * Derrière un proxy, la dernière adresse de l'en-tête configuré est celle que le proxy a vue.
     *
     * @param array<array-key, mixed> $server
     */
    private static function resolveClientIp(array $server, string $clientIpHeader): string
    {
        if ($clientIpHeader !== '') {
            $addresses = array_map('trim', explode(',', self::text($server[$clientIpHeader] ?? '')));
            $last = $addresses[array_key_last($addresses)];
            if (filter_var($last, FILTER_VALIDATE_IP) !== false) {
                return $last;
            }
        }

        return self::text($server['REMOTE_ADDR'] ?? '');
    }

    private static function text(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
```

- [ ] **Étape 10 : relancer les tests et l'outillage**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter RequestTest
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
```

Attendu : 10 tests verts, aucune correction de format, `[OK] No errors`.

- [ ] **Étape 11 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add composer.json composer.lock phpunit.xml.dist phpstan.neon .php-cs-fixer.dist.php .gitattributes .gitignore src/Http/Request.php tests/Unit/Http/RequestTest.php tests/Integration/.gitkeep
git commit -F - <<'EOF'
feat(http): ajoute l'outillage php et la requête http
EOF
```

### Tâche 3 : réponse et routeur

**Fichiers :**

- Créer : `src/Http/Response.php`, `src/Http/Router.php`, `tests/Unit/Http/ResponseTest.php`, `tests/Unit/Http/RouterTest.php`

**Interfaces :**

- Consomme : `Request` (tâche 2).
- Produit : `Technum\Http\Response` avec la constante `CONTENT_SECURITY_POLICY`, `__construct(string $body = '', int $status = 200, array $headers = [])`, `static html(string $body, int $status = 200): self`, `static redirect(string $location, int $status = 303): self`, `header(string $name): ?string`, `withHeader(string $name, string $value): self`, `send(): void`. `Technum\Http\Router` avec `__construct(Closure $notFound)`, `get(string $path, Closure $handler): void`, `post(string $path, Closure $handler): void`, `dispatch(Request $request): Response`. Les gestionnaires ont le type `Closure(Request): Response`.

- [ ] **Étape 1 : écrire les tests qui échouent**

`tests/Unit/Http/ResponseTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Http;

use PHPUnit\Framework\TestCase;
use Technum\Http\Response;

final class ResponseTest extends TestCase
{
    public function testHtmlResponseCarriesSecurityAndNoCacheHeaders(): void
    {
        $response = Response::html('<p>Bonjour</p>', 422);

        self::assertSame(422, $response->status);
        self::assertSame('<p>Bonjour</p>', $response->body);
        self::assertSame('text/html; charset=utf-8', $response->header('Content-Type'));
        self::assertSame(Response::CONTENT_SECURITY_POLICY, $response->header('Content-Security-Policy'));
        self::assertStringContainsString("script-src 'self'", Response::CONTENT_SECURITY_POLICY);
        self::assertStringContainsString("frame-ancestors 'none'", Response::CONTENT_SECURITY_POLICY);
        self::assertSame('nosniff', $response->header('X-Content-Type-Options'));
        self::assertSame('strict-origin-when-cross-origin', $response->header('Referrer-Policy'));
        self::assertSame('no-cache, private', $response->header('Cache-Control'));
        self::assertSame('no-cache', $response->header('X-LiteSpeed-Cache-Control'));
    }

    public function testRedirectDefaultsToSeeOther(): void
    {
        $response = Response::redirect('/?envoi=ok#contact');

        self::assertSame(303, $response->status);
        self::assertSame('/?envoi=ok#contact', $response->header('Location'));
        self::assertSame('', $response->body);
    }

    public function testWithHeaderReturnsACopy(): void
    {
        $original = Response::html('x');
        $copy = $original->withHeader('Strict-Transport-Security', 'max-age=31536000');

        self::assertNull($original->header('Strict-Transport-Security'));
        self::assertSame('max-age=31536000', $copy->header('Strict-Transport-Security'));
        self::assertSame('x', $copy->body);
    }
}
```

`tests/Unit/Http/RouterTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Http;

use PHPUnit\Framework\TestCase;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Http\Router;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router(static fn (Request $request): Response => new Response('introuvable ' . $request->path, 404));
        $this->router->get('/', static fn (Request $request): Response => new Response('accueil'));
        $this->router->get('/cgu/', static fn (Request $request): Response => new Response('cgu'));
        $this->router->post('/contact', static fn (Request $request): Response => new Response('envoi ' . $request->input('name')));
    }

    public function testDispatchesGetRoute(): void
    {
        self::assertSame('accueil', $this->router->dispatch(new Request('GET', '/'))->body);
    }

    public function testRegisteredPathsAreNormalized(): void
    {
        self::assertSame('cgu', $this->router->dispatch(new Request('GET', '/cgu'))->body);
    }

    public function testDispatchesPostRouteWithBody(): void
    {
        $response = $this->router->dispatch(new Request('POST', '/contact', [], ['name' => 'Awa']));

        self::assertSame('envoi Awa', $response->body);
    }

    public function testHeadRequestUsesGetRoute(): void
    {
        self::assertSame('accueil', $this->router->dispatch(new Request('HEAD', '/'))->body);
    }

    public function testUnknownPathUsesNotFoundHandler(): void
    {
        $response = $this->router->dispatch(new Request('GET', '/inconnu'));

        self::assertSame(404, $response->status);
        self::assertSame('introuvable /inconnu', $response->body);
    }

    public function testWrongMethodUsesNotFoundHandler(): void
    {
        self::assertSame(404, $this->router->dispatch(new Request('GET', '/contact'))->status);
    }
}
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'ResponseTest|RouterTest'
```

Attendu : échec, classes `Response` et `Router` introuvables.

- [ ] **Étape 3 : écrire `src/Http/Response.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Http;

final class Response
{
    public const CONTENT_SECURITY_POLICY = "default-src 'self'; img-src 'self'; style-src 'self'; "
        . "script-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self'; "
        . "frame-ancestors 'none'; base-uri 'self'; object-src 'none'";

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string $body = '',
        public readonly int $status = 200,
        public readonly array $headers = [],
    ) {
    }

    /**
     * Le HTML n'est jamais mis en cache : la page porte un jeton de formulaire daté.
     */
    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Security-Policy' => self::CONTENT_SECURITY_POLICY,
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'X-Frame-Options' => 'DENY',
            'Cache-Control' => 'no-cache, private',
            'X-LiteSpeed-Cache-Control' => 'no-cache',
        ]);
    }

    public static function redirect(string $location, int $status = 303): self
    {
        return new self('', $status, [
            'Location' => $location,
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    public function header(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function withHeader(string $name, string $value): self
    {
        return new self($this->body, $this->status, [...$this->headers, $name => $value]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
    }
}
```

- [ ] **Étape 4 : écrire `src/Http/Router.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Http;

use Closure;

final class Router
{
    /** @var array<string, array<string, Closure(Request): Response>> */
    private array $routes = [];

    /**
     * @param Closure(Request): Response $notFound
     */
    public function __construct(private readonly Closure $notFound)
    {
    }

    /**
     * @param Closure(Request): Response $handler
     */
    public function get(string $path, Closure $handler): void
    {
        $this->routes['GET'][Request::normalizePath($path)] = $handler;
    }

    /**
     * @param Closure(Request): Response $handler
     */
    public function post(string $path, Closure $handler): void
    {
        $this->routes['POST'][Request::normalizePath($path)] = $handler;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method === 'HEAD' ? 'GET' : $request->method;
        $handler = $this->routes[$method][$request->path] ?? $this->notFound;

        return $handler($request);
    }
}
```

- [ ] **Étape 5 : relancer les tests et l'outillage**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
```

Attendu : tous les tests verts, aucune erreur d'analyse.

- [ ] **Étape 6 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add src/Http/Response.php src/Http/Router.php tests/Unit/Http/ResponseTest.php tests/Unit/Http/RouterTest.php
git commit -F - <<'EOF'
feat(http): ajoute la réponse sécurisée et le routeur
EOF
```

### Tâche 4 : vues et échappement

**Fichiers :**

- Créer : `src/View/helpers.php`, `src/View/View.php`, `tests/Unit/View/ViewTest.php`, `tests/Fixtures/templates/greeting.php`, `tests/Fixtures/templates/layout.php`, `tests/Fixtures/templates/nested.php`, `tests/Fixtures/templates/broken.php`, `tests/Fixtures/public/assets/css/test.css`
- Modifier : `composer.json` (chargement automatique de `helpers.php`)

**Interfaces :**

- Produit : fonction globale `e(string|int|float|null $value): string`. `Technum\View\View` avec `__construct(string $templatesDir, string $publicDir)`, `share(array $data): void`, `render(string $template, array $data = []): string`, `renderPage(string $template, array $data, array $meta): string` (rend le gabarit, puis `layout` avec les variables `content` et `meta`), `asset(string $path): string` (renvoie `/assets/{chemin}?v={date de modification}`). Dans un gabarit, la variable `$view` désigne la vue courante. Les noms `file`, `variables` et `view` sont réservés.

- [ ] **Étape 1 : écrire les gabarits de test**

`tests/Fixtures/templates/greeting.php` :

```php
<p class="greeting">Bonjour <?= e($name) ?><?php if (isset($site)) : ?> de <?= e($site) ?><?php endif ?></p>
```

`tests/Fixtures/templates/layout.php` :

```php
<main data-title="<?= e($meta['title']) ?>"><?= $content ?></main>
```

`tests/Fixtures/templates/nested.php` :

```php
<section><?= $view->render('greeting', ['name' => 'Kossi']) ?></section>
```

`tests/Fixtures/templates/broken.php` :

```php
<p>avant</p><?php throw new RuntimeException('gabarit cassé'); ?>
```

`tests/Fixtures/public/assets/css/test.css` :

```css
body {
  margin: 0;
}
```

- [ ] **Étape 2 : écrire le test qui échoue, `tests/Unit/View/ViewTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Technum\View\View;

final class ViewTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../../Fixtures';

    private View $view;

    protected function setUp(): void
    {
        $this->view = new View(self::FIXTURES . '/templates', self::FIXTURES . '/public');
    }

    public function testEscapeFunctionNeutralizesHtmlAndQuotes(): void
    {
        self::assertSame(
            '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt; &amp; l&apos;eau',
            e('<script>alert("x")</script> & l\'eau'),
        );
        self::assertSame('', e(null));
        self::assertSame('42', e(42));
    }

    public function testRenderEscapesData(): void
    {
        $html = $this->view->render('greeting', ['name' => '<b>Awa</b>']);

        self::assertSame('<p class="greeting">Bonjour &lt;b&gt;Awa&lt;/b&gt;</p>', trim($html));
    }

    public function testSharedDataIsAvailableAndCanBeOverridden(): void
    {
        $this->view->share(['site' => 'TECHNUM', 'name' => 'partagé']);

        $html = $this->view->render('greeting', ['name' => 'Awa']);

        self::assertSame('<p class="greeting">Bonjour Awa de TECHNUM</p>', trim($html));
    }

    public function testTemplatesCanRenderPartials(): void
    {
        self::assertStringContainsString('Bonjour Kossi', $this->view->render('nested'));
    }

    public function testRenderPageWrapsContentInLayout(): void
    {
        $html = $this->view->renderPage('greeting', ['name' => 'Awa'], ['title' => 'Accueil']);

        self::assertStringStartsWith('<main data-title="Accueil"><p class="greeting">Bonjour Awa</p>', $html);
    }

    public function testMissingTemplateThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gabarit introuvable : absent');

        $this->view->render('absent');
    }

    public function testFailingTemplateDoesNotLeakOutputBuffers(): void
    {
        $level = ob_get_level();

        try {
            $this->view->render('broken');
            self::fail('Une exception était attendue.');
        } catch (RuntimeException $exception) {
            self::assertSame('gabarit cassé', $exception->getMessage());
        }

        self::assertSame($level, ob_get_level());
    }

    public function testAssetUrlCarriesFileVersion(): void
    {
        $file = self::FIXTURES . '/public/assets/css/test.css';

        self::assertSame('/assets/css/test.css?v=' . filemtime($file), $this->view->asset('css/test.css'));
        self::assertSame('/assets/css/absent.css?v=0', $this->view->asset('/css/absent.css'));
    }
}
```

- [ ] **Étape 3 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter ViewTest
```

Attendu : échec, fonction `e()` et classe `View` introuvables.

- [ ] **Étape 4 : écrire `src/View/helpers.php`**

```php
<?php

declare(strict_types=1);

/**
 * Échappe une valeur pour l'afficher dans du HTML, y compris dans un attribut.
 */
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}
```

- [ ] **Étape 5 : déclarer le fichier dans `composer.json`**

Dans `composer.json`, le bloc `autoload` devient :

```json
    "autoload": {
        "psr-4": {
            "Technum\\": "src/"
        },
        "files": [
            "src/View/helpers.php"
        ]
    },
```

Puis :

```bash
cd /c/wamp64/www/TECHNUM
composer dump-autoload
```

- [ ] **Étape 6 : écrire `src/View/View.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\View;

use RuntimeException;
use Throwable;

/**
 * Rendu des gabarits PHP. Dans un gabarit, $view désigne cette instance.
 */
final class View
{
    /** @var array<string, mixed> */
    private array $shared = [];

    public function __construct(
        private readonly string $templatesDir,
        private readonly string $publicDir,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function share(array $data): void
    {
        $this->shared = [...$this->shared, ...$data];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $file = $this->templatesDir . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('Gabarit introuvable : ' . $template);
        }

        return $this->capture($file, [...$this->shared, ...$data]);
    }

    /**
     * Rend un gabarit de page puis l'insère dans le gabarit commun « layout ».
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     */
    public function renderPage(string $template, array $data, array $meta): string
    {
        $content = $this->render($template, $data);

        return $this->render('layout', ['content' => $content, 'meta' => $meta]);
    }

    /**
     * Adresse d'un fichier de public/assets, versionnée par sa date de modification.
     */
    public function asset(string $path): string
    {
        $relative = ltrim($path, '/');
        $file = $this->publicDir . '/assets/' . $relative;
        $version = is_file($file) ? (string) filemtime($file) : '0';

        return '/assets/' . $relative . '?v=' . $version;
    }

    /**
     * @param array<string, mixed> $variables
     */
    private function capture(string $file, array $variables): string
    {
        $view = $this;
        extract($variables, EXTR_SKIP);
        ob_start();

        try {
            require $file;
        } catch (Throwable $exception) {
            ob_end_clean();

            throw $exception;
        }

        return (string) ob_get_clean();
    }
}
```

- [ ] **Étape 7 : relancer les tests et l'outillage**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
```

Attendu : tous les tests verts, aucune erreur.

- [ ] **Étape 8 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add composer.json src/View tests/Unit/View tests/Fixtures
git commit -F - <<'EOF'
feat(view): ajoute le rendu des gabarits et l'échappement
EOF
```

### Tâche 5 : configuration

**Fichiers :**

- Créer : `src/Config.php`, `src/ConfigException.php`, `.env.example`, `tests/Unit/ConfigTest.php`

**Interfaces :**

- Produit : `Technum\Config` avec `static fromArray(array $env): self` (lève `ConfigException`), `isProduction(): bool`, propriétés publiques en lecture seule `appEnv`, `appSecret`, `mailTransport` (`smtp` ou `log`), `smtpHost`, `smtpPort` (int), `smtpUsername`, `smtpPassword`, `contactSenderEmail`, `contactRecipientEmail`, `clientIpHeader`.

- [ ] **Étape 1 : écrire le test qui échoue, `tests/Unit/ConfigTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Config;
use Technum\ConfigException;

final class ConfigTest extends TestCase
{
    /** @return array<string, string> */
    private static function smtpEnv(): array
    {
        return [
            'APP_ENV' => 'production',
            'APP_SECRET' => str_repeat('s', 64),
            'MAIL_TRANSPORT' => 'smtp',
            'SMTP_HOST' => 'smtp.example.test',
            'SMTP_PORT' => '465',
            'SMTP_USERNAME' => 'elisee.atonde@bytechnum.com',
            'SMTP_PASSWORD' => 'mot-de-passe-secret',
            'CONTACT_SENDER_EMAIL' => 'elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL' => 'elisee.atonde@bytechnum.com',
        ];
    }

    public function testValidSmtpConfiguration(): void
    {
        $config = Config::fromArray(self::smtpEnv());

        self::assertTrue($config->isProduction());
        self::assertSame('smtp', $config->mailTransport);
        self::assertSame('smtp.example.test', $config->smtpHost);
        self::assertSame(465, $config->smtpPort);
        self::assertSame('mot-de-passe-secret', $config->smtpPassword);
        self::assertSame('', $config->clientIpHeader);
    }

    public function testDefaultsAreProductionAndSmtp(): void
    {
        $env = self::smtpEnv();
        unset($env['APP_ENV'], $env['MAIL_TRANSPORT']);

        $config = Config::fromArray($env);

        self::assertSame('production', $config->appEnv);
        self::assertSame('smtp', $config->mailTransport);
    }

    public function testLocalLogTransportDoesNotNeedSmtp(): void
    {
        $config = Config::fromArray([
            'APP_ENV' => 'local',
            'APP_SECRET' => str_repeat('s', 32),
            'MAIL_TRANSPORT' => 'log',
            'CONTACT_SENDER_EMAIL' => 'elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL' => 'elisee.atonde@bytechnum.com',
        ]);

        self::assertFalse($config->isProduction());
        self::assertSame('log', $config->mailTransport);
        self::assertSame(0, $config->smtpPort);
    }

    public function testClientIpHeaderIsKept(): void
    {
        $config = Config::fromArray([...self::smtpEnv(), 'CLIENT_IP_HEADER' => 'HTTP_X_FORWARDED_FOR']);

        self::assertSame('HTTP_X_FORWARDED_FOR', $config->clientIpHeader);
    }

    /** @return iterable<string, array{array<string, string>, string}> */
    public static function invalidEnvironments(): iterable
    {
        $base = self::smtpEnv();

        yield 'secret trop court' => [[...$base, 'APP_SECRET' => 'court'], 'APP_SECRET doit contenir au moins 32 caractères'];
        yield 'hôte SMTP absent' => [[...$base, 'SMTP_HOST' => ''], 'SMTP_HOST est vide'];
        yield 'port invalide' => [[...$base, 'SMTP_PORT' => 'abc'], 'SMTP_PORT doit être un numéro de port'];
        yield 'journal en production' => [[...$base, 'MAIL_TRANSPORT' => 'log'], 'MAIL_TRANSPORT=log est interdit en production'];
        yield 'transport inconnu' => [[...$base, 'MAIL_TRANSPORT' => 'pigeon'], 'MAIL_TRANSPORT doit valoir smtp ou log'];
        yield 'destinataire invalide' => [[...$base, 'CONTACT_RECIPIENT_EMAIL' => 'pas-une-adresse'], 'CONTACT_RECIPIENT_EMAIL doit être une adresse e-mail valide'];
        yield 'en-tête IP invalide' => [[...$base, 'CLIENT_IP_HEADER' => 'X-Forwarded-For'], 'CLIENT_IP_HEADER doit ressembler à HTTP_X_FORWARDED_FOR'];
    }

    /**
     * @param array<string, string> $env
     */
    #[DataProvider('invalidEnvironments')]
    public function testInvalidConfigurationIsRejectedWithAClearMessage(array $env, string $expectedProblem): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage($expectedProblem);

        Config::fromArray($env);
    }

    public function testErrorMessageNeverContainsThePassword(): void
    {
        try {
            Config::fromArray([...self::smtpEnv(), 'SMTP_HOST' => '']);
            self::fail('Une exception était attendue.');
        } catch (ConfigException $exception) {
            self::assertStringNotContainsString('mot-de-passe-secret', $exception->getMessage());
        }
    }
}
```

- [ ] **Étape 2 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter ConfigTest
```

Attendu : échec, classe `Technum\Config` introuvable.

- [ ] **Étape 3 : écrire `src/ConfigException.php`**

```php
<?php

declare(strict_types=1);

namespace Technum;

use RuntimeException;

final class ConfigException extends RuntimeException
{
}
```

- [ ] **Étape 4 : écrire `src/Config.php`**

```php
<?php

declare(strict_types=1);

namespace Technum;

/**
 * Configuration lue dans .env et validée au démarrage. Aucun message d'erreur ne contient de secret.
 */
final class Config
{
    private const SMTP_KEYS = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD'];

    private function __construct(
        public readonly string $appEnv,
        public readonly string $appSecret,
        public readonly string $mailTransport,
        public readonly string $smtpHost,
        public readonly int $smtpPort,
        public readonly string $smtpUsername,
        public readonly string $smtpPassword,
        public readonly string $contactSenderEmail,
        public readonly string $contactRecipientEmail,
        public readonly string $clientIpHeader,
    ) {
    }

    /**
     * @param array<string, string|null> $env
     */
    public static function fromArray(array $env): self
    {
        $read = static fn (string $key): string => trim((string) ($env[$key] ?? ''));
        $appEnv = $read('APP_ENV') !== '' ? $read('APP_ENV') : 'production';
        $mailTransport = $read('MAIL_TRANSPORT') !== '' ? $read('MAIL_TRANSPORT') : 'smtp';
        $problems = [];

        if (strlen($read('APP_SECRET')) < 32) {
            $problems[] = 'APP_SECRET doit contenir au moins 32 caractères';
        }

        if (!in_array($mailTransport, ['smtp', 'log'], true)) {
            $problems[] = 'MAIL_TRANSPORT doit valoir smtp ou log';
        } elseif ($mailTransport === 'log' && $appEnv === 'production') {
            $problems[] = 'MAIL_TRANSPORT=log est interdit en production';
        }

        $port = 0;
        if ($mailTransport === 'smtp') {
            foreach (self::SMTP_KEYS as $key) {
                if ($read($key) === '') {
                    $problems[] = $key . ' est vide';
                }
            }
            $parsedPort = filter_var($read('SMTP_PORT'), FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 65535],
            ]);
            if ($read('SMTP_PORT') !== '' && $parsedPort === false) {
                $problems[] = 'SMTP_PORT doit être un numéro de port';
            }
            $port = $parsedPort === false ? 0 : $parsedPort;
        }

        foreach (['CONTACT_SENDER_EMAIL', 'CONTACT_RECIPIENT_EMAIL'] as $key) {
            if (filter_var($read($key), FILTER_VALIDATE_EMAIL) === false) {
                $problems[] = $key . ' doit être une adresse e-mail valide';
            }
        }

        $clientIpHeader = $read('CLIENT_IP_HEADER');
        if ($clientIpHeader !== '' && preg_match('/^HTTP_[A-Z0-9_]+$/', $clientIpHeader) !== 1) {
            $problems[] = 'CLIENT_IP_HEADER doit ressembler à HTTP_X_FORWARDED_FOR';
        }

        if ($problems !== []) {
            throw new ConfigException('Configuration invalide : ' . implode(', ', $problems) . '.');
        }

        return new self(
            appEnv: $appEnv,
            appSecret: $read('APP_SECRET'),
            mailTransport: $mailTransport,
            smtpHost: $read('SMTP_HOST'),
            smtpPort: $port,
            smtpUsername: $read('SMTP_USERNAME'),
            smtpPassword: (string) ($env['SMTP_PASSWORD'] ?? ''),
            contactSenderEmail: $read('CONTACT_SENDER_EMAIL'),
            contactRecipientEmail: $read('CONTACT_RECIPIENT_EMAIL'),
            clientIpHeader: $clientIpHeader,
        );
    }

    public function isProduction(): bool
    {
        return $this->appEnv === 'production';
    }
}
```

- [ ] **Étape 5 : écrire `.env.example`**

```text
# Environnement : production en ligne, local sur le poste de développement.
APP_ENV=production

# Clé secrète d'au moins 32 caractères. Générer avec : php -r "echo bin2hex(random_bytes(32));"
APP_SECRET=

# Envoi des demandes : smtp en production, log en local (écrit dans storage/logs/mail-local.log).
MAIL_TRANSPORT=smtp

# Serveur SMTP de la messagerie Spacemail, à reprendre dans les réglages Spacemail.
SMTP_HOST=

# Port SMTP : 465 pour une connexion chiffrée SSL.
SMTP_PORT=465

# Adresse complète de la boîte qui envoie les demandes.
SMTP_USERNAME=

# Mot de passe de cette boîte. À saisir uniquement dans le .env du serveur.
SMTP_PASSWORD=

# Expéditeur des e-mails du formulaire, une adresse bytechnum.com authentifiée.
CONTACT_SENDER_EMAIL=elisee.atonde@bytechnum.com

# Destinataire des demandes.
CONTACT_RECIPIENT_EMAIL=elisee.atonde@bytechnum.com

# Facultatif : variable serveur qui porte la vraie adresse IP derrière un proxy, par exemple HTTP_X_FORWARDED_FOR.
CLIENT_IP_HEADER=
```

- [ ] **Étape 6 : relancer les tests et l'outillage**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
```

Attendu : tous les tests verts, aucune erreur.

- [ ] **Étape 7 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add src/Config.php src/ConfigException.php .env.example tests/Unit/ConfigTest.php
git commit -F - <<'EOF'
feat(config): valide la configuration lue dans .env
EOF
```

### Tâche 6 : intégration continue et pull request du socle

**Fichiers :**

- Créer : `.github/workflows/ci.yml`

**Interfaces :** aucune.

- [ ] **Étape 1 : écrire `.github/workflows/ci.yml`**

```yaml
name: CI

on:
  pull_request:
  push:
    branches: [main, develop]

permissions:
  contents: read

jobs:
  php:
    name: PHP, format, analyse et tests
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          coverage: none
          tools: composer:v2
      - name: Dépendances
        run: composer install --no-interaction --no-progress --prefer-dist
      - name: Mise en forme
        run: vendor/bin/php-cs-fixer fix --dry-run --diff
      - name: Analyse statique
        run: vendor/bin/phpstan analyse --no-progress --memory-limit=512M
      - name: Tests
        run: vendor/bin/phpunit

  secrets:
    name: Recherche de secrets
    runs-on: ubuntu-latest
    permissions:
      contents: read
      pull-requests: read
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0
      - uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

Le workflow n'a pas de filtre de chemins : il tourne aussi quand on le modifie.

- [ ] **Étape 2 : vérification complète puis commit**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
git add .github/workflows/ci.yml
git commit -F - <<'EOF'
chore(ci): ajoute l'intégration continue php et la recherche de secrets
EOF
```

- [ ] **Étape 3 : pousser, ouvrir la pull request, attendre l'intégration continue, fusionner**

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Socle technique : HTTP, vues, configuration" --body-file - <<EOF
## Objectif

Poser le socle technique du site. Issue liée : #$ISSUE

## Changements

- Outillage PHP : Composer, PHPUnit, PHPStan niveau 8, PHP CS Fixer
- Requête HTTP, réponse avec en-têtes de sécurité, routeur
- Rendu des gabarits et échappement
- Configuration validée, sans secret dans les messages
- Intégration continue PHP et recherche de secrets

## Tests

- [x] Tests unitaires verts en local avec PHP 8.4
- [x] Analyse statique et mise en forme sans erreur

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de code de débogage
- [x] Pas de secret exposé
- [x] Nommage conforme aux conventions
- [x] Taille : environ 600 lignes, dont la moitié de tests
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

Attendu : les trois vérifications de l'intégration continue sont vertes avant la fusion. En cas d'échec, corriger sur la branche, committer, pousser et relancer `gh pr checks --watch`.

---

## Lot 2 : ressources visuelles et contenu

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Ressources visuelles et contenu" --body "Logos et icônes, polices, captures réelles des produits, modèle et fichiers de contenu." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-ressources-contenu"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

Les scripts Python de ce lot sont des outils ponctuels. Ils vivent hors du dépôt, dans `/c/Users/jenmf/AppData/Local/Temp/technum-outils`, seuls leurs résultats sont commités.

### Tâche 7 : logos et icônes

**Fichiers :**

- Créer : `public/assets/img/logo-technum.svg`, `public/assets/img/logo-technum-signature.svg`, `public/assets/img/logo-technum-clair-signature.svg`, `public/assets/img/favicon.svg`, `public/assets/img/logo-technum-carre-512.png`, `public/favicon.ico`, `public/apple-touch-icon.png`, `public/assets/img/produits/oeil360-finance-icone.png`, `public/assets/img/produits/dis-oui-icone.svg`, `public/assets/img/produits/provia-icone.svg`, `public/assets/img/produits/carte-uac-icone.svg`

**Interfaces :**

- Produit : les chemins ci-dessus, utilisés par les gabarits (tâche 11) et le contenu (tâche 10). Rapport largeur sur hauteur : `logo-technum.svg` environ 579 × 160, `logo-technum-signature.svg` et `logo-technum-clair-signature.svg` environ 587 × 195.

Le SVG fourni (`documentations/TECHNUM-LOGO.svg`) est le logo complet en noir. Il contient 40 tracés : 0 le trait vertical, 1 l'accolade gauche, 2 à 4 les lettres T, E et C, 5 un seul tracé qui réunit le H et le N, 6 à 10 l'accolade droite et ses pixels, 11 et 12 les lettres U et M, 13 à 39 la signature. Sur le PNG officiel, la jambe gauche et la barre du H sont en Charcoal, la jambe droite du H, partagée avec le N, est bleue. Le script colore le tracé 5 avec un dégradé à arrêt net à `x = 414`. Le moteur de rendu de PyMuPDF ignore ces dégradés : la vérification se fait dans un navigateur.

- [ ] **Étape 1 : préparer les outils Python**

```bash
TOOLS=/c/Users/jenmf/AppData/Local/Temp/technum-outils
mkdir -p "$TOOLS"
python -m pip install --quiet --target "$TOOLS/pylib" svgelements pillow pymupdf
```

- [ ] **Étape 2 : écrire `$TOOLS/build_logos.py`**

```python
"""Génère les déclinaisons du logo TECHNUM à partir du SVG monochrome officiel.

Usage : python build_logos.py documentations/TECHNUM-LOGO.svg public/assets/img
"""
import sys
from pathlib import Path as FsPath

from svgelements import SVG, Path

CHARCOAL, BLUE, WHITE = "#373536", "#405FE0", "#FFFFFF"
# Limite entre la partie du H en Charcoal et la jambe partagée avec le N, en bleu,
# relevée sur le PNG officiel, en coordonnées du SVG converties en pixels.
SPLIT_X = 414.0
BAR, BRACE_LEFT = [0], [1]
BRACE_RIGHT_AND_PIXELS = [6, 7, 8, 9, 10]
TECH, HN, NUM = [2, 3, 4], 5, [11, 12]
SIGNATURE = list(range(13, 40))


def load(source):
    paths = [element for element in SVG.parse(source).elements() if isinstance(element, Path)]
    if len(paths) != 40:
        raise SystemExit(f"40 tracés attendus, {len(paths)} trouvés : le SVG source a changé")
    return paths


def bbox(paths, indexes):
    boxes = [paths[i].bbox() for i in indexes]
    return (
        min(b[0] for b in boxes),
        min(b[1] for b in boxes),
        max(b[2] for b in boxes),
        max(b[3] for b in boxes),
    )


def group(paths, indexes, fill):
    return f'<g fill="{fill}">' + "".join(f'<path d="{paths[i].d()}"/>' for i in indexes) + "</g>"


def write(target, box, body, square=False, pad=0.0):
    x0, y0, x1, y1 = box[0] - pad, box[1] - pad, box[2] + pad, box[3] + pad
    width, height = x1 - x0, y1 - y0
    if square:
        side = max(width, height)
        x0, y0 = x0 - (side - width) / 2, y0 - (side - height) / 2
        width = height = side
    target.write_text(
        f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="{x0:.2f} {y0:.2f} {width:.2f} {height:.2f}" '
        f'width="{width:.0f}" height="{height:.0f}">{body}</svg>\n',
        encoding="utf-8",
    )


def wordmark(paths, dark, with_signature):
    hn_x0, _, hn_x1, _ = paths[HN].bbox()
    stop = (SPLIT_X - hn_x0) / (hn_x1 - hn_x0)
    gradient = (
        '<defs><linearGradient id="hn" gradientUnits="userSpaceOnUse" '
        f'x1="{hn_x0:.2f}" y1="0" x2="{hn_x1:.2f}" y2="0">'
        f'<stop offset="{stop:.4f}" stop-color="{dark}"/>'
        f'<stop offset="{stop:.4f}" stop-color="{BLUE}"/></linearGradient></defs>'
    )
    dark_parts = BAR + BRACE_LEFT + TECH + (SIGNATURE if with_signature else [])
    return (
        gradient
        + group(paths, dark_parts, dark)
        + group(paths, BRACE_RIGHT_AND_PIXELS + NUM, BLUE)
        + f'<path fill="url(#hn)" d="{paths[HN].d()}"/>'
    )


def main(source, out_dir):
    paths = load(source)
    out = FsPath(out_dir)
    out.mkdir(parents=True, exist_ok=True)
    full = bbox(paths, range(40))
    compact = bbox(paths, range(13))
    symbol = bbox(paths, BRACE_LEFT + BRACE_RIGHT_AND_PIXELS)
    write(out / "logo-technum-signature.svg", full, wordmark(paths, CHARCOAL, True))
    write(out / "logo-technum-clair-signature.svg", full, wordmark(paths, WHITE, True))
    write(out / "logo-technum.svg", compact, wordmark(paths, CHARCOAL, False))
    symbol_body = group(paths, BRACE_LEFT, CHARCOAL) + group(paths, BRACE_RIGHT_AND_PIXELS, BLUE)
    write(out / "favicon.svg", symbol, symbol_body, square=True, pad=12)
    print("logos écrits dans", out)


if __name__ == "__main__":
    main(sys.argv[1], sys.argv[2])
```

- [ ] **Étape 3 : écrire `$TOOLS/build_icons.py`**

```python
"""Produit favicon.ico, apple-touch-icon.png et le logo carré à partir de favicon.svg.

Usage : python build_icons.py public
"""
import io
import sys
from pathlib import Path

import pymupdf
from PIL import Image


def render(svg_path, size):
    document = pymupdf.open(svg_path)
    pdf = pymupdf.open("pdf", document.convert_to_pdf())
    page = pdf[0]
    zoom = size / max(page.rect.width, page.rect.height)
    pixmap = page.get_pixmap(matrix=pymupdf.Matrix(zoom, zoom), alpha=True)
    return Image.open(io.BytesIO(pixmap.tobytes("png"))).convert("RGBA")


def on_white(symbol, size, padding_ratio):
    canvas = Image.new("RGBA", (size, size), (255, 255, 255, 255))
    inner = int(size * (1 - 2 * padding_ratio))
    icon = symbol.resize((inner, inner), Image.LANCZOS)
    offset = (size - inner) // 2
    canvas.alpha_composite(icon, (offset, offset))
    return canvas.convert("RGB")


def main(public_dir):
    public = Path(public_dir)
    symbol = render(str(public / "assets/img/favicon.svg"), 1024)
    on_white(symbol, 180, 0.12).save(public / "apple-touch-icon.png", optimize=True)
    on_white(symbol, 512, 0.12).save(public / "assets/img/logo-technum-carre-512.png", optimize=True)
    symbol.resize((256, 256), Image.LANCZOS).save(public / "favicon.ico", sizes=[(16, 16), (32, 32), (48, 48)])
    print("icônes écrites")


if __name__ == "__main__":
    main(sys.argv[1])
```

- [ ] **Étape 4 : générer les logos et les icônes**

```bash
cd /c/wamp64/www/TECHNUM
TOOLS=/c/Users/jenmf/AppData/Local/Temp/technum-outils
PYTHONPATH="$TOOLS/pylib" PYTHONIOENCODING=utf-8 python "$TOOLS/build_logos.py" documentations/TECHNUM-LOGO.svg public/assets/img
PYTHONPATH="$TOOLS/pylib" PYTHONIOENCODING=utf-8 python "$TOOLS/build_icons.py" public
ls -la public/assets/img public/favicon.ico public/apple-touch-icon.png
```

Attendu : quatre fichiers SVG et un PNG dans `public/assets/img`, plus `favicon.ico` et `apple-touch-icon.png` dans `public`.

- [ ] **Étape 5 : récupérer l'icône de chaque produit**

```bash
cd /c/wamp64/www/TECHNUM
mkdir -p public/assets/img/produits
curl -fsSL -A "Mozilla/5.0" -o public/assets/img/produits/oeil360-finance-icone.png https://oeil360finance.bytechnum.com/images/oeil360-icon.png
curl -fsSL -A "Mozilla/5.0" -o public/assets/img/produits/dis-oui-icone.svg https://disoui.bytechnum.com/favicon.svg
curl -fsSL -A "Mozilla/5.0" -o public/assets/img/produits/provia-icone.svg https://provia.bytechnum.com/favicon.svg
curl -fsSL -A "Mozilla/5.0" -o public/assets/img/produits/carte-uac-icone.svg https://uacmap.bytechnum.com/icon.svg
TOOLS=/c/Users/jenmf/AppData/Local/Temp/technum-outils
PYTHONPATH="$TOOLS/pylib" python -c "from PIL import Image; p='public/assets/img/produits/oeil360-finance-icone.png'; i=Image.open(p).convert('RGBA'); i.thumbnail((56, 56), Image.LANCZOS); i.save(p, optimize=True); print(i.size)"
grep -l -i -E '<script|on[a-z]+=' public/assets/img/produits/*.svg public/assets/img/*.svg || echo "aucun script dans les SVG"
```

Attendu : quatre fichiers, l'icône PNG en 56 × 56 au plus, et le message « aucun script dans les SVG ».

- [ ] **Étape 6 : vérifier les logos dans un navigateur**

Préparer une page d'aperçu hors du dépôt :

```bash
cd /c/wamp64/www/TECHNUM
TOOLS=/c/Users/jenmf/AppData/Local/Temp/technum-outils
mkdir -p "$TOOLS/apercu"
cp public/assets/img/*.svg "$TOOLS/apercu/"
cp "documentations/TECHNUM LOGO.png" "$TOOLS/apercu/officiel.png"
cat > "$TOOLS/apercu/index.html" <<'EOF'
<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Logos</title></head>
<body style="margin:0;font-family:sans-serif">
<div style="background:#fff;padding:24px;display:flex;gap:32px;align-items:center">
<img src="logo-technum-signature.svg" width="420" alt=""><img src="logo-technum.svg" width="300" alt="">
<img src="favicon.svg" width="96" alt=""><img src="favicon.svg" width="32" alt=""></div>
<div style="background:#373536;padding:24px"><img src="logo-technum-clair-signature.svg" width="420" alt=""></div>
<div style="background:#fff;padding:24px"><img src="officiel.png" width="420" alt=""></div>
</body></html>
EOF
```

Lancer en arrière-plan `php -S 127.0.0.1:8765 -t /c/Users/jenmf/AppData/Local/Temp/technum-outils/apercu`, puis avec les outils Playwright : `browser_resize` 1200 × 700, `browser_navigate` vers `http://127.0.0.1:8765/index.html`, `browser_take_screenshot` vers `c:\wamp64\www\TECHNUM\.playwright-mcp\logos.png`, et lire l'image.

Attendu : sur chaque version, la jambe gauche et la barre du H suivent la couleur de TECH, la jambe droite du H et NUM sont bleues, comme sur le PNG officiel en bas de page. La version claire reste lisible sur fond Charcoal et l'accolade du favicon se reconnaît à 32 px. Arrêter ensuite le serveur et fermer le navigateur.

- [ ] **Étape 7 : committer**

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .playwright-mcp
git add public/assets/img public/favicon.ico public/apple-touch-icon.png
git commit -F - <<'EOF'
feat(assets): ajoute les déclinaisons du logo et les icônes des produits
EOF
```

### Tâche 8 : polices et captures des produits

**Fichiers :**

- Créer : `public/assets/fonts/montserrat-600.woff2`, `public/assets/fonts/montserrat-700.woff2`, `public/assets/fonts/poppins-400.woff2`, `public/assets/fonts/poppins-500.woff2`, `public/assets/fonts/LICENSE-montserrat.txt`, `public/assets/fonts/LICENSE-poppins.txt`, `public/assets/img/produits/oeil360-finance-1280.webp`, `public/assets/img/produits/oeil360-finance-640.webp`, `public/assets/img/produits/provia-1280.webp`, `public/assets/img/produits/provia-640.webp`, `public/assets/img/produits/carte-uac-390.webp`, `public/assets/img/produits/dis-oui-420.webp`

**Interfaces :**

- Produit : les polices pour `site.css` (tâche 13) et les captures pour `content/products.php` (tâche 10), aux dimensions exactes 1280 × 800, 640 × 400, 390 × 844 et 420 × 720.

- [ ] **Étape 1 : télécharger les polices et leurs licences**

```bash
cd /c/wamp64/www/TECHNUM
mkdir -p public/assets/fonts
BASE=https://cdn.jsdelivr.net/npm/@fontsource
curl -fsSL -o public/assets/fonts/montserrat-600.woff2 "$BASE/montserrat@5/files/montserrat-latin-600-normal.woff2"
curl -fsSL -o public/assets/fonts/montserrat-700.woff2 "$BASE/montserrat@5/files/montserrat-latin-700-normal.woff2"
curl -fsSL -o public/assets/fonts/poppins-400.woff2 "$BASE/poppins@5/files/poppins-latin-400-normal.woff2"
curl -fsSL -o public/assets/fonts/poppins-500.woff2 "$BASE/poppins@5/files/poppins-latin-500-normal.woff2"
curl -fsSL -o public/assets/fonts/LICENSE-montserrat.txt "$BASE/montserrat@5/LICENSE"
curl -fsSL -o public/assets/fonts/LICENSE-poppins.txt "$BASE/poppins@5/LICENSE"
for f in public/assets/fonts/*.woff2; do printf "%s " "$f"; head -c 4 "$f"; echo; done
```

Attendu : chaque fichier woff2 commence par `wOF2`, entre 7 et 20 Ko chacun.

- [ ] **Étape 2 : capturer PROVIA**

Avec les outils Playwright : `browser_resize` 1280 × 800, `browser_navigate` vers `https://provia.bytechnum.com/`, `browser_wait_for` 2 secondes, `browser_take_screenshot` avec `scale` à `css` vers `c:\wamp64\www\TECHNUM\.playwright-mcp\provia.png`.

- [ ] **Étape 3 : capturer la démonstration d'Oeil 360° Finance**

La page d'accueil d'Oeil 360° Finance contient une démonstration qui tourne dans le navigateur, sans toucher aux données réelles. `browser_navigate` vers `https://oeil360finance.bytechnum.com/`, puis `browser_snapshot` pour repérer, dans la section « Essayez maintenant », les champs « Montant (XOF) », « Sens », « Catégorie » et le bouton « Ajouter ». Ajouter quatre entrées fictives dans cet ordre :

| Montant | Sens | Catégorie |
| --- | --- | --- |
| 350000 | Revenu (entrée) | Salaire |
| 85000 | Dépense (sortie) | Alimentation |
| 30000 | Dépense (sortie) | Transport |
| 25000 | Dépense (sortie) | Loisirs |

Placer ensuite la section en haut de l'écran avec `browser_evaluate` :

```js
() => {
  const heading = [...document.querySelectorAll('h2, h3')].find((node) => node.textContent.includes('Essayez maintenant'));
  if (!heading) {
    return false;
  }
  heading.scrollIntoView({ block: 'start' });
  window.scrollBy(0, -24);
  return true;
}
```

Attendu : `true`. `browser_take_screenshot` vers `c:\wamp64\www\TECHNUM\.playwright-mcp\oeil360.png`, puis lire l'image : le solde fictif, la répartition des dépenses et les dernières entrées doivent être visibles. Si une partie déborde, ajuster le défilement avec `window.scrollBy` et reprendre la capture.

- [ ] **Étape 4 : capturer la Carte UAC sur téléphone**

`browser_resize` 390 × 844, `browser_navigate` vers `https://uacmap.bytechnum.com/`, `browser_wait_for` 5 secondes pour le fond de carte, `browser_take_screenshot` vers `c:\wamp64\www\TECHNUM\.playwright-mcp\carte-uac.png`, puis `browser_close`.

- [ ] **Étape 5 : reprendre la capture réelle de Dis oui**

```bash
cd /c/wamp64/www/TECHNUM
cp /c/wamp64/www/dis-oui/docs/images/theme-bytechnum.png .playwright-mcp/dis-oui.png
```

Cette capture vient du dépôt du produit : l'invitation au thème TECHNUM, sur téléphone. Créer une vraie invitation en production pour la capturer écrirait dans la base de Dis oui, ce qui est exclu.

- [ ] **Étape 6 : écrire `$TOOLS/build_screenshots.py`**

```python
"""Convertit les captures des produits en WebP aux tailles attendues par le site.

Usage : python build_screenshots.py .playwright-mcp public/assets/img/produits
"""
import sys
from pathlib import Path

from PIL import Image, ImageOps

SOURCES = {
    "oeil360-finance": ("oeil360.png", [(1280, 800), (640, 400)]),
    "provia": ("provia.png", [(1280, 800), (640, 400)]),
    "carte-uac": ("carte-uac.png", [(390, 844)]),
    "dis-oui": ("dis-oui.png", [(420, 720)]),
}


def main(source_dir, out_dir):
    source, out = Path(source_dir), Path(out_dir)
    out.mkdir(parents=True, exist_ok=True)
    for slug, (name, sizes) in SOURCES.items():
        image = Image.open(source / name).convert("RGB")
        for width, height in sizes:
            fitted = ImageOps.fit(image, (width, height), Image.LANCZOS, centering=(0.5, 0.0))
            target = out / f"{slug}-{width}.webp"
            fitted.save(target, "WEBP", quality=82, method=6)
            print(target.name, fitted.size, target.stat().st_size // 1024, "Ko")


if __name__ == "__main__":
    main(sys.argv[1], sys.argv[2])
```

- [ ] **Étape 7 : convertir, contrôler, committer**

```bash
cd /c/wamp64/www/TECHNUM
TOOLS=/c/Users/jenmf/AppData/Local/Temp/technum-outils
PYTHONPATH="$TOOLS/pylib" PYTHONIOENCODING=utf-8 python "$TOOLS/build_screenshots.py" .playwright-mcp public/assets/img/produits
```

Attendu : six fichiers WebP aux tailles listées, chacun sous 200 Ko. Lire chaque fichier `-1280`, `-390` et `-420` pour confirmer qu'il montre le bon produit sans bandeau parasite.

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .playwright-mcp
git add public/assets/fonts public/assets/img/produits
git commit -F - <<'EOF'
feat(assets): ajoute les polices et les captures réelles des produits
EOF
```

### Tâche 9 : typographie française et modèle de contenu

**Fichiers :**

- Créer : `src/Content/Typography.php`, `src/Content/ProductStage.php`, `src/Content/SiteInfo.php`, `src/Content/ProductImage.php`, `src/Content/Product.php`, `src/Content/Project.php`, `src/Content/ServiceExample.php`, `src/Content/Service.php`, `tests/Unit/Content/TypographyTest.php`, `tests/Unit/Content/ProductStageTest.php`, `tests/Unit/Content/SiteInfoTest.php`, `tests/Unit/Content/ContentModelTest.php`

**Interfaces :**

- Produit :
  - `Typography::french(string $text): string` place une espace insécable U+00A0 avant `: ; ! ? »` et après `«`.
  - `enum ProductStage: string` avec les cas `Concept = 'conception'`, `Pilot = 'pilote'`, `Beta = 'beta'`, `Live = 'en-service'`, les méthodes `label(): string`, `position(): int` (1 à 4) et `static ordered(): list<ProductStage>`.
  - `SiteInfo(string $email, string $phoneDisplay, string $phoneE164, string $whatsappNumber, string $whatsappMessage, string $githubUrl, string $city, DateTimeImmutable $updatedAt)` avec `whatsappUrl()`, `phoneUrl()`, `emailUrl()`, `githubLabel()`, `updatedAtLabel()`.
  - `ProductImage(string $src, string $srcSmall, int $width, int $height, string $alt, string $frame)`. `srcSmall` vaut `''` ou désigne une image de largeur moitié.
  - `Product(string $slug, string $name, string $url, string $tagline, string $audience, ProductStage $stage, string $done, string $next, string $note, string $icon, ProductImage $image)` avec `host()`, `trackedUrl()`, `anchor()`, `isStable()`.
  - `Project(string $slug, string $name, string $description, string $nature, string $linkUrl, string $linkLabel)` avec `anchor()` et `hasLink()`.
  - `ServiceExample(string $label, string $href)` avec `hasLink()`. `Service(string $name, string $description, list<ServiceExample> $examples)` avec `examplesLabel()`.

- [ ] **Étape 1 : écrire les tests qui échouent**

`tests/Unit/Content/TypographyTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Content\Typography;

final class TypographyTest extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function samples(): iterable
    {
        yield 'deux-points' => ['Exemple : texte', "Exemple\u{00A0}: texte"];
        yield 'guillemets' => ['« Vous êtes ici »', "«\u{00A0}Vous êtes ici\u{00A0}»"];
        yield 'point d\'interrogation' => ['Vraiment ?', "Vraiment\u{00A0}?"];
        yield 'espaces multiples' => ['Déjà  : fait', "Déjà\u{00A0}: fait"];
        yield 'adresse web intacte' => ['https://bytechnum.com', 'https://bytechnum.com'];
        yield 'heure intacte' => ['À 10:30', 'À 10:30'];
    }

    #[DataProvider('samples')]
    public function testFrenchSpacingIsApplied(string $input, string $expected): void
    {
        self::assertSame($expected, Typography::french($input));
    }
}
```

`tests/Unit/Content/ProductStageTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\TestCase;
use Technum\Content\ProductStage;

final class ProductStageTest extends TestCase
{
    public function testStagesAreOrderedWithLabelsAndPositions(): void
    {
        $stages = ProductStage::ordered();

        self::assertSame(['Conception', 'Pilote', 'Bêta', 'En service'], array_map(static fn (ProductStage $stage): string => $stage->label(), $stages));
        self::assertSame([1, 2, 3, 4], array_map(static fn (ProductStage $stage): int => $stage->position(), $stages));
    }

    public function testStageIsReadFromContentValue(): void
    {
        self::assertSame(ProductStage::Beta, ProductStage::from('beta'));
        self::assertSame(ProductStage::Live, ProductStage::from('en-service'));
        self::assertNull(ProductStage::tryFrom('lancé'));
    }
}
```

`tests/Unit/Content/SiteInfoTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Technum\Content\SiteInfo;

final class SiteInfoTest extends TestCase
{
    private static function site(string $date = '2026-10-02'): SiteInfo
    {
        return new SiteInfo(
            email: 'elisee.atonde@bytechnum.com',
            phoneDisplay: '+229 01 50 61 73 00',
            phoneE164: '+2290150617300',
            whatsappNumber: '2290150617300',
            whatsappMessage: "Bonjour TECHNUM, je souhaite vous parler d'un projet.",
            githubUrl: 'https://github.com/Magloire04',
            city: 'Porto-Novo, Bénin',
            updatedAt: new DateTimeImmutable($date),
        );
    }

    public function testContactLinks(): void
    {
        $site = self::site();

        self::assertSame(
            'https://wa.me/2290150617300?text=Bonjour%20TECHNUM%2C%20je%20souhaite%20vous%20parler%20d%27un%20projet.',
            $site->whatsappUrl(),
        );
        self::assertSame('tel:+2290150617300', $site->phoneUrl());
        self::assertSame('mailto:elisee.atonde@bytechnum.com', $site->emailUrl());
        self::assertSame('github.com/Magloire04', $site->githubLabel());
    }

    public function testUpdateDateIsWrittenInFrench(): void
    {
        self::assertSame('2 octobre 2026', self::site('2026-10-02')->updatedAtLabel());
        self::assertSame('1er août 2026', self::site('2026-08-01')->updatedAtLabel());
    }
}
```

`tests/Unit/Content/ContentModelTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\TestCase;
use Technum\Content\Product;
use Technum\Content\ProductImage;
use Technum\Content\ProductStage;
use Technum\Content\Project;
use Technum\Content\Service;
use Technum\Content\ServiceExample;

final class ContentModelTest extends TestCase
{
    private static function product(string $next): Product
    {
        return new Product(
            slug: 'oeil360-finance',
            name: 'Oeil 360° Finance',
            url: 'https://oeil360finance.bytechnum.com',
            tagline: 'Suivre ses comptes.',
            audience: 'Particuliers.',
            stage: ProductStage::Live,
            done: 'Comptes multiples.',
            next: $next,
            note: '',
            icon: 'img/produits/oeil360-finance-icone.png',
            image: new ProductImage('img/a.webp', '', 1280, 800, 'Capture', 'desktop'),
        );
    }

    public function testProductLinksAndAnchor(): void
    {
        $product = self::product('');

        self::assertSame('oeil360finance.bytechnum.com', $product->host());
        self::assertSame('https://oeil360finance.bytechnum.com/?ref=bytechnum', $product->trackedUrl());
        self::assertSame('produit-oeil360-finance', $product->anchor());
    }

    public function testProductWithoutNextStepIsStable(): void
    {
        self::assertTrue(self::product('')->isStable());
        self::assertFalse(self::product('Version mobile.')->isStable());
    }

    public function testProjectLink(): void
    {
        $withLink = new Project('tracacajou', 'TraçaCajou', 'Certificats.', 'Preuve de concept', 'https://github.com/Magloire04/TracaCajou', 'Voir le dépôt');
        $withoutLink = new Project('bescat', 'BESCAT', 'Refonte.', 'Mandat client', '', '');

        self::assertSame('realisation-tracacajou', $withLink->anchor());
        self::assertTrue($withLink->hasLink());
        self::assertFalse($withoutLink->hasLink());
    }

    public function testServiceExamplesLabelFollowsCount(): void
    {
        $one = new Service('Applications de gestion', 'Des outils.', [new ServiceExample('Oeil 360° Finance', '#produit-oeil360-finance')]);
        $two = new Service('Plateformes en ligne', 'Des services.', [new ServiceExample('PROVIA', '#produit-provia'), new ServiceExample('BESCAT', '')]);

        self::assertSame('Exemple', $one->examplesLabel());
        self::assertSame('Exemples', $two->examplesLabel());
        self::assertFalse($two->examples[1]->hasLink());
    }
}
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'TypographyTest|ProductStageTest|SiteInfoTest|ContentModelTest'
```

Attendu : échec, classes du namespace `Technum\Content` introuvables.

- [ ] **Étape 3 : écrire `src/Content/Typography.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

final class Typography
{
    private const NBSP = "\u{00A0}";

    /**
     * Espaces insécables de la typographie française : avant « : ; ! ? » » et après « « ».
     */
    public static function french(string $text): string
    {
        $text = preg_replace('/[ \x{00A0}]+([:;!?»])/u', self::NBSP . '$1', $text) ?? $text;

        return preg_replace('/«[ \x{00A0}]+/u', '«' . self::NBSP, $text) ?? $text;
    }
}
```

- [ ] **Étape 4 : écrire `src/Content/ProductStage.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

enum ProductStage: string
{
    case Concept = 'conception';
    case Pilot = 'pilote';
    case Beta = 'beta';
    case Live = 'en-service';

    public function label(): string
    {
        return match ($this) {
            self::Concept => 'Conception',
            self::Pilot => 'Pilote',
            self::Beta => 'Bêta',
            self::Live => 'En service',
        };
    }

    /**
     * Position sur la piste d'état, de 1 à 4.
     */
    public function position(): int
    {
        return match ($this) {
            self::Concept => 1,
            self::Pilot => 2,
            self::Beta => 3,
            self::Live => 4,
        };
    }

    /**
     * @return list<self>
     */
    public static function ordered(): array
    {
        return [self::Concept, self::Pilot, self::Beta, self::Live];
    }
}
```

- [ ] **Étape 5 : écrire `src/Content/SiteInfo.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

use DateTimeImmutable;

final class SiteInfo
{
    private const MONTHS = [
        1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre',
    ];

    public function __construct(
        public readonly string $email,
        public readonly string $phoneDisplay,
        public readonly string $phoneE164,
        public readonly string $whatsappNumber,
        public readonly string $whatsappMessage,
        public readonly string $githubUrl,
        public readonly string $city,
        public readonly DateTimeImmutable $updatedAt,
    ) {
    }

    public function whatsappUrl(): string
    {
        return 'https://wa.me/' . $this->whatsappNumber . '?text=' . rawurlencode($this->whatsappMessage);
    }

    public function phoneUrl(): string
    {
        return 'tel:' . $this->phoneE164;
    }

    public function emailUrl(): string
    {
        return 'mailto:' . $this->email;
    }

    public function githubLabel(): string
    {
        return (string) preg_replace('#^https://#', '', $this->githubUrl);
    }

    public function updatedAtLabel(): string
    {
        $day = (int) $this->updatedAt->format('j');

        return ($day === 1 ? '1er' : (string) $day)
            . ' ' . self::MONTHS[(int) $this->updatedAt->format('n')]
            . ' ' . $this->updatedAt->format('Y');
    }
}
```

- [ ] **Étape 6 : écrire `src/Content/ProductImage.php` et `src/Content/Product.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

/**
 * Capture d'un produit. srcSmall vaut '' ou désigne la même image en largeur moitié.
 */
final class ProductImage
{
    public function __construct(
        public readonly string $src,
        public readonly string $srcSmall,
        public readonly int $width,
        public readonly int $height,
        public readonly string $alt,
        public readonly string $frame,
    ) {
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

final class Product
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly string $url,
        public readonly string $tagline,
        public readonly string $audience,
        public readonly ProductStage $stage,
        public readonly string $done,
        public readonly string $next,
        public readonly string $note,
        public readonly string $icon,
        public readonly ProductImage $image,
    ) {
    }

    public function host(): string
    {
        return (string) parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Adresse du produit marquée pour repérer, dans ses journaux, les visites venues de bytechnum.com.
     */
    public function trackedUrl(): string
    {
        return rtrim($this->url, '/') . '/?ref=bytechnum';
    }

    public function anchor(): string
    {
        return 'produit-' . $this->slug;
    }

    public function isStable(): bool
    {
        return $this->next === '';
    }
}
```

- [ ] **Étape 7 : écrire `src/Content/Project.php`, `src/Content/ServiceExample.php` et `src/Content/Service.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

final class Project
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly string $description,
        public readonly string $nature,
        public readonly string $linkUrl,
        public readonly string $linkLabel,
    ) {
    }

    public function anchor(): string
    {
        return 'realisation-' . $this->slug;
    }

    public function hasLink(): bool
    {
        return $this->linkUrl !== '';
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

final class ServiceExample
{
    public function __construct(
        public readonly string $label,
        public readonly string $href,
    ) {
    }

    public function hasLink(): bool
    {
        return $this->href !== '';
    }
}
```

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

final class Service
{
    /**
     * @param list<ServiceExample> $examples
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $examples,
    ) {
    }

    public function examplesLabel(): string
    {
        return count($this->examples) > 1 ? 'Exemples' : 'Exemple';
    }
}
```

- [ ] **Étape 8 : relancer les tests et l'outillage, puis committer**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
git add src/Content tests/Unit/Content
git commit -F - <<'EOF'
feat(content): ajoute le modèle de contenu et la typographie française
EOF
```

Attendu : tous les tests verts avant le commit.

### Tâche 10 : chargement validé et fichiers de contenu

**Fichiers :**

- Créer : `src/Content/ContentRepository.php`, `src/Content/ContentException.php`, `content/site.php`, `content/products.php`, `content/projects.php`, `content/services.php`, `tests/Support/TempDirectory.php`, `tests/Unit/Content/ContentRepositoryTest.php`, `tests/Unit/Content/ContentFilesTest.php`

**Interfaces :**

- Consomme : les classes de la tâche 9.
- Produit : `Technum\Content\ContentRepository` avec `__construct(string $contentDir, string $publicDir)`, `site(): SiteInfo`, `products(): list<Product>`, `projects(): list<Project>`, `services(): list<Service>`. Chaque méthode met son résultat en mémoire. Les champs de prose passent par `Typography::french()`. Toute anomalie lève `ContentException` avec un message qui nomme le fichier, l'entrée et le champ. `Technum\Tests\Support\TempDirectory` avec `static create(string $prefix): string` et `static remove(string $directory): void`.

- [ ] **Étape 1 : écrire `tests/Support/TempDirectory.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Support;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class TempDirectory
{
    public static function create(string $prefix): string
    {
        $directory = sys_get_temp_dir() . '/' . $prefix . '-' . bin2hex(random_bytes(4));
        mkdir($directory, 0777, true);

        return $directory;
    }

    public static function remove(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($items as $item) {
            if (!$item instanceof SplFileInfo) {
                continue;
            }
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($directory);
    }
}
```

- [ ] **Étape 2 : écrire le test qui échoue, `tests/Unit/Content/ContentRepositoryTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Content\ContentException;
use Technum\Content\ContentRepository;
use Technum\Content\ProductStage;
use Technum\Tests\Support\TempDirectory;

final class ContentRepositoryTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = TempDirectory::create('technum-content');
        mkdir($this->root . '/content');
        mkdir($this->root . '/public/assets/img', 0777, true);
        touch($this->root . '/public/assets/img/capture.webp');
        touch($this->root . '/public/assets/img/icone.svg');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->root);
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function write(string $name, array $data): void
    {
        file_put_contents($this->root . '/content/' . $name . '.php', "<?php\n\nreturn " . var_export($data, true) . ";\n");
    }

    private function repository(): ContentRepository
    {
        return new ContentRepository($this->root . '/content', $this->root . '/public');
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private static function product(array $overrides = []): array
    {
        return [...[
            'slug' => 'exemple',
            'name' => 'Exemple',
            'url' => 'https://exemple.bytechnum.com',
            'tagline' => 'Une accroche : simple.',
            'audience' => 'Tout le monde.',
            'stage' => 'beta',
            'done' => 'Déjà fait.',
            'next' => 'À venir.',
            'note' => '',
            'icon' => 'img/icone.svg',
            'image' => self::image(),
        ], ...$overrides];
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private static function image(array $overrides = []): array
    {
        return [...[
            'src' => 'img/capture.webp',
            'srcSmall' => '',
            'width' => 1280,
            'height' => 800,
            'alt' => 'Capture',
            'frame' => 'desktop',
        ], ...$overrides];
    }

    public function testBuildsProductsWithFrenchTypography(): void
    {
        $this->write('products', [self::product()]);

        $product = $this->repository()->products()[0];

        self::assertSame('exemple', $product->slug);
        self::assertSame(ProductStage::Beta, $product->stage);
        self::assertSame("Une accroche\u{00A0}: simple.", $product->tagline);
        self::assertSame('img/capture.webp', $product->image->src);
    }

    /** @return iterable<string, array{array<string, mixed>, string}> */
    public static function invalidProducts(): iterable
    {
        yield 'adresse http' => [['url' => 'http://exemple.bytechnum.com'], '« url » doit être une adresse https valide'];
        yield 'état inconnu' => [['stage' => 'lancé'], '« stage » doit valoir conception, pilote, beta ou en-service'];
        yield 'nom vide' => [['name' => ' '], '« name » est vide'];
        yield 'icône absente' => [['icon' => 'img/absente.svg'], 'fichier introuvable, assets/img/absente.svg'];
        yield 'slug invalide' => [['slug' => 'Mon Produit'], '« slug » ne contient que des minuscules'];
        yield 'cadre inconnu' => [['image' => self::image(['frame' => 'tablette'])], '« frame » doit valoir desktop ou phone'];
        yield 'texte alternatif vide' => [['image' => self::image(['alt' => ''])], '« alt » est vide'];
        yield 'largeur nulle' => [['image' => self::image(['width' => 0])], '« width » doit être un entier positif'];
    }

    /**
     * @param array<string, mixed> $overrides
     */
    #[DataProvider('invalidProducts')]
    public function testRejectsInvalidProducts(array $overrides, string $expected): void
    {
        $this->write('products', [self::product($overrides)]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage($expected);

        $this->repository()->products();
    }

    public function testRejectsMissingFile(): void
    {
        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('Fichier de contenu introuvable : products.php');

        $this->repository()->products();
    }

    public function testRejectsEmptyList(): void
    {
        $this->write('products', []);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('products.php doit renvoyer une liste non vide');

        $this->repository()->products();
    }

    public function testProjectLinkNeedsALabel(): void
    {
        $this->write('projects', [[
            'slug' => 'exemple',
            'name' => 'Exemple',
            'description' => 'Description.',
            'nature' => 'Preuve de concept',
            'linkUrl' => 'https://github.com/Magloire04/exemple',
            'linkLabel' => '',
        ]]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('« linkUrl » et « linkLabel » vont ensemble');

        $this->repository()->projects();
    }

    public function testServiceExamplesMustBePageAnchors(): void
    {
        $this->write('services', [[
            'name' => 'Service',
            'description' => 'Description.',
            'examples' => [['label' => 'Ailleurs', 'href' => 'https://ailleurs.test']],
        ]]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('« href » doit être une ancre de la page');

        $this->repository()->services();
    }

    public function testSiteDateMustUseIsoFormat(): void
    {
        $this->write('site', [
            'email' => 'elisee.atonde@bytechnum.com',
            'phoneDisplay' => '+229 01 50 61 73 00',
            'phoneE164' => '+2290150617300',
            'whatsappNumber' => '2290150617300',
            'whatsappMessage' => 'Bonjour.',
            'githubUrl' => 'https://github.com/Magloire04',
            'city' => 'Porto-Novo, Bénin',
            'updatedAt' => '02/10/2026',
        ]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('« updatedAt » doit suivre le format AAAA-MM-JJ');

        $this->repository()->site();
    }

    public function testContentIsLoadedOnlyOnce(): void
    {
        $this->write('products', [self::product()]);
        $repository = $this->repository();
        $repository->products();

        unlink($this->root . '/content/products.php');

        self::assertCount(1, $repository->products());
    }
}
```

- [ ] **Étape 3 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter ContentRepositoryTest
```

Attendu : échec, classe `ContentRepository` introuvable.

- [ ] **Étape 4 : écrire `src/Content/ContentException.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

use RuntimeException;

final class ContentException extends RuntimeException
{
}
```

- [ ] **Étape 5 : écrire `src/Content/ContentRepository.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Content;

use DateTimeImmutable;

/**
 * Charge et valide le contenu éditorial de content/*.php.
 */
final class ContentRepository
{
    private ?SiteInfo $site = null;

    /** @var list<Product>|null */
    private ?array $products = null;

    /** @var list<Project>|null */
    private ?array $projects = null;

    /** @var list<Service>|null */
    private ?array $services = null;

    public function __construct(
        private readonly string $contentDir,
        private readonly string $publicDir,
    ) {
    }

    public function site(): SiteInfo
    {
        return $this->site ??= $this->buildSite($this->load('site'));
    }

    /**
     * @return list<Product>
     */
    public function products(): array
    {
        return $this->products ??= array_map($this->buildProduct(...), $this->loadList('products'));
    }

    /**
     * @return list<Project>
     */
    public function projects(): array
    {
        return $this->projects ??= array_map($this->buildProject(...), $this->loadList('projects'));
    }

    /**
     * @return list<Service>
     */
    public function services(): array
    {
        return $this->services ??= array_map($this->buildService(...), $this->loadList('services'));
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function buildSite(array $data): SiteInfo
    {
        $context = 'site.php';
        $updatedAt = DateTimeImmutable::createFromFormat('!Y-m-d', $this->text($data, 'updatedAt', $context));
        if ($updatedAt === false) {
            throw new ContentException($context . ' : « updatedAt » doit suivre le format AAAA-MM-JJ');
        }
        $email = $this->text($data, 'email', $context);
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new ContentException($context . ' : « email » doit être une adresse valide');
        }
        $phoneE164 = $this->text($data, 'phoneE164', $context);
        if (preg_match('/^\+[0-9]{8,15}$/', $phoneE164) !== 1) {
            throw new ContentException($context . ' : « phoneE164 » doit ressembler à +2290150617300');
        }
        $whatsappNumber = $this->text($data, 'whatsappNumber', $context);
        if (preg_match('/^[0-9]{8,15}$/', $whatsappNumber) !== 1) {
            throw new ContentException($context . ' : « whatsappNumber » ne contient que des chiffres, sans +');
        }

        return new SiteInfo(
            email: $email,
            phoneDisplay: $this->text($data, 'phoneDisplay', $context),
            phoneE164: $phoneE164,
            whatsappNumber: $whatsappNumber,
            whatsappMessage: $this->text($data, 'whatsappMessage', $context),
            githubUrl: $this->httpsUrl($data, 'githubUrl', $context),
            city: $this->text($data, 'city', $context),
            updatedAt: $updatedAt,
        );
    }

    /**
     * @param array<array-key, mixed> $item
     */
    private function buildProduct(array $item): Product
    {
        $slug = $this->slug($item, 'products.php');
        $context = 'products.php, ' . $slug;
        $stage = ProductStage::tryFrom($this->text($item, 'stage', $context));
        if ($stage === null) {
            throw new ContentException($context . ' : « stage » doit valoir conception, pilote, beta ou en-service');
        }
        $image = $item['image'] ?? null;
        if (!is_array($image)) {
            throw new ContentException($context . ' : « image » doit être un tableau');
        }

        return new Product(
            slug: $slug,
            name: $this->text($item, 'name', $context),
            url: $this->httpsUrl($item, 'url', $context),
            tagline: $this->prose($item, 'tagline', $context),
            audience: $this->prose($item, 'audience', $context),
            stage: $stage,
            done: $this->prose($item, 'done', $context),
            next: $this->prose($item, 'next', $context, required: false),
            note: $this->prose($item, 'note', $context, required: false),
            icon: $this->asset($item, 'icon', $context),
            image: $this->buildImage($image, $context),
        );
    }

    /**
     * @param array<array-key, mixed> $image
     */
    private function buildImage(array $image, string $context): ProductImage
    {
        $context .= ', image';
        $frame = $this->text($image, 'frame', $context);
        if (!in_array($frame, ['desktop', 'phone'], true)) {
            throw new ContentException($context . ' : « frame » doit valoir desktop ou phone');
        }
        $srcSmall = $this->text($image, 'srcSmall', $context, required: false);

        return new ProductImage(
            src: $this->asset($image, 'src', $context),
            srcSmall: $srcSmall === '' ? '' : $this->asset($image, 'srcSmall', $context),
            width: $this->positiveInt($image, 'width', $context),
            height: $this->positiveInt($image, 'height', $context),
            alt: $this->text($image, 'alt', $context),
            frame: $frame,
        );
    }

    /**
     * @param array<array-key, mixed> $item
     */
    private function buildProject(array $item): Project
    {
        $slug = $this->slug($item, 'projects.php');
        $context = 'projects.php, ' . $slug;
        $linkUrl = $this->httpsUrl($item, 'linkUrl', $context, required: false);
        $linkLabel = $this->text($item, 'linkLabel', $context, required: false);
        if (($linkUrl === '') !== ($linkLabel === '')) {
            throw new ContentException($context . ' : « linkUrl » et « linkLabel » vont ensemble');
        }

        return new Project(
            slug: $slug,
            name: $this->text($item, 'name', $context),
            description: $this->prose($item, 'description', $context),
            nature: $this->text($item, 'nature', $context),
            linkUrl: $linkUrl,
            linkLabel: $linkLabel,
        );
    }

    /**
     * @param array<array-key, mixed> $item
     */
    private function buildService(array $item): Service
    {
        $name = $this->text($item, 'name', 'services.php');
        $context = 'services.php, ' . $name;
        $examples = $item['examples'] ?? null;
        if (!is_array($examples) || $examples === [] || !array_is_list($examples)) {
            throw new ContentException($context . ' : « examples » doit être une liste non vide');
        }
        $built = [];
        foreach ($examples as $example) {
            if (!is_array($example)) {
                throw new ContentException($context . ' : chaque exemple doit être un tableau');
            }
            $href = $this->text($example, 'href', $context, required: false);
            if ($href !== '' && preg_match('/^#[a-z0-9-]+$/', $href) !== 1) {
                throw new ContentException($context . ' : « href » doit être une ancre de la page, comme #produits');
            }
            $built[] = new ServiceExample($this->text($example, 'label', $context), $href);
        }

        return new Service($name, $this->prose($item, 'description', $context), $built);
    }

    /**
     * @return array<array-key, mixed>
     */
    private function load(string $name): array
    {
        $file = $this->contentDir . '/' . $name . '.php';
        if (!is_file($file)) {
            throw new ContentException('Fichier de contenu introuvable : ' . $name . '.php');
        }
        $data = require $file;
        if (!is_array($data)) {
            throw new ContentException($name . '.php doit renvoyer un tableau');
        }

        return $data;
    }

    /**
     * @return list<array<array-key, mixed>>
     */
    private function loadList(string $name): array
    {
        $data = $this->load($name);
        if ($data === [] || !array_is_list($data)) {
            throw new ContentException($name . '.php doit renvoyer une liste non vide');
        }
        $items = [];
        foreach ($data as $item) {
            if (!is_array($item)) {
                throw new ContentException($name . '.php : chaque entrée doit être un tableau');
            }
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function text(array $data, string $key, string $context, bool $required = true): string
    {
        $value = $data[$key] ?? ($required ? null : '');
        if (!is_string($value)) {
            throw new ContentException($context . ' : « ' . $key . ' » doit être un texte');
        }
        $value = trim($value);
        if ($required && $value === '') {
            throw new ContentException($context . ' : « ' . $key . ' » est vide');
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function prose(array $data, string $key, string $context, bool $required = true): string
    {
        return Typography::french($this->text($data, $key, $context, $required));
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function slug(array $data, string $context): string
    {
        $slug = $this->text($data, 'slug', $context);
        if (preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug) !== 1) {
            throw new ContentException($context . ' : « slug » ne contient que des minuscules, des chiffres et des tirets');
        }

        return $slug;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function httpsUrl(array $data, string $key, string $context, bool $required = true): string
    {
        $url = $this->text($data, $key, $context, $required);
        if ($url !== '' && (!str_starts_with($url, 'https://') || filter_var($url, FILTER_VALIDATE_URL) === false)) {
            throw new ContentException($context . ' : « ' . $key . ' » doit être une adresse https valide');
        }

        return $url;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function asset(array $data, string $key, string $context): string
    {
        $path = ltrim($this->text($data, $key, $context), '/');
        if (!is_file($this->publicDir . '/assets/' . $path)) {
            throw new ContentException($context . ' : fichier introuvable, assets/' . $path);
        }

        return $path;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function positiveInt(array $data, string $key, string $context): int
    {
        $value = $data[$key] ?? null;
        if (!is_int($value) || $value <= 0) {
            throw new ContentException($context . ' : « ' . $key . ' » doit être un entier positif');
        }

        return $value;
    }
}
```

- [ ] **Étape 6 : relancer le test**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter ContentRepositoryTest
```

Attendu : tous les cas verts.

- [ ] **Étape 7 : écrire `content/site.php`**

```php
<?php

declare(strict_types=1);

// Coordonnées publiques de TECHNUM et date affichée sous le registre des produits.
return [
    'email' => 'elisee.atonde@bytechnum.com',
    'phoneDisplay' => '+229 01 50 61 73 00',
    'phoneE164' => '+2290150617300',
    'whatsappNumber' => '2290150617300',
    'whatsappMessage' => "Bonjour TECHNUM, je souhaite vous parler d'un projet.",
    'githubUrl' => 'https://github.com/Magloire04',
    'city' => 'Porto-Novo, Bénin',
    'updatedAt' => '2026-10-02',
];
```

- [ ] **Étape 8 : écrire `content/products.php`**

```php
<?php

declare(strict_types=1);

// Produits ouverts au public. « next » vide : le bloc affiche « Version stable, maintenue. ».
// « srcSmall » désigne la même capture en largeur moitié, ou reste vide.
return [
    [
        'slug' => 'oeil360-finance',
        'name' => 'Oeil 360° Finance',
        'url' => 'https://oeil360finance.bytechnum.com',
        'tagline' => 'Suivre ses revenus, ses dépenses et ses comptes en franc CFA, au même endroit.',
        'audience' => 'Particuliers et indépendants qui gèrent leur argent entre caisse, Mobile Money et banque.',
        'stage' => 'en-service',
        'done' => 'Comptes multiples, transferts entre comptes, charges récurrentes, tableau de bord par période, connexion sécurisée, données traitées selon la loi n°2017-20.',
        'next' => '',
        'note' => '',
        'icon' => 'img/produits/oeil360-finance-icone.png',
        'image' => [
            'src' => 'img/produits/oeil360-finance-1280.webp',
            'srcSmall' => 'img/produits/oeil360-finance-640.webp',
            'width' => 1280,
            'height' => 800,
            'alt' => "Démonstration d'Oeil 360° Finance : solde fictif, répartition des dépenses et dernières entrées",
            'frame' => 'desktop',
        ],
    ],
    [
        'slug' => 'dis-oui',
        'name' => 'Dis oui',
        'url' => 'https://disoui.bytechnum.com',
        'tagline' => 'Transformer une demande de rendez-vous en petit jeu, avec la réponse par e-mail et le rendez-vous prêt pour le calendrier.',
        'audience' => 'Tout le monde, sans compte ni mot de passe.',
        'stage' => 'en-service',
        'done' => "Éditeur en six étapes, sept thèmes dont un thème TECHNUM pour les invitations professionnelles, partage par lien ou QR code, fichier calendrier, suppression automatique à l'échéance.",
        'next' => '',
        'note' => '',
        'icon' => 'img/produits/dis-oui-icone.svg',
        'image' => [
            'src' => 'img/produits/dis-oui-420.webp',
            'srcSmall' => '',
            'width' => 420,
            'height' => 720,
            'alt' => 'Invitation Dis oui au thème TECHNUM, affichée sur téléphone',
            'frame' => 'phone',
        ],
    ],
    [
        'slug' => 'provia',
        'name' => 'PROVIA',
        'url' => 'https://provia.bytechnum.com',
        'tagline' => 'Mettre en relation les étudiants béninois et les entreprises qui cherchent des stagiaires.',
        'audience' => 'Étudiants, recruteurs et établissements.',
        'stage' => 'beta',
        'done' => 'Profils étudiants et recruteurs, publication et consultation des offres, candidature en ligne.',
        'next' => 'Ouverture complète des inscriptions, espace étudiant complet, espace recruteur, suivi par les établissements, puis calcul de compatibilité entre profils et offres.',
        'note' => '',
        'icon' => 'img/produits/provia-icone.svg',
        'image' => [
            'src' => 'img/produits/provia-1280.webp',
            'srcSmall' => 'img/produits/provia-640.webp',
            'width' => 1280,
            'height' => 800,
            'alt' => "Page d'accueil de PROVIA, la plateforme de stages des étudiants béninois",
            'frame' => 'desktop',
        ],
    ],
    [
        'slug' => 'carte-uac',
        'name' => 'Carte UAC',
        'url' => 'https://uacmap.bytechnum.com',
        'tagline' => "Trouver son chemin à pied sur le campus d'Abomey-Calavi, jusqu'à la bonne porte, même sans réseau.",
        'audience' => 'Étudiants, parents et visiteurs du campus.',
        'stage' => 'pilote',
        'done' => 'Recherche par sigle ou surnom, itinéraire à pied avec guidage GPS, QR codes « Vous êtes ici », fonctionnement hors ligne, contributions relues avant publication.',
        'next' => 'Relevé des lieux du campus, ouverture des contributions au public.',
        'note' => "Les positions affichées aujourd'hui sont des données de démonstration.",
        'icon' => 'img/produits/carte-uac-icone.svg',
        'image' => [
            'src' => 'img/produits/carte-uac-390.webp',
            'srcSmall' => '',
            'width' => 390,
            'height' => 844,
            'alt' => "Carte UAC sur téléphone : plan du campus d'Abomey-Calavi et champ de recherche",
            'frame' => 'phone',
        ],
    ],
];
```

- [ ] **Étape 9 : écrire `content/projects.php`**

```php
<?php

declare(strict_types=1);

// Autres réalisations, reprises du profil GitHub d'Elisée. Les mandats confidentiels n'y figurent jamais.
return [
    [
        'slug' => 'tracacajou',
        'name' => 'TraçaCajou',
        'description' => "Certificats d'origine numériques pour la filière anacarde, signés et vérifiables par QR code.",
        'nature' => 'Preuve de concept',
        'linkUrl' => 'https://github.com/Magloire04/TracaCajou',
        'linkLabel' => 'Voir le dépôt',
    ],
    [
        'slug' => 'apres-mon-bac',
        'name' => 'Après mon bac',
        'description' => "Estimation des chances de bourse et aide à l'orientation des bacheliers béninois, sur 224 filières publiques.",
        'nature' => 'Produit en pause',
        'linkUrl' => 'https://github.com/Magloire04/where',
        'linkLabel' => 'Voir le dépôt',
    ],
    [
        'slug' => 'identite-numerique-cdpi',
        'name' => 'Identité numérique pour le CDPI',
        'description' => "Preuve de concept d'identité numérique décentralisée avec la suite MOSIP Inji.",
        'nature' => 'Preuve de concept',
        'linkUrl' => 'https://github.com/Magloire04/cpdi-inji-poc',
        'linkLabel' => 'Voir le dépôt',
    ],
    [
        'slug' => 'cypass',
        'name' => 'CYPASS',
        'description' => 'Plateforme de cybersécurité pour les PME africaines, co-fondée par Elisée Magloire ATONDE, qui en dirige la technique.',
        'nature' => 'Entreprise co-fondée',
        'linkUrl' => 'https://cypass.netlify.app',
        'linkLabel' => 'Voir le site',
    ],
    [
        'slug' => 'bescat',
        'name' => "BESCAT Côte d'Ivoire",
        'description' => 'Refonte du site web.',
        'nature' => 'Mandat client',
        'linkUrl' => '',
        'linkLabel' => '',
    ],
    [
        'slug' => 'e-pensionbj',
        'name' => 'e-pensionbj',
        'description' => 'Traitement automatisé de paiements de pensions, basé sur Mojaloop.',
        'nature' => 'Mandat client',
        'linkUrl' => '',
        'linkLabel' => '',
    ],
];
```

- [ ] **Étape 10 : écrire `content/services.php`**

```php
<?php

declare(strict_types=1);

// Services, chacun relié à un exemple de la page par son ancre.
return [
    [
        'name' => 'Applications de gestion',
        'description' => "Des outils adaptés à votre façon de travailler : suivi d'activité, tableaux de bord, comptes utilisateurs, exports.",
        'examples' => [
            ['label' => 'Oeil 360° Finance', 'href' => '#produit-oeil360-finance'],
        ],
    ],
    [
        'name' => 'Plateformes en ligne',
        'description' => "Des services ouverts à plusieurs publics, avec des rôles, des validations et un espace d'administration.",
        'examples' => [
            ['label' => 'PROVIA', 'href' => '#produit-provia'],
            ['label' => 'Carte UAC', 'href' => '#produit-carte-uac'],
        ],
    ],
    [
        'name' => 'Sites web et produits numériques',
        'description' => 'Sites vitrines, refontes et premières versions de produits, pensés pour le téléphone et les connexions lentes.',
        'examples' => [
            ['label' => 'Dis oui', 'href' => '#produit-dis-oui'],
            ['label' => "BESCAT Côte d'Ivoire", 'href' => '#realisation-bescat'],
        ],
    ],
    [
        'name' => 'Sécurité et conformité',
        'description' => 'Protection des données selon la loi n°2017-20, signatures électroniques, certificats vérifiables par QR code, identité numérique.',
        'examples' => [
            ['label' => 'TraçaCajou', 'href' => '#realisation-tracacajou'],
            ['label' => 'Identité numérique pour le CDPI', 'href' => '#realisation-identite-numerique-cdpi'],
        ],
    ],
    [
        'name' => 'Hébergement et suivi',
        'description' => 'Mise en ligne, sauvegardes, corrections et évolutions après la livraison.',
        'examples' => [
            ['label' => 'Nos quatre produits, que nous maintenons', 'href' => '#produits'],
        ],
    ],
];
```

- [ ] **Étape 11 : écrire `tests/Unit/Content/ContentFilesTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use FilesystemIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Technum\Content\ContentRepository;
use Technum\Content\Product;
use Technum\Content\ProductStage;

/**
 * Contrôle le contenu réel du site par rapport à la spécification validée.
 */
final class ContentFilesTest extends TestCase
{
    private const ROOT = __DIR__ . '/../../..';

    private ContentRepository $content;

    protected function setUp(): void
    {
        $this->content = new ContentRepository(self::ROOT . '/content', self::ROOT . '/public');
    }

    public function testProductsAreTheFourPublicProductsInOrder(): void
    {
        $products = $this->content->products();

        self::assertSame(
            ['oeil360-finance', 'dis-oui', 'provia', 'carte-uac'],
            array_map(static fn (Product $product): string => $product->slug, $products),
        );
        self::assertSame(
            [ProductStage::Live, ProductStage::Live, ProductStage::Beta, ProductStage::Pilot],
            array_map(static fn (Product $product): ProductStage => $product->stage, $products),
        );
    }

    public function testStableProductsHaveNoWorkInProgress(): void
    {
        $bySlug = [];
        foreach ($this->content->products() as $product) {
            $bySlug[$product->slug] = $product;
        }

        self::assertTrue($bySlug['oeil360-finance']->isStable());
        self::assertTrue($bySlug['dis-oui']->isStable());
        self::assertFalse($bySlug['provia']->isStable());
        self::assertFalse($bySlug['carte-uac']->isStable());
    }

    public function testContactDetailsMatchTheValidatedSpecification(): void
    {
        $site = $this->content->site();

        self::assertSame('elisee.atonde@bytechnum.com', $site->email);
        self::assertSame('+229 01 50 61 73 00', $site->phoneDisplay);
        self::assertSame('+2290150617300', $site->phoneE164);
        self::assertSame('2290150617300', $site->whatsappNumber);
        self::assertSame('https://github.com/Magloire04', $site->githubUrl);
    }

    public function testThereAreSixProjectsAndFiveServices(): void
    {
        self::assertCount(6, $this->content->projects());
        self::assertCount(5, $this->content->services());
    }

    public function testServiceExamplesPointToExistingAnchors(): void
    {
        $anchors = ['produits'];
        foreach ($this->content->products() as $product) {
            $anchors[] = $product->anchor();
        }
        foreach ($this->content->projects() as $project) {
            $anchors[] = $project->anchor();
        }

        foreach ($this->content->services() as $service) {
            foreach ($service->examples as $example) {
                if ($example->hasLink()) {
                    self::assertContains(ltrim($example->href, '#'), $anchors, $service->name . ', ' . $example->label);
                }
            }
        }
    }

    /**
     * Empreintes SHA-256 des mots qui désignent les deux mandats clients confidentiels.
     * Les mots eux-mêmes ne figurent nulle part dans le dépôt public.
     */
    private const CONFIDENTIAL_WORD_HASHES = [
        '196a3b33b94a4520b787250ea4a118e7bd4230cbb501b02f8cef0a816546f309',
        '12529171df4457d3621113a39f44cb35d54083b091f372df7075c666d8c56a35',
        '7567ee35c5ae9d23fa3b1eedce34aa9218f6cb7c32f28d14bcf300ef6030c538',
    ];

    public function testConfidentialWordDetectionWorks(): void
    {
        self::assertSame([hash('sha256', 'exemple')], self::matchingHashes('Un Exemple de texte.', [hash('sha256', 'exemple')]));
        self::assertSame([], self::matchingHashes('Un texte ordinaire.', [hash('sha256', 'exemple')]));
    }

    public function testPublishedFilesNeverNameTheConfidentialClients(): void
    {
        foreach (self::publishedTextFiles() as $file) {
            $found = self::matchingHashes((string) file_get_contents($file), self::CONFIDENTIAL_WORD_HASHES);
            self::assertSame([], $found, 'Mot confidentiel dans ' . $file);
        }
    }

    /** @return iterable<string, array{string}> */
    public static function forbiddenPatterns(): iterable
    {
        yield 'dossier ai-learning' => ['/ai-learning/i'];
        yield 'tiret cadratin' => ['/\x{2014}/u'];
        yield 'tiret demi-cadratin' => ['/\x{2013}/u'];
    }

    #[DataProvider('forbiddenPatterns')]
    public function testPublishedFilesNeverContainForbiddenText(string $pattern): void
    {
        foreach (self::publishedTextFiles() as $file) {
            self::assertDoesNotMatchRegularExpression($pattern, (string) file_get_contents($file), 'Texte interdit dans ' . $file);
        }
    }

    /**
     * Empreintes de la liste fournie qui correspondent à un mot du texte, sans tenir compte de la casse.
     *
     * @param list<string> $hashes
     *
     * @return list<string>
     */
    private static function matchingHashes(string $text, array $hashes): array
    {
        $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text)) ?: [];
        $wordHashes = array_map(static fn (string $word): string => hash('sha256', $word), array_unique($words));

        return array_values(array_intersect($hashes, $wordHashes));
    }

    /**
     * Fichiers texte publiés par le site, hors licences de polices tierces.
     *
     * @return list<string>
     */
    private static function publishedTextFiles(): array
    {
        $files = [];
        foreach (['content', 'templates', 'public'] as $directory) {
            $path = self::ROOT . '/' . $directory;
            if (!is_dir($path)) {
                continue;
            }
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if (!$file instanceof SplFileInfo || str_contains($file->getPathname(), 'fonts')) {
                    continue;
                }
                if (in_array($file->getExtension(), ['php', 'html', 'css', 'js', 'txt', 'xml', 'svg'], true)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }
}
```

- [ ] **Étape 12 : vérification complète, commit, pull request du lot**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
git add src/Content tests/Support tests/Unit/Content content
git commit -F - <<'EOF'
feat(content): charge et valide le contenu éditorial du site
EOF
```

Attendu : tous les tests verts, y compris `ContentFilesTest`, qui prouve que les images citées existent. Si ce test signale un tiret long dans une icône SVG téléchargée, retirer la balise `<title>` ou les métadonnées qui le contiennent, sans toucher au dessin.

Pull request du lot :

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Ressources visuelles et contenu" --body-file - <<EOF
## Objectif

Fournir les ressources visuelles et le contenu validé de la page d'accueil. Issue liée : #$ISSUE

## Changements

- Déclinaisons du logo aux couleurs officielles, favicon, icône Apple, logo carré
- Icônes et captures réelles des quatre produits, polices Montserrat et Poppins avec leurs licences
- Modèle de contenu, typographie française, chargement validé
- Fichiers de contenu : coordonnées, produits, réalisations, services

## Tests

- [x] Tests unitaires du modèle et du chargement
- [x] Contrôle du contenu réel : produits, états, coordonnées, ancres, textes interdits
- [x] Logos vérifiés dans un navigateur à côté du PNG officiel

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de secret exposé
- [x] Nommage conforme aux conventions
- [x] Taille : environ 900 lignes de code et de contenu, plus des fichiers binaires. Le contenu et sa validation forment un tout, les séparer laisserait une branche dont les tests échouent.
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

---

## Lot 3 : page d'accueil et point d'entrée

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Page d'accueil et point d'entrée" --body "Gabarits de l'accueil, assemblage de l'application, point d'entrée, pages d'erreur et configuration du serveur web." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-page-accueil"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

### Tâche 11 : gabarits de la page d'accueil

**Fichiers :**

- Créer : `templates/layout.php`, `templates/home.php`, `templates/partials/header.php`, `templates/partials/footer.php`, `templates/partials/register.php`, `templates/partials/track.php`, `templates/partials/stages.php`, `templates/partials/product.php`, `templates/partials/contact-direct.php`, `src/Page/HomePage.php`, `public/assets/css/site.css` (socle), `public/assets/js/site.js` (socle), `tests/Support/Html.php`, `tests/Unit/Page/HomePageTest.php`

**Interfaces :**

- Consomme : `View` (tâche 4), `ContentRepository` et le modèle de contenu (tâches 9 et 10).
- Produit : `Technum\Page\HomePage` avec les constantes `TITLE` et `DESCRIPTION`, `__construct(View $view, ContentRepository $content)`, `render(): string`. Les gabarits attendent les variables partagées `site` (`SiteInfo`) et `products` (`list<Product>`), et pour `home` les variables `projects` et `services`. `meta` contient `title`, `description`, `path` et, en option, `robots`. `Technum\Tests\Support\Html` avec `static parse(string $html): self`, `text(string $selector): string`, `texts(string $selector): list<string>`, `attribute(string $selector, string $name): string`, `attributes(string $selector, string $name): list<string>`, `count(string $selector): int`, `elements(string $selector): list<Dom\Element>`.

- [ ] **Étape 1 : écrire `tests/Support/Html.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Support;

use Dom\Element;
use Dom\HTMLDocument;
use RuntimeException;

/**
 * Lecture d'une page HTML rendue, par sélecteurs CSS. Les espaces insécables deviennent des espaces.
 */
final class Html
{
    private function __construct(private readonly HTMLDocument $document)
    {
    }

    public static function parse(string $html): self
    {
        return new self(HTMLDocument::createFromString($html, LIBXML_NOERROR));
    }

    public function text(string $selector): string
    {
        return self::clean($this->first($selector)->textContent ?? '');
    }

    /**
     * @return list<string>
     */
    public function texts(string $selector): array
    {
        return array_map(static fn (Element $element): string => self::clean($element->textContent ?? ''), $this->elements($selector));
    }

    public function attribute(string $selector, string $name): string
    {
        return (string) $this->first($selector)->getAttribute($name);
    }

    /**
     * @return list<string>
     */
    public function attributes(string $selector, string $name): array
    {
        return array_map(static fn (Element $element): string => (string) $element->getAttribute($name), $this->elements($selector));
    }

    public function count(string $selector): int
    {
        return count($this->elements($selector));
    }

    /**
     * @return list<Element>
     */
    public function elements(string $selector): array
    {
        $elements = [];
        foreach ($this->document->querySelectorAll($selector) as $element) {
            if ($element instanceof Element) {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    private function first(string $selector): Element
    {
        $element = $this->document->querySelector($selector);
        if ($element === null) {
            throw new RuntimeException('Élément introuvable : ' . $selector);
        }

        return $element;
    }

    private static function clean(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', str_replace(["\u{00A0}", "\u{202F}"], ' ', $text)));
    }
}
```

- [ ] **Étape 2 : écrire le test qui échoue, `tests/Unit/Page/HomePageTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Page;

use PHPUnit\Framework\TestCase;
use Technum\Content\ContentRepository;
use Technum\Page\HomePage;
use Technum\Tests\Support\Html;
use Technum\View\View;

final class HomePageTest extends TestCase
{
    private const ROOT = __DIR__ . '/../../..';

    private string $source;

    private Html $html;

    protected function setUp(): void
    {
        $content = new ContentRepository(self::ROOT . '/content', self::ROOT . '/public');
        $view = new View(self::ROOT . '/templates', self::ROOT . '/public');
        $view->share(['site' => $content->site(), 'products' => $content->products()]);
        $this->source = (new HomePage($view, $content))->render();
        $this->html = Html::parse($this->source);
    }

    public function testHeroCarriesTheBrandPromiseAndTwoActions(): void
    {
        self::assertSame('Des solutions numériques conçues pour vos réalités.', $this->html->text('h1'));
        self::assertSame('#contact', $this->html->attribute('.hero__actions .button--primary', 'href'));
        self::assertSame('#produits', $this->html->attribute('.hero__more', 'href'));
    }

    public function testRegisterListsEachProductWithItsStage(): void
    {
        self::assertSame(['Oeil 360° Finance', 'Dis oui', 'PROVIA', 'Carte UAC'], $this->html->texts('.register__name'));
        self::assertSame(['En service', 'En service', 'Bêta', 'Pilote'], $this->html->texts('.register__stage'));
        self::assertSame('Mis à jour le 2 octobre 2026', $this->html->text('.register__updated'));
        self::assertSame(3, $this->html->count('.register__item:nth-child(3) .track__bit--done'));
    }

    public function testProductBlocksShowStateAndLinkToTheProduct(): void
    {
        self::assertSame(4, $this->html->count('.product'));
        self::assertSame('Bêta', $this->html->text('#produit-provia [aria-current="step"]'));
        self::assertSame('Version stable, maintenue.', $this->html->text('#produit-oeil360-finance .product__facts div:nth-child(2) dd'));
        self::assertSame('https://provia.bytechnum.com/?ref=bytechnum', $this->html->attribute('#produit-provia .product__action', 'href'));
        self::assertStringContainsString('données de démonstration', $this->html->text('#produit-carte-uac .product__note'));
    }

    public function testProjectsServicesAndStepsAreListed(): void
    {
        self::assertSame(6, $this->html->count('.project'));
        self::assertSame(0, $this->html->count('#realisation-bescat a'));
        self::assertSame(5, $this->html->count('.service'));
        self::assertSame(5, $this->html->count('.step'));
        self::assertStringContainsString('PROVIA et Carte UAC', $this->html->text('.service:nth-child(2) .service__examples'));
    }

    public function testDirectContactUsesTheValidatedDetails(): void
    {
        self::assertStringStartsWith('https://wa.me/2290150617300?text=', $this->html->attribute('.contact-direct__action', 'href'));
        self::assertContains('tel:+2290150617300', $this->html->attributes('.contact-direct a', 'href'));
        self::assertContains('mailto:elisee.atonde@bytechnum.com', $this->html->attributes('.contact-direct a', 'href'));
    }

    public function testPageRespectsTheContentSecurityPolicy(): void
    {
        self::assertSame(0, $this->html->count('[style]'));
        self::assertSame(0, $this->html->count('style'));
        foreach ($this->html->elements('script') as $script) {
            self::assertTrue($script->hasAttribute('src') || $script->getAttribute('type') === 'application/ld+json');
        }
    }

    public function testEveryAssetReferencedByThePageExists(): void
    {
        preg_match_all('#/assets/([^"\'?\s,]+)#', $this->source, $matches);

        self::assertNotEmpty($matches[1]);
        foreach (array_unique($matches[1]) as $path) {
            self::assertFileExists(self::ROOT . '/public/assets/' . $path);
        }
    }
}
```

- [ ] **Étape 3 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomePageTest
```

Attendu : échec, classe `Technum\Page\HomePage` introuvable.

- [ ] **Étape 4 : écrire `src/Page/HomePage.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Page;

use Technum\Content\ContentRepository;
use Technum\View\View;

final class HomePage
{
    public const TITLE = 'TECHNUM, solutions numériques au Bénin';

    public const DESCRIPTION = 'TECHNUM conçoit et maintient des applications, des plateformes et des sites '
        . 'pour les entreprises et les institutions du Bénin. Découvrez nos produits en service.';

    public function __construct(
        private readonly View $view,
        private readonly ContentRepository $content,
    ) {
    }

    public function render(): string
    {
        return $this->view->renderPage('home', [
            'projects' => $this->content->projects(),
            'services' => $this->content->services(),
        ], [
            'title' => self::TITLE,
            'description' => self::DESCRIPTION,
            'path' => '/',
        ]);
    }
}
```

- [ ] **Étape 5 : écrire `templates/layout.php`**

```php
<?php
/**
 * @var Technum\View\View $view
 * @var string $content
 * @var array{title: string, description: string, path: string, robots?: string} $meta
 */
$canonical = 'https://bytechnum.com' . $meta['path'];
?>
<!doctype html>
<html lang="fr" class="no-js">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($meta['title']) ?></title>
  <meta name="description" content="<?= e($meta['description']) ?>">
<?php if (isset($meta['robots'])) : ?>
  <meta name="robots" content="<?= e($meta['robots']) ?>">
<?php else : ?>
  <link rel="canonical" href="<?= e($canonical) ?>">
<?php endif ?>
  <link rel="icon" href="/favicon.ico" sizes="48x48">
  <link rel="icon" href="<?= e($view->asset('img/favicon.svg')) ?>" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="preload" href="<?= e($view->asset('fonts/montserrat-700.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= e($view->asset('css/site.css')) ?>">
  <script src="<?= e($view->asset('js/site.js')) ?>"></script>
</head>
<body>
  <a class="skip-link" href="#contenu">Aller au contenu</a>
  <?= $view->render('partials/header') ?>
  <main id="contenu" tabindex="-1">
<?= $content ?>
  </main>
  <?= $view->render('partials/footer') ?>
</body>
</html>
```

Le script est chargé sans `defer`, dans `head` : il remplace la classe `no-js` avant l'affichage, ce qui évite un saut du menu sur téléphone. Il attend `DOMContentLoaded` pour le reste.

- [ ] **Étape 6 : écrire `templates/partials/header.php` et `templates/partials/footer.php`**

```php
<?php /** @var Technum\View\View $view */ ?>
<header class="site-header">
  <div class="site-header__inner">
    <a class="site-header__logo" href="/">
      <img src="<?= e($view->asset('img/logo-technum.svg')) ?>" alt="TECHNUM, accueil" width="148" height="41">
    </a>
    <nav class="site-nav" aria-label="Navigation principale">
      <button class="site-nav__toggle" type="button" aria-expanded="false" aria-controls="menu-principal" hidden>Menu</button>
      <ul class="site-nav__list" id="menu-principal">
        <li><a href="/#produits">Produits</a></li>
        <li><a href="/#realisations">Réalisations</a></li>
        <li><a href="/#services">Services</a></li>
        <li><a href="/#methode">Méthode</a></li>
      </ul>
    </nav>
    <a class="button button--primary site-header__cta" href="/#contact">Parler de votre projet</a>
  </div>
</header>
```

```php
<?php
/**
 * @var Technum\View\View $view
 * @var Technum\Content\SiteInfo $site
 * @var list<Technum\Content\Product> $products
 */
?>
<footer class="site-footer">
  <div class="site-footer__inner">
    <div class="site-footer__brand">
      <img src="<?= e($view->asset('img/logo-technum-clair-signature.svg')) ?>" alt="TECHNUM, la technologie à votre portée" width="220" height="73">
    </div>
    <nav class="site-footer__column" aria-labelledby="pied-produits">
      <h2 class="site-footer__title" id="pied-produits">Produits</h2>
      <ul>
        <?php foreach ($products as $product) : ?>
          <li><a href="<?= e($product->trackedUrl()) ?>"><?= e($product->name) ?></a></li>
        <?php endforeach ?>
      </ul>
    </nav>
    <div class="site-footer__column">
      <h2 class="site-footer__title">Contact</h2>
      <ul>
        <li><a href="<?= e($site->whatsappUrl()) ?>">WhatsApp</a></li>
        <li><a href="<?= e($site->phoneUrl()) ?>"><?= e($site->phoneDisplay) ?></a></li>
        <li><a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a></li>
        <li><?= e($site->city) ?></li>
      </ul>
    </div>
  </div>
  <div class="site-footer__meta">
    <p>Ce site ne dépose aucun cookie.</p>
    <p>© <?= e(date('Y')) ?> TECHNUM</p>
  </div>
</footer>
```

- [ ] **Étape 7 : écrire les petits gabarits de l'état des produits**

`templates/partials/track.php`, la piste compacte de quatre bits :

```php
<?php
/**
 * @var Technum\Content\ProductStage $stage
 * @var bool $animated
 */
?>
<span class="track<?= $animated ? ' track--animated' : '' ?>" aria-hidden="true">
  <?php foreach (\Technum\Content\ProductStage::ordered() as $step) : ?>
    <span class="track__bit<?= $step->position() <= $stage->position() ? ' track__bit--done' : '' ?>"></span>
  <?php endforeach ?>
</span>
```

`templates/partials/stages.php`, la piste complète avec le nom des étapes :

```php
<?php /** @var Technum\Content\ProductStage $stage */ ?>
<ol class="stages" role="list" aria-label="Étapes du produit">
  <?php foreach (\Technum\Content\ProductStage::ordered() as $step) : ?>
    <li class="stages__step<?= $step->position() <= $stage->position() ? ' stages__step--done' : '' ?>"<?= $step === $stage ? ' aria-current="step"' : '' ?>><?= e($step->label()) ?></li>
  <?php endforeach ?>
</ol>
```

`templates/partials/register.php`, le registre de l'accueil :

```php
<?php
/**
 * @var Technum\View\View $view
 * @var Technum\Content\SiteInfo $site
 * @var list<Technum\Content\Product> $products
 */
?>
<aside class="register" aria-labelledby="titre-registre">
  <h2 class="register__title" id="titre-registre">Nos produits aujourd'hui</h2>
  <ul class="register__list" role="list">
    <?php foreach ($products as $product) : ?>
      <li class="register__item">
        <a class="register__link" href="#<?= e($product->anchor()) ?>">
          <img class="register__icon" src="<?= e($view->asset($product->icon)) ?>" alt="" width="28" height="28">
          <span class="register__name"><?= e($product->name) ?></span>
          <?= $view->render('partials/track', ['stage' => $product->stage, 'animated' => true]) ?>
          <span class="register__stage"><?= e($product->stage->label()) ?></span>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
  <p class="register__updated">Mis à jour le <?= e($site->updatedAtLabel()) ?></p>
</aside>
```

- [ ] **Étape 8 : écrire `templates/partials/product.php`**

```php
<?php
/**
 * @var Technum\View\View $view
 * @var Technum\Content\Product $product
 */
$image = $product->image;
?>
<article class="product" id="<?= e($product->anchor()) ?>" aria-labelledby="<?= e($product->anchor()) ?>-nom">
  <figure class="product__media product__media--<?= e($image->frame) ?>">
    <div class="product__frame">
      <img src="<?= e($view->asset($image->src)) ?>"<?php if ($image->srcSmall !== '') : ?> srcset="<?= e($view->asset($image->srcSmall)) ?> <?= intdiv($image->width, 2) ?>w, <?= e($view->asset($image->src)) ?> <?= $image->width ?>w" sizes="(min-width: 60rem) 34rem, 100vw"<?php endif ?> width="<?= $image->width ?>" height="<?= $image->height ?>" alt="<?= e($image->alt) ?>" loading="lazy" decoding="async">
    </div>
    <figcaption class="product__host"><?= e($product->host()) ?></figcaption>
  </figure>
  <div class="product__text">
    <h3 class="product__name" id="<?= e($product->anchor()) ?>-nom"><?= e($product->name) ?></h3>
    <p class="product__tagline"><?= e($product->tagline) ?></p>
    <p class="product__audience">Pour qui&nbsp;: <?= e($product->audience) ?></p>
    <?= $view->render('partials/stages', ['stage' => $product->stage]) ?>
    <dl class="product__facts">
      <div>
        <dt>Déjà en place</dt>
        <dd><?= e($product->done) ?></dd>
      </div>
      <div>
        <dt>En cours</dt>
        <dd><?= $product->isStable() ? 'Version stable, maintenue.' : e($product->next) ?></dd>
      </div>
    </dl>
    <?php if ($product->note !== '') : ?>
      <p class="product__note"><?= e($product->note) ?></p>
    <?php endif ?>
    <a class="button button--secondary product__action" href="<?= e($product->trackedUrl()) ?>">Ouvrir <?= e($product->name) ?></a>
  </div>
</article>
```

- [ ] **Étape 9 : écrire `templates/partials/contact-direct.php`**

```php
<?php /** @var Technum\Content\SiteInfo $site */ ?>
<aside class="contact-direct" aria-labelledby="titre-contact-direct">
  <h3 class="contact-direct__title" id="titre-contact-direct">Vous préférez un échange direct&nbsp;?</h3>
  <a class="button button--secondary contact-direct__action" href="<?= e($site->whatsappUrl()) ?>">Écrire sur WhatsApp</a>
  <ul class="contact-direct__list" role="list">
    <li><span class="contact-direct__label">Téléphone</span><a href="<?= e($site->phoneUrl()) ?>"><?= e($site->phoneDisplay) ?></a></li>
    <li><span class="contact-direct__label">E-mail</span><a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a></li>
    <li><span class="contact-direct__label">GitHub</span><a href="<?= e($site->githubUrl) ?>"><?= e($site->githubLabel()) ?></a></li>
    <li><span class="contact-direct__label">Adresse</span><?= e($site->city) ?></li>
  </ul>
</aside>
```

- [ ] **Étape 10 : écrire `templates/home.php`**

```php
<?php
/**
 * @var Technum\View\View $view
 * @var list<Technum\Content\Product> $products
 * @var list<Technum\Content\Project> $projects
 * @var list<Technum\Content\Service> $services
 */
?>
<section class="hero" aria-labelledby="titre-accueil">
  <div class="hero__inner">
    <div class="hero__text">
      <h1 class="hero__title" id="titre-accueil">Des solutions numériques conçues pour vos réalités.</h1>
      <p class="hero__lead">TECHNUM conçoit, met en ligne et maintient des applications pour les entreprises, les institutions et les porteurs de projets du Bénin. Nos propres produits sont déjà en service&nbsp;: vous pouvez les essayer dès maintenant.</p>
      <div class="hero__actions">
        <a class="button button--primary" href="#contact">Parler de votre projet</a>
        <a class="hero__more" href="#produits">Voir nos produits</a>
      </div>
    </div>
    <?= $view->render('partials/register') ?>
  </div>
</section>

<section class="section" id="produits" aria-labelledby="titre-produits">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-produits">Nos produits</h2>
        <p class="section__context">Quatre outils conçus, hébergés et maintenus par TECHNUM, ouverts au public.</p>
      </div>
    </div>
    <div class="section__body">
      <?php foreach ($products as $product) : ?>
        <?= $view->render('partials/product', ['product' => $product]) ?>
      <?php endforeach ?>
    </div>
  </div>
</section>

<section class="section" id="realisations" aria-labelledby="titre-realisations">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-realisations">Autres réalisations</h2>
        <p class="section__context">Des preuves de concept, un produit en pause et des mandats clients.</p>
      </div>
    </div>
    <div class="section__body">
      <ul class="projects" role="list">
        <?php foreach ($projects as $project) : ?>
          <li class="project" id="<?= e($project->anchor()) ?>">
            <div class="project__head">
              <h3 class="project__name"><?= e($project->name) ?></h3>
              <p class="project__nature"><?= e($project->nature) ?></p>
            </div>
            <p class="project__description"><?= e($project->description) ?></p>
            <?php if ($project->hasLink()) : ?>
              <p><a class="project__link" href="<?= e($project->linkUrl) ?>"><?= e($project->linkLabel) ?><span class="visually-hidden"> de <?= e($project->name) ?></span></a></p>
            <?php endif ?>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</section>

<section class="section" id="services" aria-labelledby="titre-services">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-services">Ce que nous faisons pour vous</h2>
        <p class="section__context">Chaque service renvoie à un exemple que vous pouvez ouvrir.</p>
      </div>
    </div>
    <div class="section__body">
      <ul class="services" role="list">
        <?php foreach ($services as $service) : ?>
          <?php $last = count($service->examples) - 1; ?>
          <li class="service">
            <h3 class="service__name"><?= e($service->name) ?></h3>
            <p class="service__description"><?= e($service->description) ?></p>
            <p class="service__examples"><?= e($service->examplesLabel()) ?>&nbsp;:
              <?php foreach ($service->examples as $index => $example) : ?><?= $index === 0 ? '' : ($index === $last ? ' et ' : ', ') ?><?php if ($example->hasLink()) : ?><a href="<?= e($example->href) ?>"><?= e($example->label) ?></a><?php else : ?><?= e($example->label) ?><?php endif ?><?php endforeach ?>
            </p>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</section>

<section class="section" id="methode" aria-labelledby="titre-methode">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-methode">Comment se passe un projet</h2>
        <p class="section__context">Nous vous accompagnons de la conception au déploiement, puis après la livraison.</p>
      </div>
    </div>
    <div class="section__body">
      <ol class="steps" role="list">
        <li class="step">
          <h3 class="step__title">Nous écoutons le besoin réel</h3>
          <p class="step__text">Qui utilisera l'outil, pour faire quoi, sur quels appareils et avec quelle connexion.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous cadrons par écrit</h3>
          <p class="step__text">Une note de cadrage et des spécifications, validées avec vous avant le développement.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous construisons par étapes</h3>
          <p class="step__text">Une version en ligne à tester à chaque étape, sans attendre la fin.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous mettons en ligne</h3>
          <p class="step__text">Hébergement, nom de domaine, sauvegardes et conformité sont pris en charge.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous vous accompagnons</h3>
          <p class="step__text">Guide d'utilisation, corrections et évolutions après la livraison.</p>
        </li>
      </ol>
      <p class="commitments">Vos données personnelles sont traitées selon la loi n°2017-20. Chaque modification du code passe par des tests automatiques. La documentation vous est remise à la livraison.</p>
    </div>
  </div>
</section>

<section class="section" id="a-propos" aria-labelledby="titre-a-propos">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-a-propos">Qui est derrière TECHNUM</h2>
      </div>
    </div>
    <div class="section__body">
      <p class="about__text">TECHNUM est basée à Porto-Novo. Elle a été fondée par Elisée Magloire ATONDE, développeur logiciel et DevSecOps, spécialisé dans la confiance numérique&nbsp;: signatures électroniques, certificats vérifiables et protection des données.</p>
      <a class="about__link" href="https://moi.bytechnum.com">Voir le parcours du fondateur</a>
    </div>
  </div>
</section>

<section class="section section--contact" id="contact" aria-labelledby="titre-contact">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-contact">Parlons de votre projet</h2>
        <p class="section__context">Décrivez votre besoin en quelques lignes. Nous vous répondons par e-mail.</p>
      </div>
    </div>
    <div class="section__body">
      <div class="contact">
        <?= $view->render('partials/contact-direct') ?>
      </div>
    </div>
  </div>
</section>
```

- [ ] **Étape 11 : écrire le socle de `public/assets/css/site.css` et de `public/assets/js/site.js`**

Ces deux fichiers sont complétés aux tâches 13 et 14. Ils existent dès maintenant pour que la page ne référence aucun fichier absent.

`public/assets/css/site.css` :

```css
/*
 * Feuille de style de bytechnum.com, socle provisoire complété à la tâche 13.
 */

@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url('../fonts/montserrat-700.woff2') format('woff2');
}

@font-face {
  font-family: 'Poppins';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('../fonts/poppins-400.woff2') format('woff2');
}

body {
  margin: 0;
  color: #373536;
  font-family: 'Poppins', 'Segoe UI', system-ui, sans-serif;
  line-height: 1.6;
}
```

`public/assets/js/site.js` :

```js
/*
 * Améliorations progressives de bytechnum.com, socle provisoire complété à la tâche 14.
 */
(function () {
  'use strict';

  document.documentElement.classList.remove('no-js');
  document.documentElement.classList.add('js');
})();
```

- [ ] **Étape 12 : relancer les tests et l'outillage**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
```

Attendu : `HomePageTest` vert, avec ses sept tests, et tous les autres tests toujours verts.

- [ ] **Étape 13 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add templates src/Page public/assets/css public/assets/js tests/Support/Html.php tests/Unit/Page
git commit -F - <<'EOF'
feat(home): ajoute les gabarits de la page d'accueil
EOF
```

### Tâche 12 : application, point d'entrée et pages d'erreur

**Fichiers :**

- Créer : `src/Application.php`, `src/Controller/HomeController.php`, `src/Controller/ErrorController.php`, `templates/errors/404.php`, `public/index.php`, `public/.htaccess`, `public/500.html`, `tools/dev-router.php`, `storage/logs/.gitkeep`, `storage/rate-limit/.gitkeep`, `tests/Integration/ApplicationTestCase.php`, `tests/Integration/HomeRouteTest.php`
- Modifier : `.gitignore`, `phpstan.neon`, `composer.json` (phpdotenv)

**Interfaces :**

- Consomme : `Config`, `Router`, `Response`, `View`, `ContentRepository`, `HomePage`.
- Produit : `Technum\Application` avec `static create(string $rootDir, Config $config): self` et `handle(Request $request): Response`, qui ajoute `Strict-Transport-Security: max-age=31536000` en production. `HomeController::show(Request $request): Response`. `ErrorController::notFound(Request $request): Response`, statut 404, `robots` à `noindex`. Routes : `GET /`, `GET /contact` (301 vers `/#contact`), tout le reste en 404.

- [ ] **Étape 1 : installer phpdotenv**

```bash
cd /c/wamp64/www/TECHNUM
composer require vlucas/phpdotenv
```

- [ ] **Étape 2 : écrire la base des tests d'intégration, `tests/Integration/ApplicationTestCase.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Technum\Application;
use Technum\Config;
use Technum\Http\Request;
use Technum\Http\Response;

abstract class ApplicationTestCase extends TestCase
{
    protected const ROOT = __DIR__ . '/../..';

    protected const SECRET = 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk';

    /**
     * @param array<string, string> $env
     */
    protected function application(array $env = []): Application
    {
        return Application::create(self::ROOT, Config::fromArray([...self::localEnv(), ...$env]));
    }

    /**
     * @return array<string, string>
     */
    protected static function localEnv(): array
    {
        return [
            'APP_ENV' => 'local',
            'APP_SECRET' => self::SECRET,
            'MAIL_TRANSPORT' => 'log',
            'CONTACT_SENDER_EMAIL' => 'elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL' => 'elisee.atonde@bytechnum.com',
        ];
    }

    /**
     * @param array<string, string> $query
     */
    protected function get(string $path, array $query = [], ?Application $application = null): Response
    {
        $request = new Request('GET', Request::normalizePath($path), $query, [], '203.0.113.10');

        return ($application ?? $this->application())->handle($request);
    }
}
```

- [ ] **Étape 3 : écrire le test qui échoue, `tests/Integration/HomeRouteTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Tests\Support\Html;

final class HomeRouteTest extends ApplicationTestCase
{
    public function testHomePageRespondsWithSecurityHeaders(): void
    {
        $response = $this->get('/');

        self::assertSame(200, $response->status);
        self::assertSame(Response::CONTENT_SECURITY_POLICY, $response->header('Content-Security-Policy'));
        self::assertSame('no-cache, private', $response->header('Cache-Control'));
        self::assertStringContainsString('<html lang="fr"', $response->body);
        self::assertSame('https://bytechnum.com/', Html::parse($response->body)->attribute('link[rel="canonical"]', 'href'));
    }

    public function testHeadRequestIsAnswered(): void
    {
        $response = $this->application()->handle(new Request('HEAD', '/'));

        self::assertSame(200, $response->status);
    }

    public function testContactPathRedirectsToTheContactSection(): void
    {
        $response = $this->get('/contact');

        self::assertSame(301, $response->status);
        self::assertSame('/#contact', $response->header('Location'));
    }

    public function testUnknownPageIsANotFoundPageThatIsNotIndexed(): void
    {
        $response = $this->get('/page-inconnue');
        $html = Html::parse($response->body);

        self::assertSame(404, $response->status);
        self::assertSame("Cette page n'existe pas.", $html->text('h1'));
        self::assertSame('noindex', $html->attribute('meta[name="robots"]', 'content'));
        self::assertSame(0, $html->count('link[rel="canonical"]'));
    }

    public function testOnlyProductionSendsStrictTransportSecurity(): void
    {
        $production = $this->application([
            'APP_ENV' => 'production',
            'MAIL_TRANSPORT' => 'smtp',
            'SMTP_HOST' => 'smtp.example.test',
            'SMTP_PORT' => '465',
            'SMTP_USERNAME' => 'elisee.atonde@bytechnum.com',
            'SMTP_PASSWORD' => 'secret-de-test',
        ]);

        self::assertSame('max-age=31536000', $this->get('/', [], $production)->header('Strict-Transport-Security'));
        self::assertNull($this->get('/')->header('Strict-Transport-Security'));
    }
}
```

- [ ] **Étape 4 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomeRouteTest
```

Attendu : échec, classe `Technum\Application` introuvable.

- [ ] **Étape 5 : écrire les contrôleurs**

`src/Controller/HomeController.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Controller;

use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Page\HomePage;

final class HomeController
{
    public function __construct(private readonly HomePage $homePage)
    {
    }

    public function show(Request $request): Response
    {
        return Response::html($this->homePage->render());
    }
}
```

`src/Controller/ErrorController.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Controller;

use Technum\Http\Request;
use Technum\Http\Response;
use Technum\View\View;

final class ErrorController
{
    public function __construct(private readonly View $view)
    {
    }

    public function notFound(Request $request): Response
    {
        return Response::html($this->view->renderPage('errors/404', [], [
            'title' => 'Page introuvable, TECHNUM',
            'description' => "Cette page n'existe pas sur bytechnum.com.",
            'path' => $request->path,
            'robots' => 'noindex',
        ]), 404);
    }
}
```

`templates/errors/404.php` :

```php
<section class="page-message" aria-labelledby="titre-erreur">
  <div class="page-message__inner">
    <h1 id="titre-erreur">Cette page n'existe pas.</h1>
    <p>L'adresse a peut-être changé. Revenez à l'accueil pour découvrir nos produits et nos services.</p>
    <p><a class="button button--primary" href="/">Revenir à l'accueil</a></p>
  </div>
</section>
```

- [ ] **Étape 6 : écrire `src/Application.php`**

```php
<?php

declare(strict_types=1);

namespace Technum;

use Technum\Content\ContentRepository;
use Technum\Controller\ErrorController;
use Technum\Controller\HomeController;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Http\Router;
use Technum\Page\HomePage;
use Technum\View\View;

/**
 * Assemble les services du site et déclare ses routes.
 */
final class Application
{
    private function __construct(
        private readonly Router $router,
        private readonly bool $isProduction,
    ) {
    }

    public static function create(string $rootDir, Config $config): self
    {
        $content = new ContentRepository($rootDir . '/content', $rootDir . '/public');
        $view = new View($rootDir . '/templates', $rootDir . '/public');
        $view->share(['site' => $content->site(), 'products' => $content->products()]);

        $home = new HomeController(new HomePage($view, $content));
        $errors = new ErrorController($view);

        $router = new Router($errors->notFound(...));
        $router->get('/', $home->show(...));
        $router->get('/contact', static fn (Request $request): Response => Response::redirect('/#contact', 301));

        return new self($router, $config->isProduction());
    }

    public function handle(Request $request): Response
    {
        $response = $this->router->dispatch($request);

        return $this->isProduction
            ? $response->withHeader('Strict-Transport-Security', 'max-age=31536000')
            : $response;
    }
}
```

- [ ] **Étape 7 : relancer le test**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomeRouteTest
```

Attendu : cinq tests verts.

- [ ] **Étape 8 : écrire `public/index.php`**

```php
<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Technum\Application;
use Technum\Config;
use Technum\Http\Request;

$rootDir = dirname(__DIR__);

require $rootDir . '/vendor/autoload.php';

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', $rootDir . '/storage/logs/php-errors.log');

try {
    $config = Config::fromArray(Dotenv::createArrayBacked($rootDir)->load());
    $application = Application::create($rootDir, $config);
    $request = Request::fromGlobals($_SERVER, $_GET, $_POST, $config->clientIpHeader);
    $application->handle($request)->send();
} catch (Throwable $exception) {
    error_log(sprintf('%s: %s (%s:%d)', $exception::class, $exception->getMessage(), $exception->getFile(), $exception->getLine()));
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }
    readfile(__DIR__ . '/500.html');
}
```

- [ ] **Étape 9 : écrire `public/500.html`**

```html
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Erreur temporaire, TECHNUM</title>
  <meta name="robots" content="noindex">
  <link rel="stylesheet" href="/assets/css/site.css">
</head>
<body>
  <main class="page-message">
    <div class="page-message__inner">
      <h1>Le site rencontre un problème temporaire.</h1>
      <p>Réessayez dans quelques minutes. Pour une demande urgente, écrivez-nous sur <a href="https://wa.me/2290150617300">WhatsApp</a> ou à <a href="mailto:elisee.atonde@bytechnum.com">elisee.atonde@bytechnum.com</a>.</p>
    </div>
  </main>
</body>
</html>
```

- [ ] **Étape 10 : écrire `public/.htaccess`**

```apache
# bytechnum.com : réécriture vers le point d'entrée unique et en-têtes des fichiers statiques.
# HTTPS est déjà imposé par l'hébergeur : ne pas ajouter de redirection HTTPS ici.

Options -Indexes
DirectoryIndex index.php

<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{HTTP_HOST} ^www\.bytechnum\.com$ [NC]
    RewriteRule ^ https://bytechnum.com%{REQUEST_URI} [R=301,L]

    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^ - [L]

    RewriteRule ^ index.php [L]
</IfModule>

<IfModule mod_mime.c>
    AddType font/woff2 .woff2
    AddType image/webp .webp
    AddType image/svg+xml .svg
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(css|js|woff2|webp|png|svg)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
        Header set X-Content-Type-Options "nosniff"
    </FilesMatch>
    <FilesMatch "\.svg$">
        Header set Content-Security-Policy "default-src 'none'; style-src 'unsafe-inline'"
    </FilesMatch>
    <FilesMatch "\.(ico|txt|xml)$">
        Header set Cache-Control "public, max-age=86400"
        Header set X-Content-Type-Options "nosniff"
    </FilesMatch>
    <FilesMatch "^500\.html$">
        Header set Cache-Control "no-cache"
        Header set X-Content-Type-Options "nosniff"
    </FilesMatch>
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/plain application/javascript application/xml image/svg+xml
</IfModule>
```

- [ ] **Étape 11 : écrire `tools/dev-router.php`, préparer `storage` et compléter la configuration des outils**

`tools/dev-router.php` :

```php
<?php

declare(strict_types=1);

// Routeur du serveur PHP intégré, pour le développement uniquement :
// les fichiers existants de public/ sont servis tels quels, le reste passe par index.php.
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url(is_string($requestUri) ? $requestUri : '/', PHP_URL_PATH);
$publicDir = dirname(__DIR__) . '/public';

if (is_string($path) && $path !== '/' && is_file($publicDir . $path)) {
    return false;
}

require $publicDir . '/index.php';
```

Ajouter à la fin de `.gitignore` :

```text
# Journaux et limitation des envois, écrits par l'application
/storage/logs/*
!/storage/logs/.gitkeep
```

Dans `phpstan.neon`, la liste `paths` devient :

```neon
    paths:
        - src
        - tests
        - public/index.php
```

Puis :

```bash
cd /c/wamp64/www/TECHNUM
mkdir -p storage/logs storage/rate-limit
touch storage/logs/.gitkeep storage/rate-limit/.gitkeep
```

- [ ] **Étape 12 : vérifier le site en local**

Créer un `.env` local, jamais commité :

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
SECRET=$("$PHP84" -r 'echo bin2hex(random_bytes(32));')
cat > .env <<EOF
APP_ENV=local
APP_SECRET=$SECRET
MAIL_TRANSPORT=log
CONTACT_SENDER_EMAIL=elisee.atonde@bytechnum.com
CONTACT_RECIPIENT_EMAIL=elisee.atonde@bytechnum.com
EOF
git check-ignore .env
```

Attendu : `git check-ignore` affiche `.env`. Lancer en arrière-plan `"$PHP84" -S 127.0.0.1:8080 -t public tools/dev-router.php`, puis :

```bash
curl -s -o /dev/null -w "accueil %{http_code}\n" http://127.0.0.1:8080/
curl -s -o /dev/null -w "inconnue %{http_code}\n" http://127.0.0.1:8080/page-inconnue
curl -s -o /dev/null -w "contact %{http_code} %{redirect_url}\n" http://127.0.0.1:8080/contact
curl -s -o /dev/null -w "css %{http_code}\n" http://127.0.0.1:8080/assets/css/site.css
curl -s -o /dev/null -w "logo %{http_code}\n" http://127.0.0.1:8080/assets/img/logo-technum.svg
```

Attendu : 200, 404, 301 vers `http://127.0.0.1:8080/#contact`, 200, 200. Arrêter le serveur.

- [ ] **Étape 13 : vérification complète, commit, pull request du lot**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
git add composer.json composer.lock src/Application.php src/Controller templates/errors public/index.php public/500.html public/.htaccess tools storage .gitignore phpstan.neon tests/Integration
git commit -F - <<'EOF'
feat(app): assemble l'application et ajoute le point d'entrée
EOF
```

Pull request du lot :

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Page d'accueil et point d'entrée" --body-file - <<EOF
## Objectif

Rendre la page d'accueil complète, encore sans styles définitifs. Issue liée : #$ISSUE

## Changements

- Gabarits : accueil et registre des produits, produits, réalisations, services, méthode, fondateur, contact direct, en-tête, pied de page
- Assemblage de l'application, point d'entrée, page 404, page d'erreur statique
- Réécriture vers index.php, redirection de www, en-têtes des fichiers statiques
- Routeur du serveur PHP pour le développement local

## Tests

- [x] Rendu de l'accueil : contenu, états, liens, coordonnées, absence de style en ligne, fichiers cités présents
- [x] Routes : accueil, HEAD, redirection de /contact, 404 non indexée, HSTS en production seulement
- [x] Vérification locale avec curl

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de secret exposé, .env ignoré par Git
- [x] Nommage conforme aux conventions
- [x] Taille : environ 750 lignes, dont 350 de gabarits. Les gabarits et leur test forment un tout.
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

---

## Lot 4 : identité visuelle

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Identité visuelle du site" --body "Feuille de style complète, script du menu, outils de qualité du front et revue visuelle." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-identite-visuelle"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

Avant de commencer, relire la section 6 de la spécification. L'élément marquant est le registre des produits avec ses pistes d'état. Tout le reste reste sobre : pas d'ombre, pas d'apparition au défilement, pas de zoom au survol, texte aligné à gauche.

### Tâche 13 : feuille de style

**Fichiers :**

- Modifier : `public/assets/css/site.css` (remplacement complet du socle)

**Interfaces :**

- Consomme : les classes des gabarits de la tâche 11.
- Produit : les jetons `--charcoal`, `--charcoal-soft`, `--charcoal-muted`, `--blue`, `--blue-dark`, `--ice`, `--off-white`, `--white`, `--line`, `--error`, les tailles `--text-*`, les espacements `--space-1` à `--space-9`, `--radius-control`, `--radius-frame`, `--radius-panel`, `--container`, `--gutter`, `--measure`, `--header-height`. Les tâches 21 et 22 ajoutent leurs styles à la fin du fichier en réutilisant ces jetons.

- [ ] **Étape 1 : remplacer `public/assets/css/site.css` par la feuille complète**

```css
/*
 * Feuille de style de bytechnum.com.
 * Palette, typographie et composants tirés du Brand Book TECHNUM, édition 01, 2026.
 * Seule couleur hors palette : --error, réservée aux erreurs de formulaire.
 */

@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url('../fonts/montserrat-600.woff2') format('woff2');
}

@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url('../fonts/montserrat-700.woff2') format('woff2');
}

@font-face {
  font-family: 'Poppins';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('../fonts/poppins-400.woff2') format('woff2');
}

@font-face {
  font-family: 'Poppins';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url('../fonts/poppins-500.woff2') format('woff2');
}

:root {
  --charcoal: #373536;
  --charcoal-soft: #5e5c5d;
  --charcoal-muted: #8a8889;
  --blue: #405fe0;
  --blue-dark: #2846b9;
  --ice: #e9edff;
  --off-white: #f7f8fc;
  --white: #ffffff;
  --line: #e5e7ed;
  --error: #b42318;

  --font-display: 'Montserrat', 'Segoe UI', system-ui, sans-serif;
  --font-text: 'Poppins', 'Segoe UI', system-ui, sans-serif;

  --text-small: 0.875rem;
  --text-body: 1.0625rem;
  --text-lead: 1.1875rem;
  --text-h3: 1.3125rem;
  --text-product: 1.625rem;
  --text-h2: clamp(1.75rem, 1.45rem + 0.9vw, 2.0625rem);
  --text-h1: clamp(2.5rem, 1.9rem + 2.2vw, 3.25rem);

  --space-1: 0.25rem;
  --space-2: 0.5rem;
  --space-3: 0.75rem;
  --space-4: 1rem;
  --space-5: 1.5rem;
  --space-6: 2rem;
  --space-7: 3rem;
  --space-8: 4.5rem;
  --space-9: 6.5rem;

  --radius-control: 8px;
  --radius-frame: 12px;
  --radius-panel: 16px;

  --container: 75rem;
  --gutter: clamp(1rem, 4vw, 2.5rem);
  --measure: 66ch;
  --header-height: 4.5rem;
}

/* Base */

*,
*::before,
*::after {
  box-sizing: border-box;
}

html {
  -webkit-text-size-adjust: 100%;
  text-size-adjust: 100%;
  scroll-padding-top: calc(var(--header-height) + var(--space-4));
}

body {
  margin: 0;
  background: var(--white);
  color: var(--charcoal);
  font-family: var(--font-text);
  font-size: var(--text-body);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}

h1,
h2,
h3 {
  margin: 0;
  font-family: var(--font-display);
  font-weight: 700;
  line-height: 1.15;
  letter-spacing: -0.01em;
  text-wrap: balance;
}

p,
ul,
ol,
dl,
dd,
figure {
  margin: 0;
}

p {
  text-wrap: pretty;
}

a {
  color: var(--blue);
  text-decoration-line: none;
  text-decoration-thickness: 1px;
  text-underline-offset: 0.2em;
}

a:hover,
a:focus-visible {
  color: var(--blue-dark);
  text-decoration-line: underline;
}

:focus-visible {
  outline: 2px solid var(--blue);
  outline-offset: 3px;
}

img {
  display: block;
  max-width: 100%;
  height: auto;
}

[hidden] {
  display: none !important;
}

main:focus {
  outline: none;
}

.visually-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  overflow: hidden;
  clip: rect(0 0 0 0);
  white-space: nowrap;
  border: 0;
}

.skip-link {
  position: absolute;
  top: -10rem;
  left: var(--space-4);
  z-index: 20;
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-control);
  background: var(--charcoal);
  color: var(--white);
  font-family: var(--font-display);
  font-weight: 600;
}

.skip-link:focus {
  top: var(--space-4);
  color: var(--white);
}

/* Boutons */

.button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 3rem;
  padding: 0 1.375rem;
  border: 1px solid transparent;
  border-radius: var(--radius-control);
  font-family: var(--font-display);
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.2;
  text-align: center;
  text-decoration: none;
  cursor: pointer;
  transition:
    background-color 0.15s ease,
    border-color 0.15s ease,
    color 0.15s ease;
}

.button:hover,
.button:focus-visible {
  text-decoration: none;
}

.button--primary {
  background: var(--blue);
  color: var(--white);
}

.button--primary:hover,
.button--primary:focus-visible {
  background: var(--blue-dark);
  color: var(--white);
}

.button--secondary {
  border-color: var(--line);
  background: var(--white);
  color: var(--charcoal);
}

.button--secondary:hover,
.button--secondary:focus-visible {
  border-color: var(--blue);
  color: var(--blue-dark);
}

/* En-tête */

.site-header {
  position: sticky;
  top: 0;
  z-index: 10;
  border-bottom: 1px solid var(--line);
  background: var(--white);
}

.site-header__inner {
  position: relative;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-3) var(--space-6);
  max-width: var(--container);
  min-height: var(--header-height);
  margin: 0 auto;
  padding: var(--space-3) var(--gutter);
}

.site-header__logo {
  display: block;
  margin-right: auto;
  padding-block: var(--space-2);
}

.site-header__logo img {
  width: 9.25rem;
}

.site-header__cta {
  display: none;
  min-height: 2.75rem;
}

.site-nav {
  order: 3;
  width: 100%;
}

.site-nav__list {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2) var(--space-5);
  margin: 0;
  padding: 0 0 var(--space-2);
  list-style: none;
}

.site-nav__list a {
  color: var(--charcoal);
  font-family: var(--font-display);
  font-size: 0.9375rem;
  font-weight: 600;
}

.site-nav__list a:hover,
.site-nav__list a:focus-visible {
  color: var(--blue-dark);
}

.site-nav__toggle {
  display: none;
}

/* Menu replié quand le script est actif */

.js .site-nav {
  order: 0;
  width: auto;
}

.js .site-nav__toggle {
  display: inline-flex;
  align-items: center;
  min-height: 2.75rem;
  padding: 0 var(--space-4);
  border: 1px solid var(--line);
  border-radius: var(--radius-control);
  background: var(--white);
  color: var(--charcoal);
  font-family: var(--font-display);
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
}

.js .site-nav__list {
  position: absolute;
  top: 100%;
  right: 0;
  left: 0;
  display: none;
  flex-direction: column;
  gap: 0;
  padding: var(--space-2) var(--gutter) var(--space-4);
  border-bottom: 1px solid var(--line);
  background: var(--white);
}

.js .site-nav[data-open='true'] .site-nav__list {
  display: flex;
}

.js .site-nav__list a {
  display: block;
  padding: var(--space-3) 0;
}

/* Accueil */

.hero {
  padding: var(--space-7) 0;
}

.hero__inner {
  display: grid;
  gap: var(--space-7);
  max-width: var(--container);
  margin: 0 auto;
  padding: 0 var(--gutter);
}

.hero__title {
  max-width: 15ch;
  font-size: var(--text-h1);
  line-height: 1.1;
}

.hero__lead {
  max-width: 34rem;
  margin-top: var(--space-5);
  color: var(--charcoal-soft);
  font-size: var(--text-lead);
  line-height: 1.55;
}

.hero__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-4) var(--space-6);
  margin-top: var(--space-6);
}

.hero__more {
  font-family: var(--font-display);
  font-weight: 600;
}

/* Registre des produits : l'élément marquant de la page */

.register {
  --track-empty: var(--white);
  padding: var(--space-6);
  border-radius: var(--radius-panel);
  background: var(--ice);
}

.register__title {
  font-size: 1.0625rem;
}

.register__list {
  margin-top: var(--space-3);
  padding: 0;
  list-style: none;
}

.register__item + .register__item {
  border-top: 1px solid var(--white);
}

.register__link {
  display: grid;
  grid-template-areas:
    'icon name name'
    'icon track stage';
  grid-template-columns: 1.75rem minmax(0, 1fr) auto;
  align-items: center;
  gap: var(--space-1) var(--space-3);
  padding: var(--space-3) 0;
  color: var(--charcoal);
}

.register__link:hover,
.register__link:focus-visible {
  color: var(--charcoal);
  text-decoration: none;
}

.register__icon {
  grid-area: icon;
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 6px;
  object-fit: contain;
}

.register__name {
  grid-area: name;
  font-family: var(--font-display);
  font-weight: 600;
}

.register__link:hover .register__name,
.register__link:focus-visible .register__name {
  color: var(--blue-dark);
  text-decoration: underline;
  text-underline-offset: 0.2em;
}

.register__link .track {
  grid-area: track;
}

.register__stage {
  grid-area: stage;
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

.register__updated {
  margin-top: var(--space-3);
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

/* Pistes d'état : quatre bits, repris de l'ancienne page « Bientôt en ligne » */

.track {
  display: inline-flex;
  gap: 3px;
}

.track__bit {
  position: relative;
  width: 0.875rem;
  height: 0.375rem;
  overflow: hidden;
  border-radius: 2px;
  background: var(--track-empty, var(--line));
}

.track__bit--done::after {
  position: absolute;
  inset: 0;
  background: var(--blue);
  content: '';
}

.stages {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 4px;
  max-width: 28rem;
  margin-top: var(--space-5);
  padding: 0;
  list-style: none;
}

.stages__step {
  position: relative;
  padding-top: var(--space-3);
  color: var(--charcoal-soft);
  font-size: 0.8125rem;
  line-height: 1.3;
}

.stages__step::before {
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
  height: 0.375rem;
  border-radius: 2px;
  background: var(--line);
  content: '';
}

.stages__step--done::before {
  background: var(--blue);
}

.stages__step[aria-current='step'] {
  color: var(--charcoal);
  font-weight: 500;
}

/* Seule animation de la page : chaque piste du registre se remplit une fois au chargement. */

@media (prefers-reduced-motion: no-preference) {
  html {
    scroll-behavior: smooth;
  }

  .track--animated .track__bit--done::after {
    transform-origin: left center;
    animation: bit-in 360ms cubic-bezier(0.2, 0.7, 0.2, 1) both;
    animation-delay: calc(var(--row-delay, 0ms) + var(--bit-delay, 0ms));
  }

  .track__bit:nth-child(2) {
    --bit-delay: 70ms;
  }

  .track__bit:nth-child(3) {
    --bit-delay: 140ms;
  }

  .track__bit:nth-child(4) {
    --bit-delay: 210ms;
  }

  .register__item:nth-child(2) {
    --row-delay: 80ms;
  }

  .register__item:nth-child(3) {
    --row-delay: 160ms;
  }

  .register__item:nth-child(4) {
    --row-delay: 240ms;
  }
}

@keyframes bit-in {
  from {
    transform: scaleX(0);
  }

  to {
    transform: scaleX(1);
  }
}

/* Sections : colonne de repère à gauche, séparée par le filet vertical du logo */

.section__inner {
  display: grid;
  max-width: var(--container);
  margin: 0 auto;
  padding: 0 var(--gutter);
}

.section__aside {
  padding: var(--space-7) 0 var(--space-5);
}

.section__title {
  font-size: var(--text-h2);
}

.section__context {
  max-width: 34rem;
  margin-top: var(--space-3);
  color: var(--charcoal-soft);
}

.section__body {
  min-width: 0;
  padding-bottom: var(--space-7);
}

.section--contact {
  background: var(--off-white);
}

/* Produits */

.product {
  display: grid;
  gap: var(--space-5);
}

.product + .product {
  margin-top: var(--space-8);
}

.product__media {
  min-width: 0;
}

.product__frame {
  overflow: hidden;
  border: 1px solid var(--line);
  border-radius: var(--radius-frame);
  background: var(--off-white);
}

.product__frame img {
  width: 100%;
}

.product__media--phone .product__frame {
  display: flex;
  justify-content: center;
  padding: var(--space-6) var(--space-5);
}

.product__media--phone .product__frame img {
  width: auto;
  max-width: 16rem;
  border: 1px solid var(--line);
  border-radius: 20px;
}

.product__host {
  margin-top: var(--space-2);
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

.product__name {
  font-size: var(--text-product);
}

.product__tagline {
  margin-top: var(--space-3);
  font-weight: 500;
}

.product__audience {
  margin-top: var(--space-2);
  color: var(--charcoal-soft);
}

.product__facts {
  display: grid;
  gap: var(--space-4);
  margin-top: var(--space-5);
}

.product__facts dt {
  font-family: var(--font-display);
  font-size: 0.9375rem;
  font-weight: 600;
}

.product__facts dd {
  margin-top: var(--space-1);
  color: var(--charcoal-soft);
}

.product__note {
  margin-top: var(--space-4);
  padding-left: var(--space-3);
  border-left: 2px solid var(--blue);
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

.product__action {
  margin-top: var(--space-6);
}

/* Autres réalisations */

.projects {
  padding: 0;
  border-top: 1px solid var(--line);
  list-style: none;
}

.project {
  display: grid;
  gap: var(--space-2);
  padding: var(--space-5) 0;
  border-bottom: 1px solid var(--line);
}

.project__name {
  font-size: var(--text-h3);
}

.project__nature {
  margin-top: var(--space-1);
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

.project__description {
  max-width: var(--measure);
}

.project__link {
  font-family: var(--font-display);
  font-size: 0.9375rem;
  font-weight: 600;
}

/* Services */

.services {
  display: grid;
  gap: var(--space-7);
  padding: 0;
  list-style: none;
}

.service__name {
  font-size: var(--text-h3);
}

.service__description {
  margin-top: var(--space-2);
  color: var(--charcoal-soft);
}

.service__examples {
  margin-top: var(--space-3);
  font-size: 0.9375rem;
}

/* Méthode : une vraie séquence, donc numérotée */

.steps {
  display: grid;
  gap: var(--space-5);
  padding: 0;
  list-style: none;
  counter-reset: step;
}

.step {
  display: grid;
  grid-template-columns: 2.5rem minmax(0, 1fr);
  column-gap: var(--space-4);
  counter-increment: step;
}

.step::before {
  grid-row: span 2;
  color: var(--blue);
  font-family: var(--font-display);
  font-size: var(--text-h3);
  font-weight: 700;
  line-height: 1.25;
  content: counter(step);
}

.step__title {
  font-size: 1.125rem;
  line-height: 1.35;
}

.step__text {
  max-width: var(--measure);
  margin-top: var(--space-1);
  color: var(--charcoal-soft);
}

.commitments {
  max-width: var(--measure);
  margin-top: var(--space-7);
  padding: var(--space-5) var(--space-6);
  border-radius: var(--radius-panel);
  background: var(--ice);
}

/* Fondateur */

.about__text {
  max-width: var(--measure);
  font-size: var(--text-lead);
}

.about__link {
  display: inline-block;
  margin-top: var(--space-5);
  font-family: var(--font-display);
  font-weight: 600;
}

/* Contact */

.contact {
  display: grid;
  gap: var(--space-7);
}

.contact-direct {
  align-self: start;
  padding: var(--space-6);
  border: 1px solid var(--line);
  border-radius: var(--radius-panel);
  background: var(--white);
}

.contact-direct__title {
  font-size: var(--text-h3);
}

.contact-direct__action {
  width: 100%;
  margin-top: var(--space-5);
}

.contact-direct__list {
  display: grid;
  gap: var(--space-4);
  margin-top: var(--space-6);
  padding: 0;
  list-style: none;
}

.contact-direct__label {
  display: block;
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

.contact-direct__list a {
  overflow-wrap: anywhere;
}

/* Pages de message : 404 et erreur temporaire */

.page-message {
  padding: var(--space-9) 0;
}

.page-message__inner {
  max-width: var(--container);
  margin: 0 auto;
  padding: 0 var(--gutter);
}

.page-message h1 {
  max-width: 24ch;
  font-size: var(--text-h2);
}

.page-message p {
  max-width: var(--measure);
  margin-top: var(--space-4);
  color: var(--charcoal-soft);
}

/* Pied de page */

.site-footer {
  background: var(--charcoal);
  color: var(--white);
}

.site-footer :focus-visible {
  outline-color: var(--white);
}

.site-footer__inner {
  display: grid;
  gap: var(--space-7);
  max-width: var(--container);
  margin: 0 auto;
  padding: var(--space-8) var(--gutter) var(--space-7);
}

.site-footer__brand img {
  width: 13.75rem;
}

.site-footer__title {
  color: var(--ice);
  font-size: 0.9375rem;
  font-weight: 600;
}

.site-footer ul {
  display: grid;
  gap: var(--space-2);
  margin-top: var(--space-3);
  padding: 0;
  list-style: none;
}

.site-footer a {
  color: var(--white);
}

.site-footer a:hover,
.site-footer a:focus-visible {
  color: var(--ice);
}

.site-footer__meta {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: var(--space-2) var(--space-6);
  max-width: var(--container);
  margin: 0 auto;
  padding: var(--space-5) var(--gutter) var(--space-6);
  border-top: 1px solid rgba(255, 255, 255, 0.16);
  color: rgba(255, 255, 255, 0.78);
  font-size: var(--text-small);
}

/* Grands écrans */

@media (min-width: 30rem) {
  .register__link {
    grid-template-areas: 'icon name track stage';
    grid-template-columns: 1.75rem minmax(0, 1fr) auto 6.5rem;
  }
}

@media (min-width: 40rem) {
  .site-header__cta {
    display: inline-flex;
  }
}

@media (min-width: 48rem) {
  .project {
    grid-template-columns: 15rem minmax(0, 1fr);
    column-gap: var(--space-6);
  }

  .project__head {
    grid-row: span 2;
  }

  .services {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 60rem) {
  .site-header__inner {
    flex-wrap: nowrap;
  }

  .site-header__logo {
    margin-right: 0;
  }

  .site-nav,
  .js .site-nav {
    order: 0;
    width: auto;
    margin-left: auto;
  }

  .site-nav__list,
  .js .site-nav__list {
    position: static;
    display: flex;
    flex-direction: row;
    gap: var(--space-6);
    padding: 0;
    border: 0;
    background: none;
  }

  .js .site-nav__list a {
    display: inline;
    padding: 0;
  }

  .site-nav__toggle,
  .js .site-nav__toggle {
    display: none;
  }

  .hero {
    padding: var(--space-9) 0 var(--space-8);
  }

  .hero__inner {
    grid-template-columns: minmax(0, 7fr) minmax(0, 5fr);
    align-items: center;
    gap: var(--space-8);
  }

  .section__inner {
    grid-template-columns: 18rem minmax(0, 1fr);
  }

  .section__aside {
    padding: var(--space-8) var(--space-6) var(--space-8) 0;
    border-right: 1px solid var(--line);
  }

  .section__label {
    position: sticky;
    top: calc(var(--header-height) + var(--space-6));
  }

  .section__body {
    padding: var(--space-8) 0 var(--space-8) var(--space-7);
  }

  .product {
    grid-template-columns: minmax(0, 6fr) minmax(0, 5fr);
    align-items: start;
    gap: var(--space-7);
  }

  .contact {
    grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
    align-items: start;
  }

  .site-footer__inner {
    grid-template-columns: minmax(0, 2fr) repeat(3, minmax(0, 1fr));
  }
}
```

- [ ] **Étape 2 : relancer les tests**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
grep -c '' public/assets/css/site.css
wc -c public/assets/css/site.css
```

Attendu : tous les tests verts, dont celui qui vérifie que les fichiers cités existent. Feuille sous 30 Ko.

- [ ] **Étape 3 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add public/assets/css/site.css
git commit -F - <<'EOF'
style(css): applique l'identité visuelle technum à la page
EOF
```

### Tâche 14 : script du menu, outils du front et intégration continue

**Fichiers :**

- Modifier : `public/assets/js/site.js` (remplacement complet), `.github/workflows/ci.yml`
- Créer : `package.json`, `package-lock.json`, `eslint.config.mjs`, `.prettierrc.json`, `.prettierignore`

**Interfaces :**

- Consomme : le balisage de `templates/partials/header.php` (`.site-nav`, `.site-nav__toggle`, `.site-nav__list`).
- Produit : `site.js` qui pose la classe `js` sur `<html>` dès son exécution, puis, à `DOMContentLoaded`, rend le bouton « Menu » visible et gère `aria-expanded`, `data-open`, la touche Échap et la fermeture au clic sur un lien. La tâche 21 y ajoute le compteur du message et le verrouillage de l'envoi, dans la même fonction `init`.

- [ ] **Étape 1 : remplacer `public/assets/js/site.js`**

```js
/*
 * Améliorations progressives de bytechnum.com.
 * La page fonctionne sans ce script : il replie le menu sur petit écran.
 */
(function () {
  'use strict';

  document.documentElement.classList.remove('no-js');
  document.documentElement.classList.add('js');

  function setUpMenu() {
    const nav = document.querySelector('.site-nav');
    const toggle = nav ? nav.querySelector('.site-nav__toggle') : null;
    const list = nav ? nav.querySelector('.site-nav__list') : null;
    if (!nav || !toggle || !list) {
      return;
    }

    const setOpen = (isOpen) => {
      nav.dataset.open = String(isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
      toggle.textContent = isOpen ? 'Fermer' : 'Menu';
    };

    toggle.hidden = false;
    setOpen(false);

    toggle.addEventListener('click', () => setOpen(nav.dataset.open !== 'true'));

    list.addEventListener('click', (event) => {
      if (event.target instanceof Element && event.target.closest('a')) {
        setOpen(false);
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && nav.dataset.open === 'true') {
        setOpen(false);
        toggle.focus();
      }
    });
  }

  function init() {
    setUpMenu();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
```

- [ ] **Étape 2 : écrire `package.json`, puis installer les outils**

```json
{
  "name": "technum-site",
  "private": true,
  "description": "Outils de qualité du front de bytechnum.com",
  "scripts": {
    "lint:js": "eslint public/assets/js",
    "format": "prettier --write public/assets/css public/assets/js",
    "format:check": "prettier --check public/assets/css public/assets/js"
  }
}
```

```bash
cd /c/wamp64/www/TECHNUM
npm install --save-dev eslint @eslint/js globals prettier
```

Attendu : `package-lock.json` créé, `node_modules/` ignoré par Git.

- [ ] **Étape 3 : écrire la configuration des outils**

`eslint.config.mjs` :

```js
import js from '@eslint/js';
import globals from 'globals';

export default [
  js.configs.recommended,
  {
    files: ['public/assets/js/**/*.js'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'script',
      globals: globals.browser,
    },
    rules: {
      'no-console': 'error',
      'no-eval': 'error',
      'no-implied-eval': 'error',
      'no-new-func': 'error',
    },
  },
];
```

`.prettierrc.json` :

```json
{
  "singleQuote": true,
  "printWidth": 100
}
```

`.prettierignore` :

```text
vendor
node_modules
storage
documentations
docs
```

- [ ] **Étape 4 : lancer les outils du front**

```bash
cd /c/wamp64/www/TECHNUM
npm run format
npm run lint:js
npm run format:check
git diff --stat
```

Attendu : ESLint sans erreur. `npm run format` peut reformater `site.css` et `site.js` : relire le diff, il ne doit toucher que la mise en forme.

- [ ] **Étape 5 : ajouter le job front à `.github/workflows/ci.yml`**

Ajouter ce job à la fin du fichier, au même niveau que `php` et `secrets` :

```yaml
  front:
    name: Front, ESLint et Prettier
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '24'
          cache: npm
      - name: Dépendances
        run: npm ci
      - name: ESLint
        run: npm run lint:js
      - name: Prettier
        run: npm run format:check
```

- [ ] **Étape 6 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add public/assets/js/site.js public/assets/css/site.css package.json package-lock.json eslint.config.mjs .prettierrc.json .prettierignore .github/workflows/ci.yml
git commit -F - <<'EOF'
feat(front): replie le menu sur mobile et ajoute les outils du front
EOF
```

### Tâche 15 : revue visuelle et pull request du lot

**Fichiers :**

- Modifier selon les constats : `public/assets/css/site.css`, `templates/**`

**Interfaces :** aucune nouvelle.

- [ ] **Étape 1 : lancer le site en local**

Lancer en arrière-plan :

```bash
cd /c/wamp64/www/TECHNUM
/c/wamp64/bin/php/php8.4.15/php.exe -S 127.0.0.1:8080 -t public tools/dev-router.php
```

- [ ] **Étape 2 : capturer la page sur trois largeurs**

Avec les outils Playwright, pour chaque largeur 1440 × 900, 820 × 1180 et 390 × 844 : `browser_resize`, `browser_navigate` vers `http://127.0.0.1:8080/`, `browser_take_screenshot` avec `fullPage` à `true` vers `c:\wamp64\www\TECHNUM\.playwright-mcp\accueil-<largeur>.png`. Lire chaque image.

- [ ] **Étape 3 : mesurer ce qui se mesure**

Sur chaque largeur, `browser_evaluate` :

```js
() => ({
  horizontalScroll: document.documentElement.scrollWidth > document.documentElement.clientWidth,
  titleFont: getComputedStyle(document.querySelector('h1')).fontFamily,
  montserratReady: document.fonts.check('700 16px Montserrat'),
  poppinsReady: document.fonts.check('400 16px Poppins'),
  logoWidth: document.querySelector('.site-header__logo img').getBoundingClientRect().width,
  jsClass: document.documentElement.classList.contains('js'),
})
```

Attendu : `horizontalScroll` à `false`, polices prêtes, largeur du logo d'au moins 120, classe `js` présente. Puis `browser_console_messages` : aucune erreur.

- [ ] **Étape 4 : contrôler la liste de la spécification**

Cocher chaque point sur les captures :

- [ ] Le titre tient en quatre lignes au plus sur téléphone, sans débordement.
- [ ] Le registre est lisible : icône, nom, piste et état alignés. Sur téléphone, la piste et l'état passent sous le nom.
- [ ] Sur grand écran, le filet vertical court sans interruption de la section produits à la section contact, et le nom de section reste visible pendant le défilement.
- [ ] Les captures ne sont pas déformées, les captures de téléphone sont centrées dans leur cadre.
- [ ] Aucune ombre, aucun dégradé, aucun texte centré hors boutons, aucune étiquette en capitales.
- [ ] Le menu « Menu » s'ouvre et se ferme sur téléphone, Échap le referme, un clic sur un lien le referme.
- [ ] Le pied de page reste lisible : logo clair, liens blancs, contraste suffisant.

Contrôler aussi le mouvement : recharger la page sur grand écran et regarder le registre se remplir une seule fois. Puis `browser_emulate_media` avec `reducedMotion` à `reduce`, recharger : les pistes s'affichent pleines, sans animation.

Contrôler le clavier : sur grand écran, appuyer plusieurs fois sur Tab avec `browser_press_key`, puis capturer. Le lien « Aller au contenu » apparaît en premier, chaque élément actif montre un contour bleu.

- [ ] **Étape 4 bis : mesurer avec Lighthouse**

```bash
cd /c/wamp64/www/TECHNUM
npx --yes lighthouse http://127.0.0.1:8080/ --only-categories=performance,accessibility,best-practices,seo --form-factor=mobile --chrome-flags="--headless=new" --output=json --output-path=.playwright-mcp/lighthouse.json --quiet
node -e "const r=require('./.playwright-mcp/lighthouse.json'); for (const [k,v] of Object.entries(r.categories)) console.log(k, Math.round(v.score*100));"
```

Attendu : au moins 95 sur les quatre axes. Si Chrome est introuvable, noter le point et le mesurer à la tâche 27 avec PageSpeed Insights.

- [ ] **Étape 5 : corriger les écarts constatés**

Corriger dans `site.css` ou les gabarits chaque point non coché, puis reprendre les étapes 2 à 4 sur la largeur concernée jusqu'à ce que tout soit coché. Chaque correction garde les jetons existants et reste dans la palette.

- [ ] **Étape 6 : vérification complète, commit, pull request du lot**

Arrêter le serveur local et supprimer `.playwright-mcp`.

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .playwright-mcp
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
npm run lint:js
npm run format:check
git status --short
```

Si la revue a modifié des fichiers :

```bash
cd /c/wamp64/www/TECHNUM
git add public/assets/css/site.css templates
git commit -F - <<'EOF'
style(css): corrige les écarts relevés à la revue visuelle
EOF
```

Pull request du lot :

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Identité visuelle du site" --body-file - <<EOF
## Objectif

Donner à la page l'identité visuelle TECHNUM définie par la spécification. Issue liée : #$ISSUE

## Changements

- Feuille de style complète : jetons du brand book, typographie, registre et pistes d'état, sections à colonne de repère, produits, réalisations, services, méthode, contact, pied de page
- Une seule animation, désactivée quand l'utilisateur réduit les animations
- Menu replié sur téléphone, utilisable au clavier
- ESLint et Prettier, job front dans l'intégration continue

## Tests

- [x] Tests PHP toujours verts
- [x] ESLint et Prettier sans erreur
- [x] Revue visuelle sur 1440, 820 et 390 px de large, clavier, animations réduites, console sans erreur

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de console.log
- [x] Pas de secret exposé
- [x] Taille : environ 950 lignes, dont 850 de CSS. Une feuille de style unique se relit d'un tenant.
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

---

## Lot 5 : règles du formulaire de contact

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Règles du formulaire de contact" --body "Validation d'une demande, jeton signé, limite d'envois, journal de sécurité, message et expéditeurs." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-regles-formulaire"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

### Tâche 16 : validation d'une demande

**Fichiers :**

- Créer : `src/Contact/ContactRequest.php`, `tests/Unit/Contact/ContactRequestTest.php`

**Interfaces :**

- Produit : `Technum\Contact\ContactRequest` avec les constantes `NEEDS` (`management`, `platform`, `website`, `security`, `hosting`, `other` vers leurs libellés), `CONSENT_VALUE = 'oui'`, `MESSAGE_MIN = 20`, `MESSAGE_MAX = 3000`, `static fromInput(array $input): self`, `isValid(): bool`, `needLabel(): string`, `values(): array<string, string>`, propriétés en lecture seule `name`, `organization`, `email`, `need`, `message`, `consent` (bool), `errors` (`array<string, string>`, clé = nom du champ). Noms des champs du formulaire : `name`, `organization`, `email`, `need`, `message`, `consent`.

- [ ] **Étape 1 : écrire le test qui échoue, `tests/Unit/Contact/ContactRequestTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactRequest;

final class ContactRequestTest extends TestCase
{
    /** @return array<string, string> */
    private static function validInput(): array
    {
        return [
            'name' => 'Awa Dossou',
            'organization' => 'Coopérative Agbo',
            'email' => 'awa@example.bj',
            'need' => 'management',
            'message' => 'Nous voulons suivre nos ventes depuis le téléphone.',
            'consent' => 'oui',
        ];
    }

    public function testValidInputHasNoErrors(): void
    {
        $request = ContactRequest::fromInput(self::validInput());

        self::assertTrue($request->isValid());
        self::assertSame([], $request->errors);
        self::assertSame('Application de gestion', $request->needLabel());
        self::assertSame('Awa Dossou', $request->name);
    }

    public function testOrganizationIsOptional(): void
    {
        self::assertTrue(ContactRequest::fromInput([...self::validInput(), 'organization' => ''])->isValid());
    }

    /** @return iterable<string, array{array<string, mixed>, string, string}> */
    public static function invalidInputs(): iterable
    {
        yield 'nom absent' => [['name' => ''], 'name', 'Indiquez votre nom.'];
        yield 'nom trop court' => [['name' => 'A'], 'name', 'Votre nom doit contenir entre 2 et 100 caractères.'];
        yield 'nom trop long' => [['name' => str_repeat('a', 101)], 'name', 'Votre nom doit contenir entre 2 et 100 caractères.'];
        yield 'nom envoyé en tableau' => [['name' => ['Awa']], 'name', 'Indiquez votre nom.'];
        yield 'organisation trop longue' => [['organization' => str_repeat('o', 121)], 'organization', "Le nom de l'organisation ne doit pas dépasser 120 caractères."];
        yield 'e-mail absent' => [['email' => ''], 'email', 'Indiquez votre adresse e-mail.'];
        yield 'e-mail invalide' => [['email' => 'awa@'], 'email', "Cette adresse e-mail n'est pas valide."];
        yield 'besoin inconnu' => [['need' => 'pirater'], 'need', 'Choisissez le type de besoin.'];
        yield 'message vide' => [['message' => '   '], 'message', 'Décrivez votre besoin.'];
        yield 'message trop court' => [['message' => 'Bonjour'], 'message', 'Votre message doit contenir au moins 20 caractères.'];
        yield 'message trop long' => [['message' => str_repeat('m', 3001)], 'message', "Votre message ne doit pas dépasser 3\u{00A0}000 caractères."];
        yield 'accord absent' => [['consent' => ''], 'consent', 'Cochez la case pour que nous puissions vous répondre.'];
    }

    /**
     * @param array<string, mixed> $override
     */
    #[DataProvider('invalidInputs')]
    public function testInvalidInputIsReportedOnTheRightField(array $override, string $field, string $message): void
    {
        $request = ContactRequest::fromInput([...self::validInput(), ...$override]);

        self::assertFalse($request->isValid());
        self::assertSame($message, $request->errors[$field] ?? null);
    }

    public function testLimitsAreCountedInCharactersNotBytes(): void
    {
        $request = ContactRequest::fromInput([
            ...self::validInput(),
            'name' => str_repeat('é', 100),
            'message' => str_repeat('è', 3000),
        ]);

        self::assertTrue($request->isValid());
    }

    public function testControlCharactersAreRemovedFromSingleLineFields(): void
    {
        $request = ContactRequest::fromInput([
            ...self::validInput(),
            'name' => "Awa\r\nBcc: pirate@example.com",
            'organization' => "Agbo\x00",
        ]);

        self::assertSame('Awa Bcc: pirate@example.com', $request->name);
        self::assertSame('Agbo', $request->organization);
    }

    public function testMessageKeepsNormalizedLineBreaks(): void
    {
        $request = ContactRequest::fromInput([...self::validInput(), 'message' => "Première ligne assez longue\r\nDeuxième ligne"]);

        self::assertSame("Première ligne assez longue\nDeuxième ligne", $request->message);
    }

    public function testInvalidUtf8IsTreatedAsEmpty(): void
    {
        $request = ContactRequest::fromInput([...self::validInput(), 'name' => "\xC3\x28"]);

        self::assertSame('Indiquez votre nom.', $request->errors['name'] ?? null);
    }

    public function testValuesAreReturnedForRedisplay(): void
    {
        $values = ContactRequest::fromInput([...self::validInput(), 'email' => 'pas-valide'])->values();

        self::assertSame('pas-valide', $values['email']);
        self::assertSame('oui', $values['consent']);
        self::assertSame('management', $values['need']);
    }
}
```

- [ ] **Étape 2 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter ContactRequestTest
```

Attendu : échec, classe `ContactRequest` introuvable.

- [ ] **Étape 3 : écrire `src/Contact/ContactRequest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

/**
 * Demande envoyée par le formulaire de contact, nettoyée puis validée.
 */
final class ContactRequest
{
    public const NEEDS = [
        'management' => 'Application de gestion',
        'platform' => 'Plateforme en ligne',
        'website' => 'Site web ou produit numérique',
        'security' => 'Sécurité et conformité',
        'hosting' => 'Hébergement et suivi',
        'other' => 'Autre',
    ];

    public const CONSENT_VALUE = 'oui';

    public const MESSAGE_MIN = 20;

    public const MESSAGE_MAX = 3000;

    /**
     * @param array<string, string> $errors
     */
    private function __construct(
        public readonly string $name,
        public readonly string $organization,
        public readonly string $email,
        public readonly string $need,
        public readonly string $message,
        public readonly bool $consent,
        public readonly array $errors,
    ) {
    }

    /**
     * @param array<array-key, mixed> $input
     */
    public static function fromInput(array $input): self
    {
        $name = self::singleLine($input['name'] ?? '');
        $organization = self::singleLine($input['organization'] ?? '');
        $email = self::singleLine($input['email'] ?? '');
        $need = self::singleLine($input['need'] ?? '');
        $message = self::multiLine($input['message'] ?? '');
        $consent = ($input['consent'] ?? '') === self::CONSENT_VALUE;

        $errors = [];
        $nameLength = mb_strlen($name);
        if ($nameLength === 0) {
            $errors['name'] = 'Indiquez votre nom.';
        } elseif ($nameLength < 2 || $nameLength > 100) {
            $errors['name'] = 'Votre nom doit contenir entre 2 et 100 caractères.';
        }
        if (mb_strlen($organization) > 120) {
            $errors['organization'] = "Le nom de l'organisation ne doit pas dépasser 120 caractères.";
        }
        if ($email === '') {
            $errors['email'] = 'Indiquez votre adresse e-mail.';
        } elseif (mb_strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = "Cette adresse e-mail n'est pas valide.";
        }
        if (!array_key_exists($need, self::NEEDS)) {
            $errors['need'] = 'Choisissez le type de besoin.';
        }
        $messageLength = mb_strlen($message);
        if ($messageLength === 0) {
            $errors['message'] = 'Décrivez votre besoin.';
        } elseif ($messageLength < self::MESSAGE_MIN) {
            $errors['message'] = 'Votre message doit contenir au moins 20 caractères.';
        } elseif ($messageLength > self::MESSAGE_MAX) {
            $errors['message'] = "Votre message ne doit pas dépasser 3\u{00A0}000 caractères.";
        }
        if (!$consent) {
            $errors['consent'] = 'Cochez la case pour que nous puissions vous répondre.';
        }

        return new self($name, $organization, $email, $need, $message, $consent, $errors);
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function needLabel(): string
    {
        return self::NEEDS[$this->need] ?? '';
    }

    /**
     * Valeurs nettoyées, pour réafficher le formulaire.
     *
     * @return array<string, string>
     */
    public function values(): array
    {
        return [
            'name' => $this->name,
            'organization' => $this->organization,
            'email' => $this->email,
            'need' => $this->need,
            'message' => $this->message,
            'consent' => $this->consent ? self::CONSENT_VALUE : '',
        ];
    }

    /**
     * Retire les caractères de contrôle, dont les retours à la ligne : aucune injection d'en-tête possible.
     */
    private static function singleLine(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }
        $clean = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value);
        if ($clean === null) {
            return '';
        }

        return trim((string) preg_replace('/\s+/u', ' ', $clean));
    }

    private static function multiLine(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }
        $normalized = str_replace(["\r\n", "\r"], "\n", $value);
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $normalized);

        return $clean === null ? '' : trim($clean);
    }
}
```

- [ ] **Étape 4 : relancer le test, puis committer**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter ContactRequestTest
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
git add src/Contact/ContactRequest.php tests/Unit/Contact/ContactRequestTest.php
git commit -F - <<'EOF'
feat(contact): valide et nettoie une demande de contact
EOF
```

Attendu : 19 tests verts avant le commit.

### Tâche 17 : horloge et jeton signé

**Fichiers :**

- Créer : `src/Clock/Clock.php`, `src/Clock/SystemClock.php`, `src/Contact/TokenStatus.php`, `src/Contact/FormToken.php`, `tests/Support/FixedClock.php`, `tests/Unit/Contact/FormTokenTest.php`

**Interfaces :**

- Produit : `interface Technum\Clock\Clock { public function now(): int; }` (horodatage Unix en secondes), `SystemClock`. `enum Technum\Contact\TokenStatus { Valid, Invalid, TooFast, Expired }`. `Technum\Contact\FormToken` avec `MIN_AGE_SECONDS = 3`, `MAX_AGE_SECONDS = 7200`, `__construct(string $secret)` (au moins 32 caractères, sinon `InvalidArgumentException`), `issue(int $now): string` au format `horodatage.signature`, `verify(string $token, int $now): TokenStatus`. `Technum\Tests\Support\FixedClock` avec `__construct(int $now)`, `now(): int`, `advance(int $seconds): void`.

- [ ] **Étape 1 : écrire le test qui échoue, `tests/Unit/Contact/FormTokenTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Contact\FormToken;
use Technum\Contact\TokenStatus;

final class FormTokenTest extends TestCase
{
    private FormToken $token;

    protected function setUp(): void
    {
        $this->token = new FormToken(str_repeat('s', 40));
    }

    public function testFreshTokenIsValidOnceTheMinimumDelayHasPassed(): void
    {
        $issued = $this->token->issue(1000);

        self::assertSame(TokenStatus::Valid, $this->token->verify($issued, 1003));
        self::assertSame(TokenStatus::Valid, $this->token->verify($issued, 1000 + FormToken::MAX_AGE_SECONDS));
    }

    public function testTokenSubmittedTooFastIsFlagged(): void
    {
        self::assertSame(TokenStatus::TooFast, $this->token->verify($this->token->issue(1000), 1002));
    }

    public function testOldTokenIsExpired(): void
    {
        $issued = $this->token->issue(1000);

        self::assertSame(TokenStatus::Expired, $this->token->verify($issued, 1000 + FormToken::MAX_AGE_SECONDS + 1));
    }

    public function testTamperedTokensAreInvalid(): void
    {
        [$timestamp, $signature] = explode('.', $this->token->issue(1000));

        self::assertSame(TokenStatus::Invalid, $this->token->verify($timestamp . '.' . strrev($signature), 1010));
        self::assertSame(TokenStatus::Invalid, $this->token->verify('999.' . $signature, 1010));
    }

    /** @return iterable<string, array{string}> */
    public static function malformedTokens(): iterable
    {
        yield 'vide' => [''];
        yield 'sans point' => ['abc'];
        yield 'horodatage seul' => ['1000'];
        yield 'trois parties' => ['1000.ab.cd'];
        yield 'horodatage non numérique' => ['abc.def'];
    }

    #[DataProvider('malformedTokens')]
    public function testMalformedTokensAreInvalid(string $token): void
    {
        self::assertSame(TokenStatus::Invalid, $this->token->verify($token, 1010));
    }

    public function testTokenFromAnotherSecretIsInvalid(): void
    {
        $other = new FormToken(str_repeat('o', 40));

        self::assertSame(TokenStatus::Invalid, $this->token->verify($other->issue(1000), 1010));
    }

    public function testShortSecretIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FormToken('court');
    }
}
```

- [ ] **Étape 2 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter FormTokenTest
```

Attendu : échec, classe `FormToken` introuvable.

- [ ] **Étape 3 : écrire l'horloge**

`src/Clock/Clock.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Clock;

interface Clock
{
    /**
     * Horodatage Unix, en secondes.
     */
    public function now(): int;
}
```

`src/Clock/SystemClock.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Clock;

final class SystemClock implements Clock
{
    public function now(): int
    {
        return time();
    }
}
```

`tests/Support/FixedClock.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Support;

use Technum\Clock\Clock;

final class FixedClock implements Clock
{
    public function __construct(private int $now)
    {
    }

    public function now(): int
    {
        return $this->now;
    }

    public function advance(int $seconds): void
    {
        $this->now += $seconds;
    }
}
```

- [ ] **Étape 4 : écrire `src/Contact/TokenStatus.php` et `src/Contact/FormToken.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

enum TokenStatus
{
    case Valid;
    case Invalid;
    case TooFast;
    case Expired;
}
```

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

use InvalidArgumentException;

/**
 * Jeton signé qui date l'affichage du formulaire. Il remplace une session : le site ne dépose aucun cookie.
 */
final class FormToken
{
    public const MIN_AGE_SECONDS = 3;

    public const MAX_AGE_SECONDS = 7200;

    public function __construct(private readonly string $secret)
    {
        if (strlen($secret) < 32) {
            throw new InvalidArgumentException('Le secret du formulaire doit contenir au moins 32 caractères.');
        }
    }

    public function issue(int $now): string
    {
        $timestamp = (string) $now;

        return $timestamp . '.' . $this->sign($timestamp);
    }

    public function verify(string $token, int $now): TokenStatus
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2 || !ctype_digit($parts[0]) || !hash_equals($this->sign($parts[0]), $parts[1])) {
            return TokenStatus::Invalid;
        }
        $age = $now - (int) $parts[0];
        if ($age < self::MIN_AGE_SECONDS) {
            return TokenStatus::TooFast;
        }
        if ($age > self::MAX_AGE_SECONDS) {
            return TokenStatus::Expired;
        }

        return TokenStatus::Valid;
    }

    private function sign(string $timestamp): string
    {
        return hash_hmac('sha256', 'contact-form|' . $timestamp, $this->secret);
    }
}
```

- [ ] **Étape 5 : relancer le test, puis committer**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter FormTokenTest
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
git add src/Clock src/Contact/TokenStatus.php src/Contact/FormToken.php tests/Support/FixedClock.php tests/Unit/Contact/FormTokenTest.php
git commit -F - <<'EOF'
feat(contact): signe et date le formulaire avec un jeton sans session
EOF
```

### Tâche 18 : limite d'envois et journal de sécurité

**Fichiers :**

- Créer : `src/Security/RateLimiter.php`, `src/Security/SecurityLog.php`, `tests/Unit/Security/RateLimiterTest.php`, `tests/Unit/Security/SecurityLogTest.php`

**Interfaces :**

- Produit : `Technum\Security\RateLimiter` avec `__construct(string $directory, string $secret, int $maxHits = 5, int $windowSeconds = 3600)`, `isLimited(string $clientIp, int $now): bool`, `hit(string $clientIp, int $now): void`, `purgeExpired(int $now): void`. Les fichiers portent une empreinte HMAC de l'adresse, jamais l'adresse. `Technum\Security\SecurityLog` avec `MAX_BYTES = 1000000`, `__construct(string $file)`, `record(string $event, int $timestamp): void`. Un nom d'événement ne contient que des minuscules, des points et des tirets bas. Événements utilisés à la tâche 20 : `contact.honeypot`, `contact.token_invalid`, `contact.too_fast`, `contact.token_expired`, `contact.rate_limited`, `contact.mail_failed`.

- [ ] **Étape 1 : écrire les tests qui échouent**

`tests/Unit/Security/RateLimiterTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use Technum\Security\RateLimiter;
use Technum\Tests\Support\TempDirectory;

final class RateLimiterTest extends TestCase
{
    private const NOW = 1_790_000_000;

    private string $directory;

    private RateLimiter $limiter;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-rate');
        $this->limiter = new RateLimiter($this->directory, str_repeat('r', 40));
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    public function testAllowsFiveHitsPerHour(): void
    {
        for ($i = 0; $i < 4; ++$i) {
            $this->limiter->hit('203.0.113.10', self::NOW + $i);
        }
        self::assertFalse($this->limiter->isLimited('203.0.113.10', self::NOW + 10));

        $this->limiter->hit('203.0.113.10', self::NOW + 11);

        self::assertTrue($this->limiter->isLimited('203.0.113.10', self::NOW + 12));
    }

    public function testHitsExpireAfterOneHour(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->limiter->hit('203.0.113.10', self::NOW);
        }

        self::assertTrue($this->limiter->isLimited('203.0.113.10', self::NOW + 3599));
        self::assertFalse($this->limiter->isLimited('203.0.113.10', self::NOW + 3600));
    }

    public function testLimitIsKeptPerClient(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->limiter->hit('203.0.113.10', self::NOW);
        }

        self::assertFalse($this->limiter->isLimited('198.51.100.20', self::NOW));
    }

    public function testStoredFilesNeverContainTheAddress(): void
    {
        $this->limiter->hit('203.0.113.10', self::NOW);
        $files = glob($this->directory . '/*') ?: [];

        self::assertCount(1, $files);
        self::assertStringNotContainsString('203.0.113.10', basename($files[0]));
        self::assertMatchesRegularExpression('/^[0-9\n]+$/', (string) file_get_contents($files[0]));
    }

    public function testPurgeRemovesOnlyExpiredFiles(): void
    {
        $this->limiter->hit('203.0.113.10', self::NOW);
        $this->limiter->hit('198.51.100.20', self::NOW + 3000);

        $this->limiter->purgeExpired(self::NOW + 3600);

        self::assertCount(1, glob($this->directory . '/*.hits') ?: []);
    }
}
```

`tests/Unit/Security/SecurityLogTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Security;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Technum\Security\SecurityLog;
use Technum\Tests\Support\TempDirectory;

final class SecurityLogTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-log');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    public function testRecordsTheEventWithItsUtcTime(): void
    {
        $file = $this->directory . '/logs/security.log';

        (new SecurityLog($file))->record('contact.honeypot', 1_790_000_000);

        self::assertSame(gmdate('Y-m-d\TH:i:s\Z', 1_790_000_000) . " contact.honeypot\n", file_get_contents($file));
    }

    public function testRejectsEventsThatCouldCarryPersonalData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new SecurityLog($this->directory . '/security.log'))->record('contact.refus awa@example.bj', 1);
    }

    public function testRotatesTheFileWhenItGetsTooLarge(): void
    {
        $file = $this->directory . '/security.log';
        file_put_contents($file, str_repeat('x', SecurityLog::MAX_BYTES + 1));

        (new SecurityLog($file))->record('contact.rate_limited', 1_790_000_000);

        self::assertFileExists($file . '.1');
        self::assertLessThan(100, (int) filesize($file));
    }
}
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'RateLimiterTest|SecurityLogTest'
```

Attendu : échec, classes du namespace `Technum\Security` introuvables.

- [ ] **Étape 3 : écrire `src/Security/RateLimiter.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Security;

use RuntimeException;

/**
 * Limite le nombre de demandes envoyées par connexion. Chaque connexion est reconnue
 * par une empreinte HMAC de son adresse IP : l'adresse elle-même n'est jamais écrite.
 */
final class RateLimiter
{
    public function __construct(
        private readonly string $directory,
        private readonly string $secret,
        private readonly int $maxHits = 5,
        private readonly int $windowSeconds = 3600,
    ) {
    }

    public function isLimited(string $clientIp, int $now): bool
    {
        return count($this->recentHits($this->fileFor($clientIp), $now)) >= $this->maxHits;
    }

    public function hit(string $clientIp, int $now): void
    {
        $file = $this->fileFor($clientIp);
        $hits = [...$this->recentHits($file, $now), $now];
        $this->ensureDirectory();
        file_put_contents($file, implode("\n", $hits), LOCK_EX);
    }

    public function purgeExpired(int $now): void
    {
        foreach (glob($this->directory . '/*.hits') ?: [] as $file) {
            if ($this->recentHits($file, $now) === []) {
                unlink($file);
            }
        }
    }

    private function fileFor(string $clientIp): string
    {
        return $this->directory . '/' . hash_hmac('sha256', 'rate-limit|' . $clientIp, $this->secret) . '.hits';
    }

    /**
     * @return list<int>
     */
    private function recentHits(string $file, int $now): array
    {
        if (!is_file($file)) {
            return [];
        }
        $hits = [];
        foreach (explode("\n", (string) file_get_contents($file)) as $line) {
            if (ctype_digit($line) && (int) $line > $now - $this->windowSeconds) {
                $hits[] = (int) $line;
            }
        }

        return $hits;
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0750, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Dossier de limitation des envois inaccessible.');
        }
    }
}
```

- [ ] **Étape 4 : écrire `src/Security/SecurityLog.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Security;

use InvalidArgumentException;
use RuntimeException;

/**
 * Journal des refus du formulaire, sans aucune donnée personnelle.
 */
final class SecurityLog
{
    public const MAX_BYTES = 1_000_000;

    public function __construct(private readonly string $file)
    {
    }

    public function record(string $event, int $timestamp): void
    {
        if (preg_match('/^[a-z]+(\.[a-z_]+)+$/', $event) !== 1) {
            throw new InvalidArgumentException("Nom d'événement invalide : minuscules, points et tirets bas uniquement.");
        }
        $directory = dirname($this->file);
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new RuntimeException('Dossier du journal inaccessible.');
        }
        clearstatcache(true, $this->file);
        if (is_file($this->file) && (int) filesize($this->file) > self::MAX_BYTES) {
            rename($this->file, $this->file . '.1');
        }
        file_put_contents($this->file, gmdate('Y-m-d\TH:i:s\Z', $timestamp) . ' ' . $event . "\n", FILE_APPEND | LOCK_EX);
    }
}
```

- [ ] **Étape 5 : relancer les tests, puis committer**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'RateLimiterTest|SecurityLogTest'
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
git add src/Security tests/Unit/Security
git commit -F - <<'EOF'
feat(security): limite les envois par connexion et journalise les refus
EOF
```

### Tâche 19 : message et expéditeurs

**Fichiers :**

- Créer : `src/Contact/ContactMessage.php`, `src/Contact/MailerInterface.php`, `src/Contact/MailerException.php`, `src/Contact/SmtpMailer.php`, `src/Contact/LogMailer.php`, `tests/Support/FakeMailer.php`, `tests/Unit/Contact/ContactMessageTest.php`, `tests/Unit/Contact/LogMailerTest.php`
- Modifier : `composer.json` (PHPMailer)

**Interfaces :**

- Consomme : `ContactRequest` (tâche 16).
- Produit : `ContactMessage::fromRequest(ContactRequest $request): self` (lève `LogicException` si la demande n'est pas valide), propriétés `replyToEmail`, `replyToName`, `subject`, `body`. `interface MailerInterface { public function send(ContactMessage $message): void; }`, qui lève `MailerException` en cas d'échec. `SmtpMailer(string $host, int $port, string $username, string $password, string $senderEmail, string $recipientEmail)`. `LogMailer(string $file)`, pour le développement local. `Technum\Tests\Support\FakeMailer` avec `public array $sent` (`list<ContactMessage>`) et `public bool $fails`.

- [ ] **Étape 1 : installer PHPMailer**

```bash
cd /c/wamp64/www/TECHNUM
composer require phpmailer/phpmailer
```

- [ ] **Étape 2 : écrire les tests qui échouent**

`tests/Unit/Contact/ContactMessageTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use LogicException;
use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactMessage;
use Technum\Contact\ContactRequest;

final class ContactMessageTest extends TestCase
{
    public function testMessageCarriesTheRequestAndRepliesToTheVisitor(): void
    {
        $request = ContactRequest::fromInput([
            'name' => 'Awa Dossou',
            'organization' => '',
            'email' => 'awa@example.bj',
            'need' => 'platform',
            'message' => "Une plateforme pour nos adhérents.\nAvec deux rôles.",
            'consent' => 'oui',
        ]);

        $message = ContactMessage::fromRequest($request);

        self::assertSame('awa@example.bj', $message->replyToEmail);
        self::assertSame('Awa Dossou', $message->replyToName);
        self::assertSame('Demande de projet : Plateforme en ligne, Awa Dossou', $message->subject);
        self::assertStringContainsString('Organisation : non précisée', $message->body);
        self::assertStringContainsString("Une plateforme pour nos adhérents.\nAvec deux rôles.", $message->body);
    }

    public function testInvalidRequestCannotBecomeAMessage(): void
    {
        $this->expectException(LogicException::class);

        ContactMessage::fromRequest(ContactRequest::fromInput([]));
    }
}
```

`tests/Unit/Contact/LogMailerTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactMessage;
use Technum\Contact\ContactRequest;
use Technum\Contact\LogMailer;
use Technum\Contact\MailerException;
use Technum\Tests\Support\TempDirectory;

final class LogMailerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-mail');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    public function testWritesTheMessageToTheLocalFile(): void
    {
        $file = $this->directory . '/mail-local.log';

        (new LogMailer($file))->send(self::message());

        $content = (string) file_get_contents($file);
        self::assertStringContainsString('Répondre à : Awa Dossou <awa@example.bj>', $content);
        self::assertStringContainsString('Objet : Demande de projet : Application de gestion, Awa Dossou', $content);
    }

    public function testMissingDirectoryRaisesMailerException(): void
    {
        $this->expectException(MailerException::class);

        (new LogMailer($this->directory . '/absent/mail-local.log'))->send(self::message());
    }

    private static function message(): ContactMessage
    {
        return ContactMessage::fromRequest(ContactRequest::fromInput([
            'name' => 'Awa Dossou',
            'organization' => 'Coopérative Agbo',
            'email' => 'awa@example.bj',
            'need' => 'management',
            'message' => 'Nous voulons suivre nos ventes depuis le téléphone.',
            'consent' => 'oui',
        ]));
    }
}
```

- [ ] **Étape 3 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'ContactMessageTest|LogMailerTest'
```

Attendu : échec, classes `ContactMessage` et `LogMailer` introuvables.

- [ ] **Étape 4 : écrire le message, l'interface et l'exception**

`src/Contact/ContactMessage.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

use LogicException;

/**
 * E-mail envoyé à TECHNUM pour une demande valide. Répondre au message écrit au demandeur.
 */
final class ContactMessage
{
    private function __construct(
        public readonly string $replyToEmail,
        public readonly string $replyToName,
        public readonly string $subject,
        public readonly string $body,
    ) {
    }

    public static function fromRequest(ContactRequest $request): self
    {
        if (!$request->isValid()) {
            throw new LogicException('Une demande invalide ne peut pas être envoyée.');
        }
        $body = implode("\n", [
            'Nom : ' . $request->name,
            'Organisation : ' . ($request->organization !== '' ? $request->organization : 'non précisée'),
            'E-mail : ' . $request->email,
            'Besoin : ' . $request->needLabel(),
            '',
            'Message :',
            $request->message,
            '',
            '-- ',
            'Envoyé depuis le formulaire de bytechnum.com. Répondre à ce message écrit directement au demandeur.',
        ]);

        return new self(
            $request->email,
            $request->name,
            'Demande de projet : ' . $request->needLabel() . ', ' . $request->name,
            $body,
        );
    }
}
```

`src/Contact/MailerInterface.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

interface MailerInterface
{
    /**
     * @throws MailerException quand l'envoi échoue
     */
    public function send(ContactMessage $message): void;
}
```

`src/Contact/MailerException.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

use RuntimeException;

final class MailerException extends RuntimeException
{
}
```

- [ ] **Étape 5 : écrire les deux expéditeurs**

`src/Contact/SmtpMailer.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Envoi par le SMTP de la messagerie Spacemail. Le détail d'un échec n'est jamais journalisé :
 * il peut contenir l'adresse du demandeur.
 */
final class SmtpMailer implements MailerInterface
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $username,
        private readonly string $password,
        private readonly string $senderEmail,
        private readonly string $recipientEmail,
    ) {
    }

    public function send(ContactMessage $message): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->Port = $this->port;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Timeout = 15;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->setFrom($this->senderEmail, 'Site TECHNUM');
            $mail->Sender = $this->senderEmail;
            $mail->addAddress($this->recipientEmail);
            $mail->addReplyTo($message->replyToEmail, $message->replyToName);
            $mail->Subject = $message->subject;
            $mail->isHTML(false);
            $mail->Body = $message->body;
            $mail->send();
        } catch (PHPMailerException $exception) {
            throw new MailerException("L'envoi SMTP a échoué.", 0, $exception);
        }
    }
}
```

`src/Contact/LogMailer.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

/**
 * Expéditeur du développement local : écrit chaque demande dans un fichier au lieu de l'envoyer.
 */
final class LogMailer implements MailerInterface
{
    public function __construct(private readonly string $file)
    {
    }

    public function send(ContactMessage $message): void
    {
        $directory = dirname($this->file);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new MailerException('Dossier du journal local inaccessible.');
        }
        $entry = '--- ' . gmdate('c') . "\n"
            . 'Répondre à : ' . $message->replyToName . ' <' . $message->replyToEmail . ">\n"
            . 'Objet : ' . $message->subject . "\n\n"
            . $message->body . "\n\n";
        if (file_put_contents($this->file, $entry, FILE_APPEND | LOCK_EX) === false) {
            throw new MailerException('Écriture du journal local impossible.');
        }
    }
}
```

`tests/Support/FakeMailer.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Support;

use Technum\Contact\ContactMessage;
use Technum\Contact\MailerException;
use Technum\Contact\MailerInterface;

final class FakeMailer implements MailerInterface
{
    /** @var list<ContactMessage> */
    public array $sent = [];

    public bool $fails = false;

    public function send(ContactMessage $message): void
    {
        if ($this->fails) {
            throw new MailerException('Échec simulé.');
        }
        $this->sent[] = $message;
    }
}
```

- [ ] **Étape 6 : vérification complète, commit, pull request du lot**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
git add composer.json composer.lock src/Contact tests/Support/FakeMailer.php tests/Unit/Contact
git commit -F - <<'EOF'
feat(contact): compose le message et ajoute les expéditeurs smtp et local
EOF
```

Pull request du lot :

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Règles du formulaire de contact" --body-file - <<EOF
## Objectif

Poser les règles du formulaire de contact, avant son branchement sur la page. Issue liée : #$ISSUE

## Changements

- Validation et nettoyage d'une demande, sans injection d'en-tête possible
- Jeton signé et daté, qui remplace une session
- Limite de cinq demandes par heure et par connexion, sur une empreinte de l'adresse IP
- Journal des refus sans donnée personnelle, avec rotation
- Message envoyé à TECHNUM, expéditeur SMTP Spacemail et expéditeur local

## Tests

- [x] Tests unitaires de chaque règle, cas limites et cas d'erreur compris
- [x] Analyse statique et mise en forme sans erreur

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de secret exposé
- [x] Aucune donnée personnelle dans les journaux
- [x] Taille : environ 700 lignes, dont 400 de tests
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

---

## Lot 6 : envoi du formulaire de contact

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Envoi du formulaire de contact" --body "Contrôleur de contact, formulaire accessible sans JavaScript, styles et aide à la saisie." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-envoi-formulaire"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

### Tâche 20 : contrôleur de contact et formulaire

**Fichiers :**

- Créer : `src/Contact/ContactFormState.php`, `src/Controller/ContactController.php`, `templates/partials/contact-form.php`, `tests/Unit/Contact/ContactFormStateTest.php`, `tests/Integration/ContactSubmissionTest.php`
- Modifier : `src/Page/HomePage.php`, `src/Controller/HomeController.php`, `src/Application.php`, `templates/home.php`, `tests/Integration/ApplicationTestCase.php`, `tests/Unit/Page/HomePageTest.php`

**Interfaces :**

- Consomme : `ContactRequest`, `FormToken`, `TokenStatus`, `RateLimiter`, `SecurityLog`, `ContactMessage`, `MailerInterface`, `LogMailer`, `SmtpMailer`, `Clock`, `SystemClock`, `SiteInfo`.
- Produit :
  - `ContactFormState(string $token, array $values = [], array $errors = [], string $notice = '', bool $sent = false)` avec `value()`, `error()`, `hasError()`, `errorId(string $field): string` (`contact-{champ}-erreur`) et `ariaAttributes(string $field, string $helpId = ''): string`, qui renvoie les attributs `aria-invalid` et `aria-describedby` déjà échappés.
  - `HomePage::render(ContactFormState $form): string`, qui passe aussi `needs` (`ContactRequest::NEEDS`) au gabarit.
  - `HomeController(HomePage $homePage, FormToken $formToken, Clock $clock)` : `?envoi=ok` affiche la confirmation.
  - `ContactController::submit(Request $request): Response` avec la constante `SUCCESS_LOCATION = '/?envoi=ok#contact'`.
  - `Application::create(string $rootDir, Config $config, ?MailerInterface $mailer = null, ?Clock $clock = null, ?string $storageDir = null): self`, route `POST /contact`.

Ordre des contrôles du contrôleur : champ piège rempli, jeton falsifié ou envoi trop rapide (réponse 303 comme un succès, rien n'est envoyé, l'événement est journalisé), jeton expiré (422 et message), champs invalides (422), limite atteinte (429), échec d'envoi (503), puis succès (303 vers `/?envoi=ok#contact`).

- [ ] **Étape 1 : réécrire la base des tests d'intégration**

`tests/Integration/ApplicationTestCase.php` devient :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Technum\Application;
use Technum\Config;
use Technum\Contact\FormToken;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Tests\Support\FakeMailer;
use Technum\Tests\Support\FixedClock;
use Technum\Tests\Support\TempDirectory;

abstract class ApplicationTestCase extends TestCase
{
    protected const ROOT = __DIR__ . '/../..';

    protected const SECRET = 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk';

    protected const NOW = 1_790_000_000;

    protected FakeMailer $mailer;

    protected FixedClock $clock;

    protected string $storageDir;

    protected function setUp(): void
    {
        $this->mailer = new FakeMailer();
        $this->clock = new FixedClock(self::NOW);
        $this->storageDir = TempDirectory::create('technum-storage');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->storageDir);
    }

    /**
     * @param array<string, string> $env
     */
    protected function application(array $env = []): Application
    {
        return Application::create(
            self::ROOT,
            Config::fromArray([...self::localEnv(), ...$env]),
            $this->mailer,
            $this->clock,
            $this->storageDir,
        );
    }

    /**
     * @return array<string, string>
     */
    protected static function localEnv(): array
    {
        return [
            'APP_ENV' => 'local',
            'APP_SECRET' => self::SECRET,
            'MAIL_TRANSPORT' => 'log',
            'CONTACT_SENDER_EMAIL' => 'elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL' => 'elisee.atonde@bytechnum.com',
        ];
    }

    /**
     * @param array<string, string> $query
     */
    protected function get(string $path, array $query = [], ?Application $application = null): Response
    {
        $request = new Request('GET', Request::normalizePath($path), $query, [], '203.0.113.10');

        return ($application ?? $this->application())->handle($request);
    }

    /**
     * @param array<array-key, mixed> $body
     */
    protected function post(string $path, array $body, string $clientIp = '203.0.113.10'): Response
    {
        return $this->application()->handle(new Request('POST', Request::normalizePath($path), [], $body, $clientIp));
    }

    protected function tokenIssuedSecondsAgo(int $seconds): string
    {
        return (new FormToken(self::SECRET))->issue(self::NOW - $seconds);
    }
}
```

- [ ] **Étape 2 : écrire les tests qui échouent**

`tests/Unit/Contact/ContactFormStateTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactFormState;

final class ContactFormStateTest extends TestCase
{
    public function testFieldWithoutErrorOrHelpHasNoAriaAttributes(): void
    {
        self::assertSame('', (new ContactFormState('jeton'))->ariaAttributes('name'));
    }

    public function testHelpTextIsLinkedToItsField(): void
    {
        self::assertSame(
            ' aria-describedby="contact-message-aide"',
            (new ContactFormState('jeton'))->ariaAttributes('message', 'contact-message-aide'),
        );
    }

    public function testErrorMarksTheFieldInvalidAndLinksTheMessage(): void
    {
        $state = new ContactFormState('jeton', ['message' => 'court'], ['message' => 'Trop court.']);

        self::assertSame(
            ' aria-invalid="true" aria-describedby="contact-message-aide contact-message-erreur"',
            $state->ariaAttributes('message', 'contact-message-aide'),
        );
        self::assertSame('court', $state->value('message'));
        self::assertSame('', $state->value('name'));
        self::assertSame('Trop court.', $state->error('message'));
    }
}
```

`tests/Integration/ContactSubmissionTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use Technum\Tests\Support\Html;

final class ContactSubmissionTest extends ApplicationTestCase
{
    /**
     * @return array<string, string>
     */
    private function validForm(int $tokenAge = 30): array
    {
        return [
            'token' => $this->tokenIssuedSecondsAgo($tokenAge),
            'website' => '',
            'name' => 'Awa Dossou',
            'organization' => 'Coopérative Agbo',
            'email' => 'awa@example.bj',
            'need' => 'management',
            'message' => 'Nous voulons suivre nos ventes depuis le téléphone.',
            'consent' => 'oui',
        ];
    }

    private function securityLog(): string
    {
        $file = $this->storageDir . '/logs/security.log';

        return is_file($file) ? (string) file_get_contents($file) : '';
    }

    public function testValidRequestSendsOneEmailAndRedirectsToTheConfirmation(): void
    {
        $response = $this->post('/contact', $this->validForm());

        self::assertSame(303, $response->status);
        self::assertSame('/?envoi=ok#contact', $response->header('Location'));
        self::assertCount(1, $this->mailer->sent);
        self::assertSame('awa@example.bj', $this->mailer->sent[0]->replyToEmail);
        self::assertStringContainsString('Application de gestion', $this->mailer->sent[0]->subject);
    }

    public function testConfirmationIsShownAfterTheRedirect(): void
    {
        $html = Html::parse($this->get('/', ['envoi' => 'ok'])->body);

        self::assertSame("Demande envoyée. Nous vous répondons à l'adresse indiquée.", $html->text('.form-status--success'));
    }

    public function testFormPostsToTheContactAnchorAndWorksWithoutJavascript(): void
    {
        $html = Html::parse($this->get('/')->body);

        self::assertSame('/contact#contact', $html->attribute('form.contact-form', 'action'));
        self::assertSame('post', $html->attribute('form.contact-form', 'method'));
        self::assertNotSame('', $html->attribute('input[name="token"]', 'value'));
        self::assertSame('-1', $html->attribute('#contact-website', 'tabindex'));
        self::assertSame(0, $html->count('#contact-consent[checked]'));
        self::assertSame(0, $html->count('.form-status'));
    }

    public function testInvalidRequestIsRedisplayedWithErrorsAndValues(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'name' => '', 'message' => 'Trop court']);
        $html = Html::parse($response->body);

        self::assertSame(422, $response->status);
        self::assertSame('Indiquez votre nom.', $html->text('#contact-name-erreur'));
        self::assertSame('true', $html->attribute('#contact-name', 'aria-invalid'));
        self::assertSame('contact-name-erreur', $html->attribute('#contact-name', 'aria-describedby'));
        self::assertSame('Trop court', $html->text('#contact-message'));
        self::assertSame('awa@example.bj', $html->attribute('#contact-email', 'value'));
        self::assertSame('management', $html->attribute('#contact-need option[selected]', 'value'));
        self::assertSame([], $this->mailer->sent);
    }

    public function testRedisplayedValuesAreEscaped(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'name' => '<script>alert(1)</script>', 'email' => 'pas-valide']);

        self::assertSame(422, $response->status);
        self::assertStringNotContainsString('<script>alert(1)</script>', $response->body);
        self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $response->body);
    }

    public function testHoneypotSubmissionIsSilentlyDropped(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'website' => 'https://spam.example']);

        self::assertSame(303, $response->status);
        self::assertSame([], $this->mailer->sent);
        self::assertStringContainsString('contact.honeypot', $this->securityLog());
    }

    public function testTamperedTokenIsSilentlyDropped(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'token' => '123.abc']);

        self::assertSame(303, $response->status);
        self::assertSame([], $this->mailer->sent);
        self::assertStringContainsString('contact.token_invalid', $this->securityLog());
    }

    public function testTooFastSubmissionIsSilentlyDropped(): void
    {
        $response = $this->post('/contact', $this->validForm(1));

        self::assertSame(303, $response->status);
        self::assertSame([], $this->mailer->sent);
        self::assertStringContainsString('contact.too_fast', $this->securityLog());
    }

    public function testExpiredTokenAsksToSendAgainAndKeepsValues(): void
    {
        $response = $this->post('/contact', $this->validForm(7201));
        $html = Html::parse($response->body);

        self::assertSame(422, $response->status);
        self::assertStringContainsString('Le formulaire a expiré', $html->text('.form-status--error'));
        self::assertSame('Awa Dossou', $html->attribute('#contact-name', 'value'));
        self::assertSame([], $this->mailer->sent);
    }

    public function testSixthValidRequestWithinAnHourIsRateLimited(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            self::assertSame(303, $this->post('/contact', $this->validForm())->status);
        }

        $response = $this->post('/contact', $this->validForm());

        self::assertSame(429, $response->status);
        self::assertStringContainsString('+229 01 50 61 73 00', Html::parse($response->body)->text('.form-status--error'));
        self::assertCount(5, $this->mailer->sent);
    }

    public function testRateLimitIsKeptPerClientAddress(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->post('/contact', $this->validForm(), '203.0.113.10');
        }

        self::assertSame(303, $this->post('/contact', $this->validForm(), '198.51.100.20')->status);
    }

    public function testMailFailureOffersTheDirectContacts(): void
    {
        $this->mailer->fails = true;

        $response = $this->post('/contact', $this->validForm());
        $notice = Html::parse($response->body)->text('.form-status--error');

        self::assertSame(503, $response->status);
        self::assertStringContainsString('+229 01 50 61 73 00', $notice);
        self::assertStringContainsString('elisee.atonde@bytechnum.com', $notice);
        self::assertStringContainsString('contact.mail_failed', $this->securityLog());
    }

    public function testArrayValuedFieldsNeverCauseAServerError(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'name' => ['Awa'], 'message' => ['x']]);

        self::assertSame(422, $response->status);
    }
}
```

- [ ] **Étape 3 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'ContactFormStateTest|ContactSubmissionTest'
```

Attendu : échec, `ContactFormState` introuvable et `Application::create()` qui n'accepte pas encore l'expéditeur.

- [ ] **Étape 4 : écrire `src/Contact/ContactFormState.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Contact;

/**
 * État du formulaire affiché : jeton, valeurs saisies, erreurs par champ, message général.
 */
final class ContactFormState
{
    /**
     * @param array<string, string> $values
     * @param array<string, string> $errors
     */
    public function __construct(
        public readonly string $token,
        public readonly array $values = [],
        public readonly array $errors = [],
        public readonly string $notice = '',
        public readonly bool $sent = false,
    ) {
    }

    public function value(string $field): string
    {
        return $this->values[$field] ?? '';
    }

    public function error(string $field): string
    {
        return $this->errors[$field] ?? '';
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function errorId(string $field): string
    {
        return 'contact-' . $field . '-erreur';
    }

    /**
     * Attributs ARIA d'un champ, déjà échappés : état invalide et textes qui le décrivent.
     */
    public function ariaAttributes(string $field, string $helpId = ''): string
    {
        $describedBy = array_filter([$helpId, $this->hasError($field) ? $this->errorId($field) : '']);
        $attributes = $this->hasError($field) ? ' aria-invalid="true"' : '';
        if ($describedBy !== []) {
            $attributes .= ' aria-describedby="' . e(implode(' ', $describedBy)) . '"';
        }

        return $attributes;
    }
}
```

- [ ] **Étape 5 : écrire `src/Controller/ContactController.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Controller;

use Technum\Clock\Clock;
use Technum\Contact\ContactFormState;
use Technum\Contact\ContactMessage;
use Technum\Contact\ContactRequest;
use Technum\Contact\FormToken;
use Technum\Contact\MailerException;
use Technum\Contact\MailerInterface;
use Technum\Contact\TokenStatus;
use Technum\Content\SiteInfo;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Page\HomePage;
use Technum\Security\RateLimiter;
use Technum\Security\SecurityLog;

final class ContactController
{
    public const SUCCESS_LOCATION = '/?envoi=ok#contact';

    public function __construct(
        private readonly HomePage $homePage,
        private readonly FormToken $formToken,
        private readonly RateLimiter $rateLimiter,
        private readonly MailerInterface $mailer,
        private readonly SecurityLog $securityLog,
        private readonly Clock $clock,
        private readonly SiteInfo $site,
    ) {
    }

    public function submit(Request $request): Response
    {
        $now = $this->clock->now();
        if ($request->input('website') !== '') {
            return $this->dropSilently('contact.honeypot', $now);
        }
        $tokenStatus = $this->formToken->verify($request->input('token'), $now);
        if ($tokenStatus === TokenStatus::Invalid) {
            return $this->dropSilently('contact.token_invalid', $now);
        }
        if ($tokenStatus === TokenStatus::TooFast) {
            return $this->dropSilently('contact.too_fast', $now);
        }

        $contact = ContactRequest::fromInput($request->body);
        if ($tokenStatus === TokenStatus::Expired) {
            $this->securityLog->record('contact.token_expired', $now);

            return $this->redisplay($contact, $now, 422, 'Le formulaire a expiré. Vérifiez vos informations, puis envoyez-les de nouveau.', $contact->errors);
        }
        if (!$contact->isValid()) {
            return $this->redisplay($contact, $now, 422, "La demande n'est pas partie. Corrigez les champs signalés.", $contact->errors);
        }
        if ($this->rateLimiter->isLimited($request->clientIp, $now)) {
            $this->securityLog->record('contact.rate_limited', $now);

            return $this->redisplay($contact, $now, 429, sprintf(
                'Trop de demandes depuis cette connexion. Réessayez dans une heure, ou écrivez-nous sur WhatsApp au %s.',
                $this->site->phoneDisplay,
            ));
        }

        try {
            $this->mailer->send(ContactMessage::fromRequest($contact));
        } catch (MailerException) {
            $this->securityLog->record('contact.mail_failed', $now);

            return $this->redisplay($contact, $now, 503, sprintf(
                "L'envoi n'a pas abouti. Écrivez-nous sur WhatsApp au %s ou à %s.",
                $this->site->phoneDisplay,
                $this->site->email,
            ));
        }

        $this->rateLimiter->hit($request->clientIp, $now);
        $this->rateLimiter->purgeExpired($now);

        return Response::redirect(self::SUCCESS_LOCATION);
    }

    /**
     * Les robots reçoivent la même réponse qu'un envoi réussi, pour ne rien leur apprendre.
     */
    private function dropSilently(string $event, int $now): Response
    {
        $this->securityLog->record($event, $now);

        return Response::redirect(self::SUCCESS_LOCATION);
    }

    /**
     * @param array<string, string> $errors
     */
    private function redisplay(ContactRequest $contact, int $now, int $status, string $notice, array $errors = []): Response
    {
        $form = new ContactFormState($this->formToken->issue($now), $contact->values(), $errors, $notice);

        return Response::html($this->homePage->render($form), $status);
    }
}
```

- [ ] **Étape 6 : brancher le formulaire sur l'accueil**

`src/Page/HomePage.php`, la méthode `render` et les imports deviennent :

```php
<?php

declare(strict_types=1);

namespace Technum\Page;

use Technum\Contact\ContactFormState;
use Technum\Contact\ContactRequest;
use Technum\Content\ContentRepository;
use Technum\View\View;

final class HomePage
{
    public const TITLE = 'TECHNUM, solutions numériques au Bénin';

    public const DESCRIPTION = 'TECHNUM conçoit et maintient des applications, des plateformes et des sites '
        . 'pour les entreprises et les institutions du Bénin. Découvrez nos produits en service.';

    public function __construct(
        private readonly View $view,
        private readonly ContentRepository $content,
    ) {
    }

    public function render(ContactFormState $form): string
    {
        return $this->view->renderPage('home', [
            'projects' => $this->content->projects(),
            'services' => $this->content->services(),
            'form' => $form,
            'needs' => ContactRequest::NEEDS,
        ], [
            'title' => self::TITLE,
            'description' => self::DESCRIPTION,
            'path' => '/',
        ]);
    }
}
```

`src/Controller/HomeController.php` devient :

```php
<?php

declare(strict_types=1);

namespace Technum\Controller;

use Technum\Clock\Clock;
use Technum\Contact\ContactFormState;
use Technum\Contact\FormToken;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Page\HomePage;

final class HomeController
{
    public function __construct(
        private readonly HomePage $homePage,
        private readonly FormToken $formToken,
        private readonly Clock $clock,
    ) {
    }

    public function show(Request $request): Response
    {
        $form = new ContactFormState(
            token: $this->formToken->issue($this->clock->now()),
            sent: ($request->query['envoi'] ?? '') === 'ok',
        );

        return Response::html($this->homePage->render($form));
    }
}
```

Dans `tests/Unit/Page/HomePageTest.php`, ajouter l'import `use Technum\Contact\ContactFormState;` et remplacer la ligne de rendu par :

```php
        $this->source = (new HomePage($view, $content))->render(new ContactFormState('jeton-de-test'));
```

- [ ] **Étape 7 : écrire `templates/partials/contact-form.php`**

```php
<?php
/**
 * @var Technum\Contact\ContactFormState $form
 * @var array<string, string> $needs
 */
?>
<div class="contact-form-area">
  <?php if ($form->sent) : ?>
    <p class="form-status form-status--success" role="status">Demande envoyée. Nous vous répondons à l'adresse indiquée.</p>
  <?php endif ?>
  <?php if ($form->notice !== '') : ?>
    <p class="form-status form-status--error" role="alert"><?= e($form->notice) ?></p>
  <?php endif ?>
  <form class="contact-form" method="post" action="/contact#contact" novalidate>
    <input type="hidden" name="token" value="<?= e($form->token) ?>">
    <div class="trap" aria-hidden="true">
      <label for="contact-website">Ne remplissez pas ce champ</label>
      <input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-name">Votre nom</label>
      <input class="form-control" id="contact-name" name="name" type="text" autocomplete="name" required maxlength="100" value="<?= e($form->value('name')) ?>"<?= $form->ariaAttributes('name') ?>>
      <?php if ($form->hasError('name')) : ?>
        <p class="form-error" id="<?= e($form->errorId('name')) ?>"><?= e($form->error('name')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-organization">Votre organisation <span class="form-field__hint">(facultatif)</span></label>
      <input class="form-control" id="contact-organization" name="organization" type="text" autocomplete="organization" maxlength="120" value="<?= e($form->value('organization')) ?>"<?= $form->ariaAttributes('organization') ?>>
      <?php if ($form->hasError('organization')) : ?>
        <p class="form-error" id="<?= e($form->errorId('organization')) ?>"><?= e($form->error('organization')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-email">Votre adresse e-mail</label>
      <input class="form-control" id="contact-email" name="email" type="email" inputmode="email" autocomplete="email" required maxlength="254" value="<?= e($form->value('email')) ?>"<?= $form->ariaAttributes('email') ?>>
      <?php if ($form->hasError('email')) : ?>
        <p class="form-error" id="<?= e($form->errorId('email')) ?>"><?= e($form->error('email')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-need">Votre besoin</label>
      <select class="form-control" id="contact-need" name="need" required<?= $form->ariaAttributes('need') ?>>
        <option value="">Choisissez</option>
        <?php foreach ($needs as $value => $label) : ?>
          <option value="<?= e($value) ?>"<?= $form->value('need') === $value ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach ?>
      </select>
      <?php if ($form->hasError('need')) : ?>
        <p class="form-error" id="<?= e($form->errorId('need')) ?>"><?= e($form->error('need')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-message">Votre message</label>
      <textarea class="form-control" id="contact-message" name="message" rows="6" required maxlength="3000"<?= $form->ariaAttributes('message', 'contact-message-aide') ?>><?= e($form->value('message')) ?></textarea>
      <p class="form-field__help" id="contact-message-aide">Entre 20 et 3&nbsp;000 caractères. <span class="form-count" id="contact-message-compte"></span></p>
      <?php if ($form->hasError('message')) : ?>
        <p class="form-error" id="<?= e($form->errorId('message')) ?>"><?= e($form->error('message')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-consent">
      <input id="contact-consent" name="consent" type="checkbox" value="oui" required<?= $form->value('consent') === 'oui' ? ' checked' : '' ?><?= $form->ariaAttributes('consent', 'contact-consent-lien') ?>>
      <label for="contact-consent">J'accepte que TECHNUM utilise ces informations pour répondre à ma demande.</label>
      <p class="form-consent__link" id="contact-consent-lien"><a href="/confidentialite">Lire la politique de confidentialité</a></p>
      <?php if ($form->hasError('consent')) : ?>
        <p class="form-error" id="<?= e($form->errorId('consent')) ?>"><?= e($form->error('consent')) ?></p>
      <?php endif ?>
    </div>

    <button class="button button--primary contact-form__submit" type="submit">Envoyer la demande</button>
  </form>
</div>
```

Dans `templates/home.php`, ajouter `form` et `needs` au bloc de documentation en tête :

```php
 * @var Technum\Contact\ContactFormState $form
 * @var array<string, string> $needs
```

puis remplacer le bloc `<div class="contact">` de la section contact par :

```php
      <div class="contact">
        <?= $view->render('partials/contact-form', ['form' => $form, 'needs' => $needs]) ?>
        <?= $view->render('partials/contact-direct') ?>
      </div>
```

- [ ] **Étape 8 : assembler la nouvelle version de `src/Application.php`**

```php
<?php

declare(strict_types=1);

namespace Technum;

use Technum\Clock\Clock;
use Technum\Clock\SystemClock;
use Technum\Contact\FormToken;
use Technum\Contact\LogMailer;
use Technum\Contact\MailerInterface;
use Technum\Contact\SmtpMailer;
use Technum\Content\ContentRepository;
use Technum\Controller\ContactController;
use Technum\Controller\ErrorController;
use Technum\Controller\HomeController;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Http\Router;
use Technum\Page\HomePage;
use Technum\Security\RateLimiter;
use Technum\Security\SecurityLog;
use Technum\View\View;

/**
 * Assemble les services du site et déclare ses routes.
 */
final class Application
{
    private function __construct(
        private readonly Router $router,
        private readonly bool $isProduction,
    ) {
    }

    public static function create(
        string $rootDir,
        Config $config,
        ?MailerInterface $mailer = null,
        ?Clock $clock = null,
        ?string $storageDir = null,
    ): self {
        $storageDir ??= $rootDir . '/storage';
        $clock ??= new SystemClock();
        $content = new ContentRepository($rootDir . '/content', $rootDir . '/public');
        $site = $content->site();
        $view = new View($rootDir . '/templates', $rootDir . '/public');
        $view->share(['site' => $site, 'products' => $content->products()]);

        $mailer ??= $config->mailTransport === 'log'
            ? new LogMailer($storageDir . '/logs/mail-local.log')
            : new SmtpMailer(
                $config->smtpHost,
                $config->smtpPort,
                $config->smtpUsername,
                $config->smtpPassword,
                $config->contactSenderEmail,
                $config->contactRecipientEmail,
            );

        $formToken = new FormToken($config->appSecret);
        $homePage = new HomePage($view, $content);
        $home = new HomeController($homePage, $formToken, $clock);
        $contact = new ContactController(
            $homePage,
            $formToken,
            new RateLimiter($storageDir . '/rate-limit', $config->appSecret),
            $mailer,
            new SecurityLog($storageDir . '/logs/security.log'),
            $clock,
            $site,
        );
        $errors = new ErrorController($view);

        $router = new Router($errors->notFound(...));
        $router->get('/', $home->show(...));
        $router->get('/contact', static fn (Request $request): Response => Response::redirect('/#contact', 301));
        $router->post('/contact', $contact->submit(...));

        return new self($router, $config->isProduction());
    }

    public function handle(Request $request): Response
    {
        $response = $this->router->dispatch($request);

        return $this->isProduction
            ? $response->withHeader('Strict-Transport-Security', 'max-age=31536000')
            : $response;
    }
}
```

- [ ] **Étape 9 : relancer tous les tests**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
```

Attendu : tous les tests verts, y compris `HomeRouteTest`, `HomePageTest` et les quatorze tests de `ContactSubmissionTest`.

- [ ] **Étape 10 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add src templates tests
git commit -F - <<'EOF'
feat(contact): envoie les demandes du formulaire de la page d'accueil
EOF
```

### Tâche 21 : styles et aide à la saisie du formulaire

**Fichiers :**

- Modifier : `public/assets/css/site.css` (ajout en fin de fichier), `public/assets/js/site.js` (remplacement complet)

**Interfaces :**

- Consomme : le balisage de `templates/partials/contact-form.php` (`.contact-form`, `#contact-message`, `#contact-message-compte`).
- Produit : compteur de caractères du message, bouton d'envoi désactivé pendant l'envoi puis réactivé si le visiteur revient en arrière.

- [ ] **Étape 1 : ajouter les styles du formulaire à la fin de `public/assets/css/site.css`**

```css
/* Formulaire de contact */

.contact-form-area {
  min-width: 0;
}

.contact-form {
  display: grid;
  gap: var(--space-5);
}

.form-field {
  display: grid;
  gap: var(--space-2);
}

.form-field__label {
  font-family: var(--font-display);
  font-size: 0.9375rem;
  font-weight: 600;
}

.form-field__hint {
  color: var(--charcoal-soft);
  font-family: var(--font-text);
  font-weight: 400;
}

.form-field__help {
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

.form-control {
  width: 100%;
  min-height: 3rem;
  padding: 0.75rem 0.875rem;
  border: 1px solid var(--charcoal-muted);
  border-radius: var(--radius-control);
  background: var(--white);
  color: var(--charcoal);
  font: inherit;
}

textarea.form-control {
  min-height: 10rem;
  resize: vertical;
}

.form-control:focus-visible {
  border-color: var(--blue);
  outline: 2px solid var(--blue);
  outline-offset: 1px;
}

.form-control[aria-invalid='true'] {
  border-color: var(--error);
}

.form-error {
  color: var(--error);
  font-size: var(--text-small);
  font-weight: 500;
}

.form-consent {
  display: grid;
  grid-template-columns: 1.25rem minmax(0, 1fr);
  align-items: start;
  gap: var(--space-2) var(--space-3);
}

.form-consent input {
  width: 1.25rem;
  height: 1.25rem;
  margin: 0.2rem 0 0;
  accent-color: var(--blue);
}

.form-consent__link,
.form-consent .form-error {
  grid-column: 2;
  font-size: var(--text-small);
}

.form-status {
  margin-bottom: var(--space-5);
  padding: var(--space-4) var(--space-5);
  border-radius: var(--radius-control);
}

.form-status--success {
  background: var(--ice);
  color: var(--blue-dark);
  font-weight: 500;
}

.form-status--error {
  border: 1px solid var(--error);
  background: var(--white);
  color: var(--error);
}

.contact-form__submit {
  justify-self: start;
}

.button:disabled {
  cursor: progress;
  opacity: 0.7;
}

.trap {
  position: absolute;
  left: -9999px;
  width: 1px;
  height: 1px;
  overflow: hidden;
}
```

- [ ] **Étape 2 : remplacer `public/assets/js/site.js`**

```js
/*
 * Améliorations progressives de bytechnum.com.
 * La page fonctionne sans ce script : il replie le menu sur petit écran,
 * compte les caractères du message et empêche un double envoi du formulaire.
 */
(function () {
  'use strict';

  document.documentElement.classList.remove('no-js');
  document.documentElement.classList.add('js');

  function setUpMenu() {
    const nav = document.querySelector('.site-nav');
    const toggle = nav ? nav.querySelector('.site-nav__toggle') : null;
    const list = nav ? nav.querySelector('.site-nav__list') : null;
    if (!nav || !toggle || !list) {
      return;
    }

    const setOpen = (isOpen) => {
      nav.dataset.open = String(isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
      toggle.textContent = isOpen ? 'Fermer' : 'Menu';
    };

    toggle.hidden = false;
    setOpen(false);

    toggle.addEventListener('click', () => setOpen(nav.dataset.open !== 'true'));

    list.addEventListener('click', (event) => {
      if (event.target instanceof Element && event.target.closest('a')) {
        setOpen(false);
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && nav.dataset.open === 'true') {
        setOpen(false);
        toggle.focus();
      }
    });
  }

  function setUpMessageCounter() {
    const message = document.getElementById('contact-message');
    const counter = document.getElementById('contact-message-compte');
    if (!message || !counter) {
      return;
    }

    const format = new Intl.NumberFormat('fr-FR');
    const update = () => {
      const length = message.value.length;
      counter.textContent = `${format.format(length)} ${length > 1 ? 'caractères saisis' : 'caractère saisi'}.`;
    };

    message.addEventListener('input', update);
    update();
  }

  function setUpSubmitLock() {
    const form = document.querySelector('.contact-form');
    const button = form ? form.querySelector('button[type="submit"]') : null;
    if (!form || !button) {
      return;
    }

    const label = button.textContent;

    form.addEventListener('submit', () => {
      button.disabled = true;
      button.textContent = 'Envoi en cours…';
    });

    window.addEventListener('pageshow', (event) => {
      if (event.persisted) {
        button.disabled = false;
        button.textContent = label;
      }
    });
  }

  function init() {
    setUpMenu();
    setUpMessageCounter();
    setUpSubmitLock();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
```

- [ ] **Étape 3 : lancer les outils du front**

```bash
cd /c/wamp64/www/TECHNUM
npm run format
npm run lint:js
npm run format:check
```

Attendu : aucune erreur, diff limité à la mise en forme. Puis `wc -c public/assets/js/site.js public/assets/css/site.css` : script sous 10 Ko, feuille sous 30 Ko.

- [ ] **Étape 4 : vérifier les trois états du formulaire dans un navigateur**

Lancer en arrière-plan `/c/wamp64/bin/php/php8.4.15/php.exe -S 127.0.0.1:8080 -t public tools/dev-router.php`. Le `.env` local utilise `MAIL_TRANSPORT=log`.

Avec Playwright, en 1440 × 900 puis en 390 × 844 :

1. `browser_navigate` vers `http://127.0.0.1:8080/#contact`, capture : formulaire vide, bouton WhatsApp à côté sur grand écran, en dessous sur téléphone.
2. Cliquer sur « Envoyer la demande » sans rien remplir, capture : message général en haut, une erreur sous chaque champ obligatoire, la page reste sur la section contact.
3. Remplir nom, e-mail, besoin, un message d'au moins 20 caractères et cocher l'accord, attendre 4 secondes avec `browser_wait_for`, envoyer, capture : message « Demande envoyée. Nous vous répondons à l'adresse indiquée. ».
4. Vérifier que le compteur affiche le nombre de caractères pendant la saisie.

```bash
cd /c/wamp64/www/TECHNUM
tail -n 12 storage/logs/mail-local.log
rm -f storage/logs/mail-local.log storage/logs/security.log storage/rate-limit/*.hits
```

Attendu : la demande de l'étape 3 apparaît dans le journal local, puis les fichiers locaux sont supprimés. Arrêter le serveur et supprimer `.playwright-mcp`.

- [ ] **Étape 5 : vérification complète, commit, pull request du lot**

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .playwright-mcp
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
npm run lint:js
npm run format:check
git add public/assets/css/site.css public/assets/js/site.js
git commit -F - <<'EOF'
feat(contact): met en forme le formulaire et aide à la saisie
EOF
```

Pull request du lot :

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Envoi du formulaire de contact" --body-file - <<EOF
## Objectif

Brancher le formulaire de contact sur la page d'accueil. Issue liée : #$ISSUE

## Changements

- Contrôleur de contact : piège à robots, jeton, validation, limite d'envois, envoi, confirmation
- Formulaire accessible sans JavaScript, erreurs reliées à leur champ, valeurs conservées
- Styles du formulaire, compteur du message, envoi unique par clic

## Tests

- [x] Tests d'intégration de chaque issue d'un envoi : succès, erreurs, piège, jeton falsifié, envoi trop rapide, jeton expiré, limite, échec SMTP, champs en tableau, échappement
- [x] Vérification dans un navigateur des états vide, en erreur et envoyé, sur ordinateur et téléphone

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de console.log
- [x] Pas de secret exposé
- [x] Taille : environ 750 lignes, dont 300 de tests
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

---

## Lot 7 : pages légales et référencement

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Pages légales et référencement" --body "Mentions légales, politique de confidentialité, conditions d'utilisation, balises de partage, données structurées, robots.txt, sitemap.xml et image de partage." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-legal-referencement"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

### Tâche 22 : pages légales

**Fichiers :**

- Créer : `src/Controller/LegalController.php`, `templates/legal/mentions-legales.php`, `templates/legal/confidentialite.php`, `templates/legal/cgu.php`, `tests/Integration/LegalPagesTest.php`
- Modifier : `src/Application.php`, `templates/partials/footer.php`, `public/assets/css/site.css` (ajout en fin de fichier)

**Interfaces :**

- Consomme : `View`, `Response`, `SiteInfo` (variable partagée `site`).
- Produit : `LegalController(View $view)` avec `static pages(): list<string>` (`mentions-legales`, `confidentialite`, `cgu`) et `show(string $page): Response`. Routes `GET /mentions-legales`, `GET /confidentialite`, `GET /cgu`.

- [ ] **Étape 1 : écrire le test qui échoue, `tests/Integration/LegalPagesTest.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Technum\Http\Request;
use Technum\Tests\Support\Html;

final class LegalPagesTest extends ApplicationTestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function pages(): iterable
    {
        yield 'mentions légales' => ['/mentions-legales', 'Mentions légales'];
        yield 'confidentialité' => ['/confidentialite', 'Politique de confidentialité'];
        yield 'conditions' => ['/cgu', "Conditions d'utilisation"];
    }

    #[DataProvider('pages')]
    public function testLegalPageIsServedWithItsCanonicalAddress(string $path, string $title): void
    {
        $response = $this->get($path);
        $html = Html::parse($response->body);

        self::assertSame(200, $response->status);
        self::assertSame($title, $html->text('h1'));
        self::assertSame('https://bytechnum.com' . $path, $html->attribute('link[rel="canonical"]', 'href'));
        self::assertContains('mailto:elisee.atonde@bytechnum.com', $html->attributes('.legal a', 'href'));
    }

    public function testTrailingSlashFromTheBrowserIsAccepted(): void
    {
        $request = Request::fromGlobals(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/cgu/'], [], []);

        self::assertSame(200, $this->application()->handle($request)->status);
    }

    public function testLegalNoticeNamesTheHost(): void
    {
        self::assertStringContainsString('Spaceship, Inc.', Html::parse($this->get('/mentions-legales')->body)->text('.legal'));
    }

    public function testPrivacyPolicyStatesRetentionCookiesAndAuthority(): void
    {
        $text = Html::parse($this->get('/confidentialite')->body)->text('.legal');

        self::assertStringContainsString('12 mois', $text);
        self::assertStringContainsString('aucun cookie', $text);
        self::assertStringContainsString('APDP', $text);
    }

    public function testFooterLinksToEveryLegalPage(): void
    {
        $links = Html::parse($this->get('/')->body)->attributes('.site-footer a', 'href');

        foreach (['/mentions-legales', '/confidentialite', '/cgu'] as $path) {
            self::assertContains($path, $links);
        }
    }
}
```

- [ ] **Étape 2 : lancer le test et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter LegalPagesTest
```

Attendu : échec, les pages répondent 404.

- [ ] **Étape 3 : écrire `src/Controller/LegalController.php`**

```php
<?php

declare(strict_types=1);

namespace Technum\Controller;

use InvalidArgumentException;
use Technum\Http\Response;
use Technum\View\View;

final class LegalController
{
    /** @var array<string, array{title: string, description: string}> */
    private const PAGES = [
        'mentions-legales' => [
            'title' => 'Mentions légales',
            'description' => 'Éditeur, hébergement et propriété intellectuelle du site bytechnum.com.',
        ],
        'confidentialite' => [
            'title' => 'Politique de confidentialité',
            'description' => 'Données reçues par le formulaire de contact de bytechnum.com, usage, durée de conservation et droits.',
        ],
        'cgu' => [
            'title' => "Conditions d'utilisation",
            'description' => "Règles d'utilisation du site bytechnum.com.",
        ],
    ];

    public function __construct(private readonly View $view)
    {
    }

    /**
     * @return list<string>
     */
    public static function pages(): array
    {
        return array_keys(self::PAGES);
    }

    public function show(string $page): Response
    {
        $definition = self::PAGES[$page] ?? throw new InvalidArgumentException('Page légale inconnue : ' . $page);

        return Response::html($this->view->renderPage('legal/' . $page, [], [
            'title' => $definition['title'] . ', TECHNUM',
            'description' => $definition['description'],
            'path' => '/' . $page,
        ]));
    }
}
```

- [ ] **Étape 4 : déclarer les routes dans `src/Application.php`**

Ajouter l'import `use Technum\Controller\LegalController;`, puis, juste après la ligne `$router->post('/contact', $contact->submit(...));` :

```php
        $legal = new LegalController($view);
        foreach (LegalController::pages() as $page) {
            $router->get('/' . $page, static fn (Request $request): Response => $legal->show($page));
        }
```

- [ ] **Étape 5 : écrire `templates/legal/mentions-legales.php`**

```php
<?php /** @var Technum\Content\SiteInfo $site */ ?>
<article class="legal" aria-labelledby="titre-page">
  <div class="legal__inner">
    <div class="legal__content">
      <h1 id="titre-page">Mentions légales</h1>
      <p class="legal__updated">Dernière mise à jour&nbsp;: 2 octobre 2026</p>

      <h2>Éditeur du site</h2>
      <p>Le site bytechnum.com est édité par Elisée Magloire ATONDE, qui exploite la marque TECHNUM à <?= e($site->city) ?>.</p>
      <ul>
        <li>E-mail&nbsp;: <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a></li>
        <li>Téléphone et WhatsApp&nbsp;: <a href="<?= e($site->phoneUrl()) ?>"><?= e($site->phoneDisplay) ?></a></li>
      </ul>
      <p>Directeur de la publication&nbsp;: Elisée Magloire ATONDE.</p>

      <h2>Hébergement</h2>
      <p>Le site est hébergé par Spaceship, Inc., 4600 East Washington Street, Suite 300, Phoenix, AZ 85034, États-Unis, sur un serveur situé à Amsterdam, aux Pays-Bas. Site web&nbsp;: <a href="https://www.spaceship.com">spaceship.com</a>.</p>

      <h2>Propriété intellectuelle</h2>
      <p>Les textes, le logo TECHNUM, les visuels et la mise en page de ce site appartiennent à Elisée Magloire ATONDE. Les captures d'écran montrent des produits édités par TECHNUM. Toute reproduction, même partielle, demande une autorisation écrite préalable.</p>

      <h2>Liens</h2>
      <p>Ce site renvoie vers les produits TECHNUM, hébergés sur des sous-domaines de bytechnum.com et soumis à leurs propres conditions, ainsi que vers des sites tiers comme GitHub. TECHNUM n'est pas responsable du contenu des sites tiers.</p>

      <h2>Contact</h2>
      <p>Pour toute question sur ce site, écrivez à <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a>.</p>
    </div>
  </div>
</article>
```

- [ ] **Étape 6 : écrire `templates/legal/confidentialite.php`**

```php
<?php /** @var Technum\Content\SiteInfo $site */ ?>
<article class="legal" aria-labelledby="titre-page">
  <div class="legal__inner">
    <div class="legal__content">
      <h1 id="titre-page">Politique de confidentialité</h1>
      <p class="legal__updated">Dernière mise à jour&nbsp;: 2 octobre 2026</p>

      <h2>Responsable du traitement</h2>
      <p>Elisée Magloire ATONDE, qui exploite la marque TECHNUM à <?= e($site->city) ?>. Contact&nbsp;: <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a>.</p>

      <h2>Données reçues</h2>
      <p>Le site ne reçoit de données personnelles que par son formulaire de contact&nbsp;: votre nom, votre organisation si vous l'indiquez, votre adresse e-mail, le type de besoin et votre message.</p>
      <p>Le site ne dépose aucun cookie et n'utilise aucun outil de mesure d'audience. Comme tout serveur web, celui de l'hébergeur garde un journal technique des connexions, avec l'adresse IP, la page demandée, la date et l'heure, pour la sécurité du service.</p>

      <h2>Usage de vos données</h2>
      <p>Vos données servent uniquement à répondre à votre demande et, si vous le souhaitez, à préparer une proposition. Elles ne sont ni vendues ni cédées, et ne servent à aucune prospection.</p>

      <h2>Base légale</h2>
      <p>Le traitement repose sur votre consentement, donné en cochant la case du formulaire. Vous pouvez le retirer à tout moment en nous écrivant.</p>

      <h2>Destinataires et lieu de conservation</h2>
      <p>Le formulaire transmet votre demande par e-mail à la boîte TECHNUM, hébergée par Spacemail, le service de messagerie de Spaceship, Inc. Le serveur du site se trouve à Amsterdam, aux Pays-Bas&nbsp;: vos données sont donc traitées hors du Bénin. Le site lui-même ne garde aucune copie de votre message.</p>

      <h2>Durée de conservation</h2>
      <p>Votre demande est conservée 12 mois après notre dernier échange, puis supprimée.</p>

      <h2>Protection contre les envois abusifs</h2>
      <p>Pour limiter les envois automatiques, le serveur garde pendant une heure une empreinte de votre adresse IP, calculée avec une clé secrète. L'adresse elle-même n'est jamais enregistrée par le site.</p>

      <h2>Vos droits</h2>
      <p>Conformément à la loi n°2017-20 du 20 avril 2018 portant code du numérique en République du Bénin, vous pouvez accéder à vos données, les faire rectifier ou effacer, et vous opposer à leur traitement. Écrivez à <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a>.</p>
      <p>Si vous estimez que vos droits ne sont pas respectés, vous pouvez saisir l'Autorité de protection des données à caractère personnel, l'APDP&nbsp;: <a href="https://apdp.bj">apdp.bj</a>.</p>
    </div>
  </div>
</article>
```

- [ ] **Étape 7 : écrire `templates/legal/cgu.php`**

```php
<?php /** @var Technum\Content\SiteInfo $site */ ?>
<article class="legal" aria-labelledby="titre-page">
  <div class="legal__inner">
    <div class="legal__content">
      <h1 id="titre-page">Conditions d'utilisation</h1>
      <p class="legal__updated">Dernière mise à jour&nbsp;: 2 octobre 2026</p>

      <h2>Objet</h2>
      <p>Ces conditions encadrent l'utilisation du site bytechnum.com, qui présente TECHNUM, ses produits, ses réalisations et ses services. Naviguer sur le site vaut acceptation de ces conditions.</p>

      <h2>Accès au site</h2>
      <p>Le site est accessible gratuitement et sans compte. TECHNUM peut l'interrompre pour une maintenance ou une mise à jour.</p>

      <h2>Produits présentés</h2>
      <p>Chaque produit présenté sur ce site a ses propres conditions, consultables sur son site. L'état affiché pour chaque produit décrit son avancement à la date de mise à jour indiquée sur la page d'accueil.</p>

      <h2>Formulaire de contact</h2>
      <p>Vous vous engagez à fournir des informations exactes et à ne pas utiliser le formulaire pour envoyer des messages publicitaires, frauduleux ou malveillants.</p>

      <h2>Propriété intellectuelle</h2>
      <p>Les contenus du site sont protégés. Les conditions de leur réutilisation figurent dans les <a href="/mentions-legales">mentions légales</a>.</p>

      <h2>Responsabilité</h2>
      <p>TECHNUM veille à l'exactitude des informations publiées, sans pouvoir garantir l'absence d'erreur. TECHNUM n'est pas responsable du contenu des sites tiers vers lesquels ce site renvoie.</p>

      <h2>Droit applicable</h2>
      <p>Ces conditions sont soumises au droit béninois.</p>

      <h2>Contact</h2>
      <p>Pour toute question&nbsp;: <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a>.</p>
    </div>
  </div>
</article>
```

- [ ] **Étape 8 : ajouter les liens légaux au pied de page et leurs styles**

Dans `templates/partials/footer.php`, ajouter cette colonne juste avant la fermeture `</div>` de `site-footer__inner`, après la colonne « Contact » :

```php
    <nav class="site-footer__column" aria-labelledby="pied-informations">
      <h2 class="site-footer__title" id="pied-informations">Informations</h2>
      <ul>
        <li><a href="/mentions-legales">Mentions légales</a></li>
        <li><a href="/confidentialite">Confidentialité</a></li>
        <li><a href="/cgu">Conditions d'utilisation</a></li>
      </ul>
    </nav>
```

Ajouter à la fin de `public/assets/css/site.css` :

```css
/* Pages légales */

.legal {
  padding: var(--space-8) 0 var(--space-9);
}

.legal__inner {
  max-width: var(--container);
  margin: 0 auto;
  padding: 0 var(--gutter);
}

.legal__content {
  max-width: var(--measure);
}

.legal h1 {
  font-size: var(--text-h2);
}

.legal__updated {
  margin-top: var(--space-3);
  color: var(--charcoal-soft);
  font-size: var(--text-small);
}

.legal h2 {
  margin-top: var(--space-7);
  font-size: var(--text-h3);
}

.legal p,
.legal ul {
  margin-top: var(--space-3);
}

.legal ul {
  padding-left: 1.25rem;
}

.legal li + li {
  margin-top: var(--space-2);
}
```

- [ ] **Étape 9 : relancer les tests, puis committer**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
npm run format
npm run format:check
git add src templates public/assets/css/site.css tests/Integration/LegalPagesTest.php
git commit -F - <<'EOF'
feat(legal): ajoute les mentions légales, la confidentialité et les cgu
EOF
```

Attendu : tous les tests verts, y compris les sept tests de `LegalPagesTest`.

### Tâche 23 : référencement et image de partage

**Fichiers :**

- Créer : `src/Page/StructuredData.php`, `public/robots.txt`, `public/sitemap.xml`, `public/assets/img/og-image.png`, `tests/Unit/Page/StructuredDataTest.php`, `tests/Integration/SeoTest.php`
- Modifier : `templates/layout.php` (remplacement complet), `src/Page/HomePage.php`

**Interfaces :**

- Consomme : `SiteInfo`.
- Produit : `StructuredData::organization(SiteInfo $site): string`, un JSON encodé avec `JSON_HEX_TAG`, donc sûr dans une balise `script`. `meta` accepte en option `jsonLd`, inséré tel quel par le gabarit commun.

- [ ] **Étape 1 : écrire les tests qui échouent**

`tests/Unit/Page/StructuredDataTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Page;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Technum\Content\SiteInfo;
use Technum\Page\StructuredData;

final class StructuredDataTest extends TestCase
{
    public function testOrganizationDescribesTechnum(): void
    {
        $site = new SiteInfo(
            email: 'elisee.atonde@bytechnum.com',
            phoneDisplay: '+229 01 50 61 73 00',
            phoneE164: '+2290150617300',
            whatsappNumber: '2290150617300',
            whatsappMessage: 'Bonjour.',
            githubUrl: 'https://github.com/Magloire04',
            city: 'Porto-Novo, Bénin',
            updatedAt: new DateTimeImmutable('2026-10-02'),
        );

        $json = StructuredData::organization($site);
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertSame('Organization', $data['@type']);
        self::assertSame('TECHNUM', $data['name']);
        self::assertSame('+2290150617300', $data['telephone']);
        self::assertSame('https://bytechnum.com/assets/img/logo-technum-carre-512.png', $data['logo']);
        self::assertStringNotContainsString('<', $json);
    }
}
```

`tests/Integration/SeoTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use Technum\Page\HomePage;
use Technum\Tests\Support\Html;

final class SeoTest extends ApplicationTestCase
{
    public function testHomePageDescribesItselfForSearchAndSharing(): void
    {
        $html = Html::parse($this->get('/')->body);

        self::assertSame(HomePage::TITLE, $html->text('title'));
        self::assertSame(HomePage::DESCRIPTION, $html->attribute('meta[name="description"]', 'content'));
        self::assertSame('https://bytechnum.com/', $html->attribute('meta[property="og:url"]', 'content'));
        self::assertSame('summary_large_image', $html->attribute('meta[name="twitter:card"]', 'content'));

        $image = $html->attribute('meta[property="og:image"]', 'content');
        self::assertStringStartsWith('https://bytechnum.com/assets/', $image);
        self::assertFileExists(self::ROOT . '/public/' . substr($image, strlen('https://bytechnum.com/')));
    }

    public function testHomePageCarriesOrganizationStructuredData(): void
    {
        $json = Html::parse($this->get('/')->body)->text('script[type="application/ld+json"]');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertSame('Organization', $data['@type']);
    }

    public function testOpenGraphImageHasTheExpectedSize(): void
    {
        $size = getimagesize(self::ROOT . '/public/assets/img/og-image.png');
        if ($size === false) {
            self::fail("L'image de partage est illisible.");
        }

        self::assertSame([1200, 630], [$size[0], $size[1]]);
    }

    public function testRobotsAndSitemapListThePublicPages(): void
    {
        self::assertStringContainsString(
            'Sitemap: https://bytechnum.com/sitemap.xml',
            (string) file_get_contents(self::ROOT . '/public/robots.txt'),
        );

        $sitemap = simplexml_load_file(self::ROOT . '/public/sitemap.xml');
        if ($sitemap === false) {
            self::fail('sitemap.xml est illisible.');
        }
        $locations = [];
        foreach ($sitemap->url as $url) {
            $locations[] = (string) $url->loc;
        }

        self::assertSame([
            'https://bytechnum.com/',
            'https://bytechnum.com/mentions-legales',
            'https://bytechnum.com/confidentialite',
            'https://bytechnum.com/cgu',
        ], $locations);
    }
}
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'StructuredDataTest|SeoTest'
```

Attendu : échec, classe `StructuredData` introuvable et fichiers absents.

- [ ] **Étape 3 : écrire `src/Page/StructuredData.php` et l'utiliser dans `HomePage`**

```php
<?php

declare(strict_types=1);

namespace Technum\Page;

use Technum\Content\SiteInfo;

final class StructuredData
{
    /**
     * Données structurées schema.org de TECHNUM, sûres dans une balise script grâce à JSON_HEX_TAG.
     */
    public static function organization(SiteInfo $site): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'TECHNUM',
            'slogan' => 'La technologie à votre portée',
            'url' => 'https://bytechnum.com/',
            'logo' => 'https://bytechnum.com/assets/img/logo-technum-carre-512.png',
            'email' => $site->email,
            'telephone' => $site->phoneE164,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Porto-Novo',
                'addressCountry' => 'BJ',
            ],
            'founder' => [
                '@type' => 'Person',
                'name' => 'Elisée Magloire ATONDE',
                'url' => 'https://moi.bytechnum.com',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
    }
}
```

Dans `src/Page/HomePage.php`, le tableau `meta` de `render()` devient :

```php
        ], [
            'title' => self::TITLE,
            'description' => self::DESCRIPTION,
            'path' => '/',
            'jsonLd' => StructuredData::organization($this->content->site()),
        ]);
```

- [ ] **Étape 4 : remplacer `templates/layout.php`**

```php
<?php
/**
 * @var Technum\View\View $view
 * @var string $content
 * @var array{title: string, description: string, path: string, robots?: string, jsonLd?: string} $meta
 */
$canonical = 'https://bytechnum.com' . $meta['path'];
?>
<!doctype html>
<html lang="fr" class="no-js">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($meta['title']) ?></title>
  <meta name="description" content="<?= e($meta['description']) ?>">
<?php if (isset($meta['robots'])) : ?>
  <meta name="robots" content="<?= e($meta['robots']) ?>">
<?php else : ?>
  <link rel="canonical" href="<?= e($canonical) ?>">
<?php endif ?>
  <meta property="og:type" content="website">
  <meta property="og:locale" content="fr_FR">
  <meta property="og:site_name" content="TECHNUM">
  <meta property="og:title" content="<?= e($meta['title']) ?>">
  <meta property="og:description" content="<?= e($meta['description']) ?>">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:image" content="https://bytechnum.com/assets/img/og-image.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="Logo TECHNUM et la phrase : Des solutions numériques conçues pour vos réalités.">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="icon" href="/favicon.ico" sizes="48x48">
  <link rel="icon" href="<?= e($view->asset('img/favicon.svg')) ?>" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="preload" href="<?= e($view->asset('fonts/montserrat-700.woff2')) ?>" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= e($view->asset('css/site.css')) ?>">
  <script src="<?= e($view->asset('js/site.js')) ?>"></script>
<?php if (isset($meta['jsonLd'])) : ?>
  <script type="application/ld+json"><?= $meta['jsonLd'] ?></script>
<?php endif ?>
</head>
<body>
  <a class="skip-link" href="#contenu">Aller au contenu</a>
  <?= $view->render('partials/header') ?>
  <main id="contenu" tabindex="-1">
<?= $content ?>
  </main>
  <?= $view->render('partials/footer') ?>
</body>
</html>
```

- [ ] **Étape 5 : écrire `public/robots.txt` et `public/sitemap.xml`**

`public/robots.txt` :

```text
User-agent: *
Allow: /

Sitemap: https://bytechnum.com/sitemap.xml
```

`public/sitemap.xml` :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://bytechnum.com/</loc>
    <lastmod>2026-10-02</lastmod>
  </url>
  <url>
    <loc>https://bytechnum.com/mentions-legales</loc>
    <lastmod>2026-10-02</lastmod>
  </url>
  <url>
    <loc>https://bytechnum.com/confidentialite</loc>
    <lastmod>2026-10-02</lastmod>
  </url>
  <url>
    <loc>https://bytechnum.com/cgu</loc>
    <lastmod>2026-10-02</lastmod>
  </url>
</urlset>
```

- [ ] **Étape 6 : produire l'image de partage**

Créer une page temporaire, jamais commitée, qui compose l'image avec les vraies polices du site :

```bash
cd /c/wamp64/www/TECHNUM
cat > public/og-source.html <<'EOF'
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="/assets/css/site.css">
<style>
  body { margin: 0; }
  .og { box-sizing: border-box; width: 1200px; height: 630px; padding: 72px 80px 0; display: flex; flex-direction: column; background: #ffffff; }
  .og img { width: 380px; }
  .og p { margin: auto 0 56px; max-width: 920px; font: 700 64px/1.1 'Montserrat', sans-serif; letter-spacing: -0.01em; color: #373536; }
  .og__band { display: flex; height: 18px; margin: 0 -80px; }
  .og__band span { flex: 1; }
  .og__band .c { background: #373536; }
  .og__band .b { background: #405fe0; }
  .og__band .d { background: #2846b9; }
</style>
</head>
<body>
<div class="og">
  <img src="/assets/img/logo-technum-signature.svg" alt="">
  <p>Des solutions numériques conçues pour vos réalités.</p>
  <div class="og__band"><span class="c"></span><span class="b"></span><span class="d"></span></div>
</div>
</body>
</html>
EOF
```

Lancer en arrière-plan `/c/wamp64/bin/php/php8.4.15/php.exe -S 127.0.0.1:8080 -t public tools/dev-router.php`. Avec Playwright : `browser_resize` 1200 × 630, `browser_navigate` vers `http://127.0.0.1:8080/og-source.html`, `browser_wait_for` 1 seconde, `browser_take_screenshot` avec `scale` à `css` vers `c:\wamp64\www\TECHNUM\public\assets\img\og-image.png`. Lire l'image : logo aux couleurs officielles en haut, phrase en Montserrat, bande Charcoal, bleu et bleu foncé en bas. Arrêter le serveur, puis :

```bash
cd /c/wamp64/www/TECHNUM
rm public/og-source.html
rm -rf .playwright-mcp
git status --short public
```

Attendu : `og-source.html` n'apparaît plus, `og-image.png` apparaît comme nouveau fichier.

- [ ] **Étape 7 : vérification complète, commit, pull request du lot**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
npm run lint:js
npm run format:check
git add src/Page templates/layout.php public/robots.txt public/sitemap.xml public/assets/img/og-image.png tests/Unit/Page/StructuredDataTest.php tests/Integration/SeoTest.php
git commit -F - <<'EOF'
feat(seo): ajoute les balises de partage, les données structurées et le sitemap
EOF
```

Pull request du lot :

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Pages légales et référencement" --body-file - <<EOF
## Objectif

Compléter le site avec ses pages légales et son référencement. Issue liée : #$ISSUE

## Changements

- Mentions légales, politique de confidentialité, conditions d'utilisation, liens dans le pied de page
- Balises Open Graph et Twitter, données structurées Organization
- robots.txt, sitemap.xml, image de partage aux couleurs de la marque

## Tests

- [x] Pages légales : statut, titre, adresse canonique, contacts, hébergeur, durée de conservation, APDP
- [x] Référencement : titre, description, image de partage existante en 1200 × 630, JSON-LD valide, sitemap complet

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de secret exposé
- [x] Taille : environ 550 lignes, dont 200 de texte légal
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

---

## Lot 8 : préparation de la mise en ligne

Ouverture du lot :

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
ISSUE=$(gh issue create --title "Préparation de la mise en ligne" --body "Script de déploiement, documentation et version 1 sur main." | sed 's#.*/##')
git checkout -b "feature/TECHNUM-$ISSUE-preparation-mise-en-ligne"
echo "$ISSUE" | tee .git/TECHNUM_ISSUE
```

### Tâche 24 : script de déploiement, documentation et version 1

**Fichiers :**

- Créer : `deploy.sh`
- Modifier : `README.md` (remplacement complet)

**Interfaces :** aucune nouvelle.

- [ ] **Étape 1 : écrire `deploy.sh`**

```bash
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
```

```bash
cd /c/wamp64/www/TECHNUM
bash -n deploy.sh && echo "syntaxe correcte"
git update-index --chmod=+x deploy.sh 2>/dev/null || true
```

- [ ] **Étape 2 : remplacer `README.md`**

````markdown
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

- Conception : [docs/superpowers/specs/2026-10-02-page-accueil-bytechnum-design.md](docs/superpowers/specs/2026-10-02-page-accueil-bytechnum-design.md)
- Plan de réalisation : [docs/superpowers/plans/2026-10-02-page-accueil-bytechnum.md](docs/superpowers/plans/2026-10-02-page-accueil-bytechnum.md)
- Règles de contribution : [CONTRIBUTING.md](CONTRIBUTING.md)
````

- [ ] **Étape 3 : committer et ouvrir la pull request du lot**

```bash
cd /c/wamp64/www/TECHNUM
git add deploy.sh README.md
git commit -F - <<'EOF'
docs: ajoute le script de déploiement et documente le projet
EOF
```

Pull request du lot :

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=$(cat .git/TECHNUM_ISSUE)
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Préparation de la mise en ligne" --body-file - <<EOF
## Objectif

Préparer la première mise en ligne de bytechnum.com. Issue liée : #$ISSUE

## Changements

- Script de déploiement depuis main
- README : démarrage local, vérifications, modification du contenu, mise en ligne

## Tests

- [x] Syntaxe du script contrôlée avec bash -n
- [x] Intégration continue verte

## Checklist auteur

- [x] Relu par moi-même
- [x] Pas de secret exposé
EOF
gh pr checks --watch
gh pr merge --merge --delete-branch
git checkout develop && git pull --ff-only
```

- [ ] **Étape 4 : revue finale de toute la branche develop**

Invoquer la compétence `superpowers:requesting-code-review` sur l'écart entre `main` et `develop`. Le relecteur reçoit la spécification, ce plan et la liste des points de vigilance. Corriger chaque constat bloquant dans une branche `bugfix/TECHNUM-{issue}-{description}` avec sa propre pull request, avant de continuer.

- [ ] **Étape 5 : préparer la version 1 sur main**

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
RELEASE="release/$(date +%Y-%m-%d)"
git checkout -b "$RELEASE"
git push -u origin HEAD
ISSUE=$(gh issue create --title "Version 1 de bytechnum.com" --body "Passage de develop sur main pour la première mise en ligne." | sed 's#.*/##')
gh pr create --base main --title "[#$ISSUE] Version 1 de bytechnum.com" --body-file - <<EOF
## Objectif

Publier la version 1 du site sur main. Issue liée : #$ISSUE

## Changements

- Toutes les pull requests fusionnées dans develop depuis la conception

## Tests

- [x] Intégration continue verte sur develop
- [x] Revue finale de la branche effectuée, constats bloquants corrigés

## Checklist auteur

- [x] Relu par moi-même
- [x] Pas de secret exposé
EOF
gh pr checks --watch
gh pr merge --merge
git checkout main && git pull --ff-only
git tag -a v1.0.0 -F - <<'EOF'
Version 1 de bytechnum.com
EOF
git push origin v1.0.0
git checkout develop
```

La branche de version ne contient aucun commit propre : `develop` est déjà à jour. Si un correctif y a été ajouté, ouvrir aussi une pull request de cette branche vers `develop`.

---

## Lot 9 : mise en ligne, sur accord explicite d'Elisée

Chaque tâche de ce lot écrit sur le serveur de production. Avant chacune, demander l'accord explicite d'Elisée et attendre sa réponse. Ne jamais lire le fichier `.env` du serveur.

Raccourci utilisé dans les commandes. La commande SSH du serveur, fournie par Elisée, reste hors du dépôt public : l'écrire une fois dans `/c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande`, sous la forme `ssh -o BatchMode=yes -i <clé> -p <port> <compte>@<serveur>`.

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
```

### Tâche 25 : préparer le serveur

**Fichiers :** aucun dans le dépôt. Sur le serveur : `~/.ssh/technum_deploy`, un bloc `Host github.com-technum` dans `~/.ssh/config`, le dossier `~/apps/technum`.

- [ ] **Étape 1 : obtenir l'accord explicite d'Elisée pour préparer le serveur**

- [ ] **Étape 2 : créer la clé de déploiement et l'ajouter au dépôt, en lecture seule**

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
TOOLS=/c/Users/jenmf/AppData/Local/Temp/technum-outils
$SSH 'test -f ~/.ssh/technum_deploy || ssh-keygen -t ed25519 -N "" -C "technum-deploy" -f ~/.ssh/technum_deploy >/dev/null; cat ~/.ssh/technum_deploy.pub' > "$TOOLS/technum_deploy.pub"
gh repo deploy-key add "$TOOLS/technum_deploy.pub" --repo Magloire04/technum --title "Serveur Spaceship bytechnum.com"
```

- [ ] **Étape 3 : ajouter l'alias SSH sans toucher aux entrées existantes**

Le serveur a déjà une entrée `Host github.com` pour un autre site. L'alias dédié évite d'utiliser la mauvaise clé.

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
$SSH 'grep -q "^Host github.com-technum$" ~/.ssh/config 2>/dev/null || printf "\nHost github.com-technum\n    HostName github.com\n    User git\n    IdentityFile ~/.ssh/technum_deploy\n    IdentitiesOnly yes\n" >> ~/.ssh/config; chmod 600 ~/.ssh/config; grep -n "^Host " ~/.ssh/config'
```

Attendu : la liste des hôtes contient `github.com-technum` en plus des entrées déjà présentes.

- [ ] **Étape 4 : cloner main et installer les dépendances**

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
$SSH 'cd ~/apps && GIT_SSH_COMMAND="ssh -o StrictHostKeyChecking=accept-new" git clone -b main git@github.com-technum:Magloire04/technum.git technum && cd technum && composer install --no-dev --optimize-autoloader --no-interaction --no-progress && mkdir -p storage/logs storage/rate-limit && chmod 750 storage storage/logs storage/rate-limit && git log -1 --oneline'
```

Attendu : le dernier commit de `main` s'affiche.

- [ ] **Étape 5 : Elisée crée le fichier `.env` du serveur**

Elisée saisit lui-même les secrets, qui ne transitent jamais par l'assistant :

Se connecter au serveur avec la commande SSH habituelle, puis :

```bash
cd ~/apps/technum
cp .env.example .env
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
nano .env
chmod 600 .env
```

Valeurs attendues dans `.env` : `APP_ENV=production`, `APP_SECRET` égal à la clé affichée, `MAIL_TRANSPORT=smtp`, `SMTP_HOST` et `SMTP_PORT=465` selon les réglages Spacemail, `SMTP_USERNAME=elisee.atonde@bytechnum.com`, `SMTP_PASSWORD` égal au mot de passe de cette boîte, `CONTACT_SENDER_EMAIL` et `CONTACT_RECIPIENT_EMAIL` à `elisee.atonde@bytechnum.com`, `CLIENT_IP_HEADER` vide pour l'instant.

- [ ] **Étape 6 : vérifier la configuration sans afficher de secret**

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
$SSH 'cd ~/apps/technum && php -r "require \"vendor/autoload.php\"; Technum\Config::fromArray(Dotenv\Dotenv::createArrayBacked(\".\")->load()); echo \"configuration valide\", PHP_EOL;"'
```

Attendu : `configuration valide`. Sinon, le message d'erreur nomme les variables à corriger, sans jamais afficher leur valeur. Si cette commande est refusée, demander à Elisée de la lancer lui-même.

### Tâche 26 : basculer bytechnum.com et vérifier

- [ ] **Étape 1 : obtenir l'accord explicite d'Elisée pour la bascule**

- [ ] **Étape 2 : archiver la page « Bientôt en ligne » et basculer la racine web**

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
$SSH 'set -e; ts=$(date +%Y%m%d-%H%M%S); tar -czf ~/backups/bytechnum-bientot-$ts.tar.gz -C ~ bytechnum.com; mv ~/bytechnum.com ~/bytechnum.com.bientot; ln -s ~/apps/technum/public ~/bytechnum.com; ls -la ~ | grep bytechnum'
```

Attendu : `bytechnum.com -> /home/<compte>/apps/technum/public` et le dossier `bytechnum.com.bientot`.

Retour arrière, en cas de problème non résolu :

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
$SSH 'rm ~/bytechnum.com && mv ~/bytechnum.com.bientot ~/bytechnum.com'
```

- [ ] **Étape 3 : contrôler les réponses du serveur**

```bash
UA="Mozilla/5.0"
for u in https://bytechnum.com/ https://bytechnum.com/cgu https://bytechnum.com/page-inconnue https://bytechnum.com/assets/css/site.css https://bytechnum.com/robots.txt https://bytechnum.com/sitemap.xml https://bytechnum.com/apple-touch-icon.png; do
  curl -s -o /dev/null -A "$UA" -w "$u %{http_code}\n" "$u"
done
curl -s -o /dev/null -A "$UA" -w "www %{http_code} %{redirect_url}\n" https://www.bytechnum.com/
curl -s -o /dev/null -A "$UA" -w "http %{http_code} %{redirect_url}\n" http://bytechnum.com/
curl -s -I -A "$UA" https://bytechnum.com/ | grep -i -E '^(content-security-policy|strict-transport-security|x-content-type-options|cache-control)'
curl -s -I -A "$UA" https://bytechnum.com/assets/css/site.css | grep -i -E '^(cache-control|content-type)'
```

Attendu : 200, 200, 404, 200, 200, 200, 200. www et http répondent 301 vers `https://bytechnum.com/`. Les quatre en-têtes de sécurité sont présents sur l'accueil, la feuille de style est servie avec un cache d'un an. Si tout répond 404 juste après la bascule, attendre quelques minutes : le serveur resynchronise une racine recréée. Si le problème persiste, revenir en arrière.

- [ ] **Étape 4 : vérifier l'adresse IP vue par le site**

```bash
SSH="$(cat /c/Users/jenmf/AppData/Local/Temp/technum-outils/ssh-commande)"
$SSH 'cat > ~/apps/technum/public/ip-check-temporaire.php' <<'EOF'
<?php
header('Content-Type: text/plain');
echo $_SERVER['REMOTE_ADDR'] ?? '', "\n", $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '', "\n";
EOF
curl -s https://bytechnum.com/ip-check-temporaire.php
echo "adresse publique de ce poste : $(curl -s https://api.ipify.org)"
$SSH 'rm -f ~/apps/technum/public/ip-check-temporaire.php'
curl -s -o /dev/null -w "fichier temporaire : %{http_code}\n" https://bytechnum.com/ip-check-temporaire.php
```

Attendu : la dernière ligne affiche 404. Si la première ligne renvoyée par le serveur est l'adresse publique du poste, rien à faire. Sinon, et si la deuxième ligne se termine par cette adresse, Elisée ajoute `CLIENT_IP_HEADER=HTTP_X_FORWARDED_FOR` dans le `.env` du serveur.

- [ ] **Étape 5 : vérifier le site en production dans un navigateur**

Avec Playwright, en 1440 × 900 puis en 390 × 844 : `browser_navigate` vers `https://bytechnum.com/`, capture pleine page, `browser_console_messages` sans erreur. Contrôler le logo, les polices, les captures, le registre animé, le menu sur téléphone et le pied de page.

- [ ] **Étape 6 : envoyer une vraie demande de test**

Remplir le formulaire en production avec le nom « Test de mise en ligne », une adresse e-mail d'Elisée et un message de test, puis envoyer. Attendu : message « Demande envoyée. Nous vous répondons à l'adresse indiquée. ». Elisée confirme la réception dans sa boîte, avec la bonne adresse de réponse. Si le site affiche « L'envoi n'a pas abouti », Elisée vérifie `SMTP_HOST`, `SMTP_PORT` et le mot de passe dans le `.env`.

- [ ] **Étape 7 : nettoyer**

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .playwright-mcp
```

Le dossier `~/bytechnum.com.bientot` reste en place quelques jours pour un retour arrière rapide. Le supprimer ensuite, avec l'accord d'Elisée : l'archive reste dans `~/backups`.

### Tâche 27 : après la mise en ligne

- [ ] **Étape 1 : lancer l'audit d'après mise en ligne**

Invoquer la compétence `post-deploiement-site` : santé du site, indexation, référencement, vitesse et sécurité, puis accompagnement pour Google Search Console, Bing Webmaster Tools et la fiche Google Business de Porto-Novo.

- [ ] **Étape 2 : proposer les suites hors périmètre de la v1**

Avec l'accord d'Elisée, ouvrir une issue dans chaque dépôt concerné pour ajouter le lien « Un produit TECHNUM » vers `https://bytechnum.com` : `Magloire04/oeil-360-finance`, `Magloire04/Dis_oui`, `Magloire04/provia`, `Magloire04/uac_map`. Ouvrir aussi une issue dans `Magloire04/moi.portfolio` pour un lien vers bytechnum.com. Chaque changement suit ensuite le flux de son propre dépôt.

---

## Couverture de la spécification

| Section de la spécification | Tâches |
| --- | --- |
| 1, 2, 3 : objectif, décisions, publics | 10 (contenu), 11 (accueil), 20 (contact) |
| 4 : structure de la page | 11, 20, 22 |
| 5.1 à 5.6 : contenu des sections | 10, 11 |
| 5.7 : formulaire et messages | 16, 20, 21 |
| 5.8 : pied de page et pages légales | 11, 22 |
| 6.1 à 6.10 : direction visuelle | 7, 8, 13, 14, 15, 21 |
| 7.1 à 7.3 : principe, arborescence, routes | 2 à 6, 12, 20, 22 |
| 7.4 : contenu | 9, 10 |
| 7.5 : formulaire de contact | 16 à 20 |
| 7.6 : sécurité | 3, 4, 12, 16, 18, 20 |
| 7.7 : performance | 8, 12, 13, 15 |
| 7.8 : référencement et mesure | 23, 27 |
| 8 : tests et qualité | toutes, 6 et 14 pour l'intégration continue |
| 9 : déploiement | 24, 25, 26 |
| 10 : hors périmètre | 27, étape 2 |
| 11 : décisions du 2 octobre 2026 | 10, 22, 25 |
| 12 : conventions du dépôt | 1 et chaque ouverture de lot
