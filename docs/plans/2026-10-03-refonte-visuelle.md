# Refonte visuelle de bytechnum.com : plan de réalisation

**Objectif :** donner au site la direction « Couleur de marque » validée le 3 octobre 2026 : accueil bleu, vignettes des produits, produits à onglets, sections en couleurs alternées, méthode en bandeau bleu et contact repensé, sans changer le contenu.

**Architecture :** les gabarits PHP de l'accueil changent de structure, la feuille de style remplace ses blocs section par section, et le script du site gagne les onglets des produits. Chaque tâche livre une partie testée : contenu, accueil, sections, produits, script, réalisations et services, méthode et contact, mouvement, puis publication.

**Pile technique :** PHP 8.4 sans framework, gabarits PHP, une feuille CSS et un script écrits à la main, PHPUnit 12, PHPStan niveau 8, PHP CS Fixer, ESLint, Prettier.

**Spécification :** `docs/specs/2026-10-03-refonte-visuelle-design.md`

## Contraintes globales

- Charte : `--charcoal #373536`, `--blue #405fe0`, `--blue-dark #2846b9`, `--ice #e9edff`, `--off-white #f7f8fc`, `--line #e5e7ed`. Polices Montserrat 600 et 700, Poppins 400 et 500, déjà servies par le site.
- Tout texte posé sur `#405fe0` est blanc. Tout texte respecte un contraste d'au moins 4,5.
- Deux teintes de texte pour les fonds sombres, ajoutées comme variables : `--on-blue: #dde4ff` sur le bleu foncé, `--on-charcoal: #dcdadb` sur le charbon.
- Aucune ressource externe, aucun script ni style en ligne, aucun attribut `style` : le test `testPageRespectsTheContentSecurityPolicy` le vérifie.
- Le contenu ne change pas, sauf le champ `summary` des produits.
- Mouvement : au chargement et en réponse à un geste seulement, jamais au défilement, et rien quand l'appareil demande moins de mouvement.
- Feuille de style sous 40 Ko, script sous 10 Ko.
- Textes : voix « nous », aucun tiret long ni demi-cadratin.
- Commandes PHP locales : `PHP84=/c/wamp64/bin/php/php8.4.15/php.exe`, à redéfinir dans chaque bloc.
- Git : branche `feature/TECHNUM-39-refonte-visuelle`, Conventional Commits en minuscules de 72 caractères au plus, messages passés par `git commit -F -` avec un heredoc, aucun trailer de co-auteur.
- Mise en ligne : seulement sur accord explicite d'Elisée, au moment de le faire.

## Points de vigilance

1. **Lien partagé vers un produit.** L'adresse `https://bytechnum.com/#produit-carte-uac`, ouverte depuis un message ou un autre site, doit afficher l'onglet Carte UAC et la section des produits. Contrôle : tâche 5, étape 5.
2. **Visiteur sans JavaScript.** Les quatre produits restent visibles les uns sous les autres, et aucune liste d'onglets vide n'apparaît. Tests : tâche 4, liste masquée par défaut et panneaux visibles ; tâche 5, navigateur sans script.
3. **Navigation au clavier.** Les onglets se parcourent avec les flèches, Début et Fin, et le focus reste visible sur les fonds bleus et charbon. Contrôles : tâche 5, étape 5, et règles de focus des tâches 2, 3 et 7.
4. **Mouvement réduit.** Aucune animation ni décalage au survol quand l'appareil demande moins de mouvement. Test : tâche 8, toutes les animations sont dans le bloc réservé ; contrôle navigateur en mouvement réduit.
5. **Téléphone étroit, 320 px.** Aucun défilement horizontal, vignettes lisibles. Contrôle : tâche 8, étape 6.

## Conventions d'exécution

- Dossier du dépôt : `c:/wamp64/www/TECHNUM`. Chaque bloc de commandes commence par `cd /c/wamp64/www/TECHNUM`.
- Vérification complète :

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/php-cs-fixer fix --dry-run --diff
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
"$PHP84" vendor/bin/phpunit
npm run lint:js
npm run format:check
```

- Serveur local : `"$PHP84" -S 127.0.0.1:8080 -t public tools/dev-router.php`, lancé en arrière-plan, arrêté à la fin de la tâche.
- Captures du navigateur dans `.captures/`, dossier exclu de Git par `.git/info/exclude`, supprimé à la fin de chaque tâche qui l'utilise.
- Les contrôles dans le navigateur passent par Playwright, qui pilote Chromium.
- Ordre des blocs de la feuille de style : base, boutons, en-tête, accueil, registre, pistes, vignettes, sections, produits, réalisations, services, méthode, contact, pages de message, pied de page, mouvement, paliers d'écran, formulaire, pages légales. Chaque nouveau bloc porte ses propres paliers d'écran.

## Carte des fichiers

| Fichier | Rôle | Tâches |
| --- | --- | --- |
| `src/Content/Product.php`, `src/Content/ContentRepository.php`, `content/products.php` | Champ `summary` des produits | 1 |
| `templates/home.php` | Structure de l'accueil | 2, 3, 4, 6, 7 |
| `templates/partials/product-strip.php` | Vignettes des produits | 2 |
| `templates/partials/product-tabs.php` | Liste d'onglets, masquée sans script | 4 |
| `templates/partials/contact-direct.php` | Carte bleue de contact direct | 7 |
| `public/assets/css/site.css` | Styles | 2 à 8 |
| `public/assets/js/site.js` | Onglets des produits | 5 |
| `tests/Unit/Content/*`, `tests/Unit/Page/HomePageTest.php`, `tests/Unit/StylesheetTest.php` | Tests | 1 à 8 |

---

### Tâche 1 : résumé court des produits

**Fichiers :**

- Modifier : `src/Content/Product.php`, `src/Content/ContentRepository.php`, `content/products.php`
- Tests : `tests/Unit/Content/ContentModelTest.php`, `tests/Unit/Content/ContentRepositoryTest.php`, `tests/Unit/Content/ContentFilesTest.php`

**Interfaces :**

- Consomme : `Product`, `ContentRepository::prose()`, `ContentException`.
- Produit : propriété `Product::$summary` (`string`, placée juste après `tagline`), obligatoire, 70 caractères au plus.

- [ ] **Étape 1 : écrire les tests qui échouent**

Dans `tests/Unit/Content/ContentModelTest.php`, ajouter l'argument `summary` aux deux appels `new Product(`, juste après `tagline` :

```php
            tagline: 'Suivre ses comptes.',
            summary: 'Revenus et dépenses au même endroit.',
```

```php
            tagline: 'Des stages.',
            summary: 'Stages : projet en cours.',
```

Dans `tests/Unit/Content/ContentRepositoryTest.php`, dans le tableau de `product()`, ajouter après la ligne `'tagline' => 'Une accroche : simple.',` :

```php
            'summary' => 'Un résumé : court.',
```

Dans `testBuildsProductsWithFrenchTypography()`, ajouter après l'assertion sur `tagline` :

```php
        self::assertSame("Un résumé\u{00A0}: court.", $product->summary);
```

Dans `invalidProducts()`, ajouter après le cas `'nom vide'` :

```php
        yield 'résumé vide' => [['summary' => ' '], '« summary » est vide'];
        yield 'résumé trop long' => [['summary' => str_repeat('a', 71)], '« summary » compte 70 caractères au plus'];
```

Dans `tests/Unit/Content/ContentFilesTest.php`, ajouter après `testStableProductsHaveNoWorkInProgress()` :

```php
    public function testEveryProductHasAShortSummary(): void
    {
        self::assertSame(
            [
                'Revenus, dépenses et comptes en franc CFA, au même endroit.',
                'Une demande de rendez-vous transformée en petit jeu.',
                "Stages\u{00A0}: étudiants et entreprises. Projet en cours.",
                "Le campus d'Abomey-Calavi, à pied, même sans réseau.",
            ],
            array_map(static fn (Product $product): string => $product->summary, $this->content->products()),
        );
    }
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter 'ContentModelTest|ContentRepositoryTest|ContentFilesTest'
```

Attendu : échec, avec « Unknown named parameter $summary » et des propriétés `summary` introuvables.

- [ ] **Étape 3 : ajouter la propriété au modèle**

Dans `src/Content/Product.php`, ajouter dans le constructeur, juste après `public readonly string $tagline,` :

```php
        public readonly string $summary,
```

- [ ] **Étape 4 : lire et valider le résumé**

Dans `src/Content/ContentRepository.php`, dans `buildProduct()`, ajouter juste après la ligne `tagline: $this->prose($item, 'tagline', $context),` :

```php
            summary: $this->summary($item, $context),
```

Puis ajouter cette méthode juste avant `private function slug(`. Garder le docblock `@param` de cette méthode :

```php
    /**
     * Résumé d'une vignette de l'accueil : obligatoire, 70 caractères au plus pour tenir sur deux lignes.
     *
     * @param array<array-key, mixed> $item
     */
    private function summary(array $item, string $context): string
    {
        $summary = $this->prose($item, 'summary', $context);
        if (mb_strlen($summary) > 70) {
            throw new ContentException($context . ' : « summary » compte 70 caractères au plus');
        }

        return $summary;
    }

```

- [ ] **Étape 5 : écrire les résumés**

Dans `content/products.php`, remplacer la première ligne de commentaire par :

```php
// Produits de TECHNUM. « next » vide : le bloc affiche « Version stable, maintenue. ».
// « summary » : résumé des vignettes de l'accueil, 70 caractères au plus.
```

Puis ajouter une ligne `summary` juste après la ligne `tagline` de chaque produit :

```php
        'summary' => 'Revenus, dépenses et comptes en franc CFA, au même endroit.',
```

```php
        'summary' => 'Une demande de rendez-vous transformée en petit jeu.',
```

```php
        'summary' => 'Stages : étudiants et entreprises. Projet en cours.',
```

```php
        'summary' => "Le campus d'Abomey-Calavi, à pied, même sans réseau.",
```

dans l'ordre : Oeil 360° Finance, Dis oui, PROVIA, Carte UAC.

- [ ] **Étape 6 : relancer les tests**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
"$PHP84" vendor/bin/phpstan analyse --no-progress --memory-limit=512M
```

Attendu : tous les tests verts, PHPStan sans erreur.

- [ ] **Étape 7 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add src/Content content/products.php tests/Unit/Content
git commit -F - <<'EOF'
feat(contenu): ajoute un résumé court à chaque produit
EOF
```

---

### Tâche 2 : accueil bleu, accolades et vignettes des produits

**Fichiers :**

- Modifier : `templates/home.php` (section `.hero`), `public/assets/css/site.css`, `tests/Unit/Page/HomePageTest.php`
- Créer : `templates/partials/product-strip.php`

**Interfaces :**

- Consomme : `Product::$summary`, `Product::anchor()`, `Product::$image` (`src`, `srcSmall`, `width`, `height`, `frame`), `ProductStage::$value`, `ProductStage::label()`, variable partagée `products`.
- Produit : classes `.hero__braces`, `.button--light`, `.product-strip`, `.product-strip__list`, `.product-tile`, `.product-tile__shot--desktop|phone`, `.product-tile__name`, `.product-tile__summary`, `.chip`, `.chip--{valeur de l'état}` ; variables `--on-blue` et `--on-charcoal`.

- [ ] **Étape 1 : écrire les tests qui échouent**

Dans `tests/Unit/Page/HomePageTest.php`, dans `testHeroCarriesTheBrandPromiseAndTwoActions()`, remplacer `.hero__actions .button--primary` par `.hero__actions .button--light`. Puis ajouter après ce test :

```php
    public function testHeroBracesAreDecorative(): void
    {
        self::assertSame('true', $this->html->attribute('.hero__braces', 'aria-hidden'));
        self::assertSame('{}', str_replace(' ', '', $this->html->text('.hero__braces')));
    }

    public function testStripLinksEachProductToItsTab(): void
    {
        self::assertSame(
            ['#produit-oeil360-finance', '#produit-dis-oui', '#produit-provia', '#produit-carte-uac'],
            $this->html->attributes('.product-tile', 'href'),
        );
        self::assertSame(['Oeil 360° Finance', 'Dis oui', 'PROVIA', 'Carte UAC'], $this->html->texts('.product-tile__name'));
        self::assertSame(['En service', 'En service', 'Bêta', 'Pilote'], $this->html->texts('.product-tile .chip'));
        self::assertSame('chip chip--beta', $this->html->attribute('.product-strip li:nth-child(3) .chip', 'class'));
        self::assertStringContainsString('Projet en cours', $this->html->text('.product-strip li:nth-child(3) .product-tile__summary'));
    }
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomePageTest
```

Attendu : trois échecs, « Élément introuvable » pour `.hero__actions .button--light`, `.hero__braces` et `.product-strip li:nth-child(3) .chip`.

- [ ] **Étape 3 : écrire l'accueil et les vignettes**

Dans `templates/home.php`, remplacer toute la section `<section class="hero" …>…</section>` par :

```php
<section class="hero" aria-labelledby="titre-accueil">
  <div class="hero__braces" aria-hidden="true"><span>{</span><span>}</span></div>
  <div class="hero__inner">
    <div class="hero__text">
      <h1 class="hero__title" id="titre-accueil">Des solutions numériques conçues pour vos réalités.</h1>
      <p class="hero__lead">TECHNUM conçoit, met en ligne et maintient des applications pour les entreprises, les institutions et les porteurs de projets du Bénin. Nos propres produits sont déjà en service&nbsp;: vous pouvez les essayer dès maintenant.</p>
      <div class="hero__actions">
        <a class="button button--light" href="#contact">Parler de votre projet</a>
        <a class="hero__more" href="#produits">Voir nos produits</a>
      </div>
    </div>
    <?= $view->render('partials/register') ?>
  </div>
</section>

<?= $view->render('partials/product-strip') ?>
```

Créer `templates/partials/product-strip.php` :

```php
<?php
/**
 * @var Technum\View\View $view
 * @var list<Technum\Content\Product> $products
 */
?>
<div class="product-strip">
  <ul class="product-strip__list" role="list">
    <?php foreach ($products as $product) : ?>
      <?php
      $image = $product->image;
      $small = $image->srcSmall !== '';
      ?>
      <li>
        <a class="product-tile" href="#<?= e($product->anchor()) ?>">
          <span class="product-tile__shot product-tile__shot--<?= e($image->frame) ?>">
            <img src="<?= e($view->asset($small ? $image->srcSmall : $image->src)) ?>" alt="" width="<?= $small ? intdiv($image->width, 2) : $image->width ?>" height="<?= $small ? intdiv($image->height, 2) : $image->height ?>" loading="lazy" decoding="async">
          </span>
          <span class="product-tile__body">
            <span class="product-tile__head">
              <span class="product-tile__name"><?= e($product->name) ?></span>
              <span class="chip chip--<?= e($product->stage->value) ?>"><?= e($product->stage->label()) ?></span>
            </span>
            <span class="product-tile__summary"><?= e($product->summary) ?></span>
          </span>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</div>
```

- [ ] **Étape 4 : écrire les styles**

Dans `public/assets/css/site.css` :

1. Remplacer les lignes 2 à 4 du commentaire d'en-tête par :

```css
 * Feuille de style de bytechnum.com.
 * Palette, typographie et composants tirés du Brand Book TECHNUM, édition 01, 2026.
 * Hors palette : --error pour les erreurs de formulaire, --on-blue et --on-charcoal pour les textes
 * secondaires posés sur le bleu foncé et sur le charbon.
```

2. Dans `:root`, ajouter après `--error: #b42318;` :

```css
  --on-blue: #dde4ff;
  --on-charcoal: #dcdadb;
```

3. Dans le bloc « Boutons », ajouter après la règle `.button--secondary:hover, .button--secondary:focus-visible { … }` :

```css

.button--light {
  background: var(--white);
  color: var(--blue-dark);
}

.button--light:hover,
.button--light:focus-visible {
  background: var(--ice);
  color: var(--blue-dark);
}
```

4. Remplacer tout le bloc qui va de la ligne `/* Accueil */` jusqu'à la ligne précédant `/* Registre des produits : l'élément marquant de la page */` par :

```css
/* Accueil : fond bleu de la marque, accolades décoratives, registre sur carte blanche */

.hero {
  position: relative;
  overflow: hidden;
  padding: var(--space-7) 0 calc(var(--space-9) + 3rem);
  background: var(--blue-dark);
  color: var(--white);
}

.hero :focus-visible {
  outline-color: var(--white);
}

.hero__braces {
  position: absolute;
  top: 1rem;
  right: -3rem;
  display: flex;
  gap: 7rem;
  color: var(--blue);
  font-family: var(--font-display);
  font-size: 16rem;
  font-weight: 700;
  line-height: 0.9;
  pointer-events: none;
  user-select: none;
}

.hero__braces span:first-child {
  transform: translateX(-4.5rem);
}

.hero__braces span:last-child {
  transform: translateX(4.5rem);
}

.hero__inner {
  position: relative;
  display: grid;
  gap: var(--space-7);
  max-width: var(--container);
  margin: 0 auto;
  padding: 0 var(--gutter);
}

.hero__title {
  max-width: 13ch;
  font-size: clamp(2.5rem, 1.6rem + 3vw, 3.875rem);
  line-height: 1.05;
  letter-spacing: -0.02em;
}

.hero__lead {
  max-width: 34rem;
  margin-top: var(--space-5);
  color: var(--on-blue);
  font-size: var(--text-lead);
  line-height: 1.65;
}

.hero__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-4) var(--space-6);
  margin-top: var(--space-6);
}

.hero__more {
  color: var(--white);
  font-family: var(--font-display);
  font-weight: 600;
  text-decoration-line: underline;
  text-decoration-color: rgba(255, 255, 255, 0.45);
  text-decoration-thickness: 2px;
  text-underline-offset: 0.4em;
}

.hero__more:hover,
.hero__more:focus-visible {
  color: var(--white);
  text-decoration-color: var(--white);
}

@media (min-width: 60rem) {
  .hero {
    padding: var(--space-8) 0 calc(var(--space-9) + 4rem);
  }

  .hero__braces {
    top: 1.5rem;
    right: 2rem;
    gap: 14rem;
    font-size: 29rem;
  }

  .hero__inner {
    grid-template-columns: minmax(0, 7fr) minmax(0, 5fr);
    align-items: center;
    gap: var(--space-8);
  }
}

```

5. Dans le bloc du registre, remplacer la règle `.register { … }` et la règle `.register__item + .register__item { … }` par :

```css
.register {
  --track-empty: var(--ice);
  padding: var(--space-6);
  border-radius: var(--radius-panel);
  background: var(--white);
  color: var(--charcoal);
}

.register :focus-visible {
  outline-color: var(--blue);
}
```

```css
.register__item + .register__item {
  border-top: 1px solid var(--line);
}
```

6. Ajouter ce bloc juste avant la ligne `/* Sections : colonne de repère à gauche, séparée par le filet vertical du logo */` :

```css
/* Vignettes des produits : elles chevauchent le bas de l'accueil et ouvrent l'onglet du produit */

.product-strip {
  position: relative;
  max-width: var(--container);
  margin: -6.5rem auto 0;
  padding: 0 var(--gutter);
}

.product-strip__list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: var(--space-4);
  padding: 0;
  list-style: none;
}

.product-tile {
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
  border: 1px solid var(--line);
  border-radius: var(--radius-panel);
  background: var(--white);
  color: var(--charcoal);
  transition:
    transform 0.2s ease,
    border-color 0.2s ease;
}

.product-tile:hover,
.product-tile:focus-visible {
  border-color: var(--blue);
  color: var(--charcoal);
  text-decoration: none;
}

.product-tile__shot {
  display: flex;
  justify-content: center;
  height: 7.5rem;
  overflow: hidden;
  padding: var(--space-3) var(--space-3) 0;
  background: var(--ice);
}

.product-tile__shot img {
  width: 100%;
  height: 100%;
  border-radius: 6px 6px 0 0;
  object-fit: cover;
  object-position: top;
}

.product-tile__shot--phone img {
  width: 48%;
  border-radius: 12px 12px 0 0;
}

.product-tile__body {
  display: grid;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-4) var(--space-4);
}

.product-tile__head {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-2);
}

.product-tile__name {
  font-family: var(--font-display);
  font-weight: 700;
  line-height: 1.25;
}

.product-tile__summary {
  color: var(--charcoal-soft);
  font-size: var(--text-small);
  line-height: 1.5;
}

/* Pastilles d'état */

.chip {
  display: inline-block;
  padding: 0.2rem 0.625rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 500;
  white-space: nowrap;
}

.chip--en-service {
  background: var(--blue);
  color: var(--white);
}

.chip--beta {
  background: var(--ice);
  color: var(--blue-dark);
}

.chip--pilote,
.chip--conception {
  border: 1px solid var(--line);
  background: var(--off-white);
  color: var(--charcoal);
}

@media (min-width: 60rem) {
  .product-strip {
    margin-top: -7.5rem;
  }

  .product-strip__list {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: var(--space-5);
  }

  .product-tile__shot {
    height: 9.75rem;
    padding: var(--space-4) var(--space-4) 0;
  }
}

```

7. Dans le palier `@media (min-width: 60rem)` situé sous « Grands écrans », supprimer les deux règles `.hero { … }` et `.hero__inner { … }`.

- [ ] **Étape 5 : relancer les tests et la vérification complète**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomePageTest
npm run format
npm run format:check
```

Attendu : `HomePageTest` vert, Prettier sans écart. Puis lancer la vérification complète des conventions : tout est vert.

- [ ] **Étape 6 : contrôler l'accueil dans le navigateur**

Lancer le serveur local. Dans Chromium, à 1440 × 900 puis à 390 × 844, ouvrir `http://127.0.0.1:8080/` et capturer l'écran vers `.captures/t2-<largeur>.png`. Attendu : accueil bleu, titre blanc, registre sur carte blanche, accolades derrière le registre sur ordinateur, quatre vignettes qui chevauchent le bas de l'accueil sur ordinateur et deux colonnes de vignettes sur téléphone, aucune barre de défilement horizontale. Arrêter le serveur et supprimer `.captures`.

- [ ] **Étape 7 : committer**

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .captures
git add templates/home.php templates/partials/product-strip.php public/assets/css/site.css tests/Unit/Page/HomePageTest.php
git commit -F - <<'EOF'
feat(accueil): passe l'accueil en bleu et ajoute les vignettes des produits
EOF
```

---

### Tâche 3 : sections à titre en tête et fonds alternés

**Fichiers :**

- Modifier : `templates/home.php` (sections `#produits` à `#contact`), `public/assets/css/site.css`, `tests/Unit/Page/HomePageTest.php`

**Interfaces :**

- Consomme : contenu des sections actuel.
- Produit : classes `.section__head`, `.section--ice`, `.section--charcoal`, `.section--blue`, `.section--split`, `.section--contact`. Les tâches 4, 6 et 7 remplacent le contenu des sections sans toucher à leur en-tête.

- [ ] **Étape 1 : écrire les tests qui échouent**

Dans `tests/Unit/Page/HomePageTest.php`, ajouter après `testStripLinksEachProductToItsTab()` :

```php
    public function testSectionsAlternateTheirBackgrounds(): void
    {
        self::assertSame(
            ['section', 'section section--ice', 'section section--charcoal section--split', 'section section--blue', 'section section--contact'],
            $this->html->attributes('main > .section', 'class'),
        );
    }

    public function testEachSectionOpensWithItsTitle(): void
    {
        self::assertSame(
            ['Nos produits', 'Autres réalisations', 'Ce que nous faisons pour vous', 'Comment se passe un projet', 'Parlons de votre projet'],
            $this->html->texts('.section__head .section__title'),
        );
        self::assertSame(0, $this->html->count('.section__aside'));
    }
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomePageTest
```

Attendu : deux échecs, classes de section différentes et titres introuvables sous `.section__head`.

- [ ] **Étape 3 : réécrire les sections**

Dans `templates/home.php`, remplacer tout ce qui suit `<?= $view->render('partials/product-strip') ?>` par :

```php

<section class="section" id="produits" aria-labelledby="titre-produits">
  <div class="section__inner">
    <div class="section__head">
      <h2 class="section__title" id="titre-produits">Nos produits</h2>
      <p class="section__context">Quatre outils conçus, hébergés et maintenus par TECHNUM.</p>
    </div>
    <?php foreach ($products as $product) : ?>
      <?= $view->render('partials/product', ['product' => $product]) ?>
    <?php endforeach ?>
  </div>
</section>

<section class="section section--ice" id="realisations" aria-labelledby="titre-realisations">
  <div class="section__inner">
    <div class="section__head">
      <h2 class="section__title" id="titre-realisations">Autres réalisations</h2>
      <p class="section__context">Des preuves de concept, un produit en pause et un mandat client.</p>
    </div>
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
</section>

<section class="section section--charcoal section--split" id="services" aria-labelledby="titre-services">
  <div class="section__inner">
    <div class="section__head">
      <h2 class="section__title" id="titre-services">Ce que nous faisons pour vous</h2>
      <p class="section__context">Chaque service renvoie à un exemple que vous pouvez ouvrir.</p>
    </div>
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
</section>

<section class="section section--blue" id="methode" aria-labelledby="titre-methode">
  <div class="section__inner">
    <div class="section__head">
      <h2 class="section__title" id="titre-methode">Comment se passe un projet</h2>
      <p class="section__context">Nous vous accompagnons de la conception au déploiement, puis après la livraison.</p>
    </div>
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
</section>

<section class="section section--contact" id="contact" aria-labelledby="titre-contact">
  <div class="section__inner">
    <div class="section__head">
      <h2 class="section__title" id="titre-contact">Parlons de votre projet</h2>
      <p class="section__context">Décrivez votre besoin en quelques lignes. Nous vous répondons par e-mail.</p>
    </div>
    <div class="contact">
      <?= $view->render('partials/contact-form', ['form' => $form, 'needs' => $needs]) ?>
      <?= $view->render('partials/contact-direct') ?>
    </div>
  </div>
</section>
```

- [ ] **Étape 4 : écrire la mise en page des sections**

Dans `public/assets/css/site.css`, remplacer tout le bloc qui va de la ligne `/* Sections : colonne de repère à gauche, séparée par le filet vertical du logo */` jusqu'à la ligne précédant `/* Produits */` par :

```css
/* Sections : un titre en tête, puis le contenu ; chaque section a son fond */

.section {
  padding: var(--space-8) 0;
}

.section__inner {
  max-width: var(--container);
  margin: 0 auto;
  padding: 0 var(--gutter);
}

.section__head {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  justify-content: space-between;
  gap: var(--space-3) var(--space-7);
  margin-bottom: var(--space-6);
}

.section__title {
  font-size: clamp(1.875rem, 1.45rem + 1.4vw, 2.5rem);
  line-height: 1.1;
}

.section__context {
  max-width: 34rem;
  color: var(--charcoal-soft);
}

.section--ice {
  background: var(--ice);
}

.section--charcoal {
  background: var(--charcoal);
  color: var(--white);
}

.section--blue {
  background: var(--blue-dark);
  color: var(--white);
}

.section--charcoal .section__context {
  color: var(--on-charcoal);
}

.section--blue .section__context {
  color: var(--on-blue);
}

.section--charcoal :focus-visible,
.section--blue :focus-visible {
  outline-color: var(--white);
}

@media (min-width: 60rem) {
  .section {
    padding: var(--space-9) 0;
  }

  .section__head {
    margin-bottom: var(--space-7);
  }

  .section--split .section__inner {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
    align-items: start;
    column-gap: var(--space-8);
  }

  .section--split .section__head {
    display: block;
    margin-bottom: 0;
  }

  .section--split .section__context {
    margin-top: var(--space-4);
  }
}

```

Dans le palier `@media (min-width: 60rem)` situé sous « Grands écrans », supprimer les quatre règles `.section__inner { … }`, `.section__aside { … }`, `.section__label { … }` et `.section__body { … }`.

- [ ] **Étape 5 : relancer les tests**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
npm run format
npm run format:check
```

Attendu : tous les tests verts.

- [ ] **Étape 6 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add templates/home.php public/assets/css/site.css tests/Unit/Page/HomePageTest.php
git commit -F - <<'EOF'
feat(accueil): ouvre chaque section par son titre et alterne les fonds
EOF
```

---

### Tâche 4 : produits à onglets, sans script d'abord

**Fichiers :**

- Modifier : `templates/home.php` (section `#produits`), `public/assets/css/site.css`, `tests/Unit/Page/HomePageTest.php`
- Créer : `templates/partials/product-tabs.php`

**Interfaces :**

- Consomme : `Product::anchor()`, `Product::$slug`, `Product::$icon`, `Product::$name`, `ProductStage::label()`, partiel `partials/product` inchangé.
- Produit : élément `.product-tabs` avec `role="tablist"` et l'attribut `hidden` ; boutons `.product-tabs__tab` avec `role="tab"`, `id="onglet-{slug}"`, `aria-controls="produit-{slug}"`, `aria-selected` et `tabindex` ; panneaux `article.product#produit-{slug}` dans `.products`.

- [ ] **Étape 1 : écrire les tests qui échouent**

Dans `tests/Unit/Page/HomePageTest.php`, ajouter après `testEachSectionOpensWithItsTitle()` :

```php
    public function testProductTabsWaitForTheScript(): void
    {
        $tablist = $this->html->elements('.product-tabs')[0];

        self::assertTrue($tablist->hasAttribute('hidden'));
        self::assertSame('tablist', $tablist->getAttribute('role'));
        self::assertSame(
            ['produit-oeil360-finance', 'produit-dis-oui', 'produit-provia', 'produit-carte-uac'],
            $this->html->attributes('.product-tabs__tab', 'aria-controls'),
        );
        self::assertSame(['onglet-oeil360-finance', 'onglet-dis-oui', 'onglet-provia', 'onglet-carte-uac'], $this->html->attributes('.product-tabs__tab', 'id'));
        self::assertSame(['true', 'false', 'false', 'false'], $this->html->attributes('.product-tabs__tab', 'aria-selected'));
        self::assertSame(['0', '-1', '-1', '-1'], $this->html->attributes('.product-tabs__tab', 'tabindex'));
    }

    public function testEveryProductStaysVisibleWithoutTheScript(): void
    {
        self::assertSame(4, $this->html->count('#produits .products > .product:not([hidden])'));
    }
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomePageTest
```

Attendu : deux échecs, `.product-tabs` introuvable et aucun produit sous `.products`.

- [ ] **Étape 3 : écrire la liste d'onglets et les panneaux**

Créer `templates/partials/product-tabs.php` :

```php
<?php
/**
 * Liste d'onglets des produits. Masquée sans JavaScript : les panneaux s'affichent alors les uns sous les autres.
 *
 * @var Technum\View\View $view
 * @var list<Technum\Content\Product> $products
 */
?>
<div class="product-tabs" role="tablist" aria-label="Nos produits" hidden>
  <?php foreach ($products as $index => $product) : ?>
    <button class="product-tabs__tab" type="button" role="tab" id="onglet-<?= e($product->slug) ?>" aria-controls="<?= e($product->anchor()) ?>" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>" tabindex="<?= $index === 0 ? '0' : '-1' ?>">
      <img class="product-tabs__icon" src="<?= e($view->asset($product->icon)) ?>" alt="" width="28" height="28">
      <span class="product-tabs__label">
        <span class="product-tabs__name"><?= e($product->name) ?></span>
        <span class="product-tabs__stage"><?= e($product->stage->label()) ?></span>
      </span>
    </button>
  <?php endforeach ?>
</div>
```

Dans `templates/home.php`, dans la section `#produits`, remplacer la boucle des produits par :

```php
    <?= $view->render('partials/product-tabs') ?>
    <div class="products">
      <?php foreach ($products as $product) : ?>
        <?= $view->render('partials/product', ['product' => $product]) ?>
      <?php endforeach ?>
    </div>
```

- [ ] **Étape 4 : écrire les styles des onglets et des panneaux**

Dans `public/assets/css/site.css` :

1. Dans le bloc « Boutons », remplacer les règles `.button--secondary { … }` et `.button--secondary:hover, .button--secondary:focus-visible { … }` par :

```css
.button--secondary {
  border: 2px solid var(--blue);
  background: var(--white);
  color: var(--blue-dark);
}

.button--secondary:hover,
.button--secondary:focus-visible {
  border-color: var(--blue-dark);
  background: var(--ice);
  color: var(--blue-dark);
}
```

2. Remplacer tout le bloc qui va de la ligne `/* Produits */` jusqu'à la ligne précédant `/* Autres réalisations */` par :

```css
/* Produits : un panneau par produit ; le script les présente en onglets */

.product-tabs {
  display: flex;
  gap: var(--space-3);
  margin: 0 calc(var(--gutter) * -1) var(--space-6);
  padding: 0 var(--gutter) var(--space-2);
  overflow-x: auto;
  scroll-padding-inline: var(--gutter);
  scroll-snap-type: x mandatory;
}

.product-tabs__tab {
  display: flex;
  flex: 0 0 auto;
  align-items: center;
  gap: var(--space-3);
  min-height: 3.5rem;
  padding: var(--space-3) var(--space-4);
  border: 1px solid var(--line);
  border-radius: var(--radius-frame);
  background: var(--white);
  color: var(--charcoal);
  font: inherit;
  text-align: left;
  cursor: pointer;
  scroll-snap-align: start;
  transition:
    border-color 0.2s ease,
    background-color 0.2s ease;
}

.product-tabs__tab:hover {
  border-color: var(--blue);
}

.product-tabs__tab[aria-selected='true'] {
  border-color: var(--blue);
  background: var(--ice);
}

.product-tabs__icon {
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 6px;
  object-fit: contain;
}

.product-tabs__label {
  display: grid;
}

.product-tabs__name {
  font-family: var(--font-display);
  font-size: 0.9375rem;
  font-weight: 600;
  line-height: 1.3;
}

.product-tabs__stage {
  color: var(--charcoal-soft);
  font-size: 0.75rem;
}

.product {
  display: grid;
  gap: var(--space-5);
}

.product + .product {
  margin-top: var(--space-8);
}

.js .product + .product {
  margin-top: 0;
}

.product:focus-visible {
  outline-offset: 6px;
}

.product__media {
  min-width: 0;
}

.product__frame {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  height: 15rem;
  overflow: hidden;
  padding: var(--space-5) var(--space-5) 0;
  border-radius: var(--radius-panel);
  background: var(--ice);
}

.product__frame img {
  width: 100%;
  border-radius: 10px 10px 0 0;
}

.product__media--phone .product__frame img {
  width: min(13rem, 70%);
  border-radius: 22px 22px 0 0;
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

@media (min-width: 60rem) {
  .product-tabs {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin: 0 0 var(--space-7);
    padding: 0;
    overflow: visible;
  }

  .product {
    grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
    align-items: center;
    gap: var(--space-8);
  }

  .product__frame {
    height: 25rem;
    padding: var(--space-6) var(--space-6) 0;
  }
}

```

3. Dans le palier `@media (min-width: 60rem)` situé sous « Grands écrans », supprimer la règle `.product { … }`.

- [ ] **Étape 5 : relancer les tests**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
npm run format
npm run format:check
```

Attendu : tous les tests verts. Sans script, la page affiche les quatre panneaux les uns sous les autres.

- [ ] **Étape 6 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add templates/home.php templates/partials/product-tabs.php public/assets/css/site.css tests/Unit/Page/HomePageTest.php
git commit -F - <<'EOF'
feat(produits): présente chaque produit dans un panneau prêt pour les onglets
EOF
```

---

### Tâche 5 : script des onglets

**Fichiers :**

- Modifier : `public/assets/js/site.js`

**Interfaces :**

- Consomme : `.product-tabs` et ses boutons `role="tab"` (tâche 4), panneaux `#produit-{slug}`, liens `a[href^="#produit-"]` des vignettes, du registre et des exemples de services.
- Produit : fonction `setUpProductTabs()`, appelée par `init()`.

- [ ] **Étape 1 : constater l'absence de comportement**

Lancer le serveur local. Dans Chromium à 1440 × 900, ouvrir `http://127.0.0.1:8080/#produit-carte-uac`, puis exécuter dans la page :

```js
() => ({
  tablistVisible: !document.querySelector('.product-tabs').hidden,
  visiblePanels: [...document.querySelectorAll('.products > .product')].filter((p) => !p.hidden).length,
})
```

Attendu : `{ tablistVisible: false, visiblePanels: 4 }`, car aucun script ne gère encore les onglets.

- [ ] **Étape 2 : écrire le script**

Dans `public/assets/js/site.js`, remplacer le commentaire d'en-tête par :

```js
/*
 * Améliorations progressives de bytechnum.com.
 * La page fonctionne sans ce script : il replie le menu sur petit écran, présente les produits
 * en onglets, compte les caractères du message et empêche un double envoi du formulaire.
 */
```

Ajouter cette fonction juste avant `function init() {` :

```js
  function setUpProductTabs() {
    const tablist = document.querySelector('.product-tabs');
    if (!tablist) {
      return;
    }

    const tabs = Array.from(tablist.querySelectorAll('[role="tab"]'));
    const panels = tabs.map((tab) => document.getElementById(tab.getAttribute('aria-controls') || ''));
    if (tabs.length === 0 || panels.some((panel) => !panel)) {
      return;
    }

    panels.forEach((panel, index) => {
      panel.setAttribute('role', 'tabpanel');
      panel.setAttribute('aria-labelledby', tabs[index].id);
      panel.tabIndex = 0;
    });

    const select = (index, moveFocus) => {
      tabs.forEach((tab, position) => {
        const isSelected = position === index;
        tab.setAttribute('aria-selected', String(isSelected));
        tab.tabIndex = isSelected ? 0 : -1;
        panels[position].hidden = !isSelected;
      });
      if (moveFocus) {
        tabs[index].focus();
      }
    };

    const indexOfHash = (hash) => panels.findIndex((panel) => `#${panel.id}` === hash);

    const reveal = (index) => {
      select(index, false);
      tablist.scrollIntoView({ block: 'start' });
    };

    tablist.hidden = false;
    select(0, false);

    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => select(index, false));
    });

    tablist.addEventListener('keydown', (event) => {
      const current = tabs.indexOf(document.activeElement);
      if (current === -1) {
        return;
      }
      const last = tabs.length - 1;
      const targets = {
        ArrowRight: current === last ? 0 : current + 1,
        ArrowLeft: current === 0 ? last : current - 1,
        Home: 0,
        End: last,
      };
      const target = targets[event.key];
      if (target === undefined) {
        return;
      }
      event.preventDefault();
      select(target, true);
    });

    document.addEventListener('click', (event) => {
      const link = event.target instanceof Element ? event.target.closest('a[href^="#produit-"]') : null;
      const index = link ? indexOfHash(link.getAttribute('href')) : -1;
      if (index === -1) {
        return;
      }
      event.preventDefault();
      window.history.pushState(null, '', link.getAttribute('href'));
      reveal(index);
    });

    window.addEventListener('hashchange', () => {
      const index = indexOfHash(window.location.hash);
      if (index !== -1) {
        reveal(index);
      }
    });

    const initial = indexOfHash(window.location.hash);
    if (initial !== -1) {
      reveal(initial);
    }
  }

```

Dans `init()`, ajouter `setUpProductTabs();` juste après `setUpMenu();`.

- [ ] **Étape 3 : lancer les outils du front**

```bash
cd /c/wamp64/www/TECHNUM
npm run format
npm run lint:js
npm run format:check
wc -c public/assets/js/site.js
```

Attendu : aucune erreur, script sous 10 000 octets.

- [ ] **Étape 4 : constater le comportement**

Recharger `http://127.0.0.1:8080/#produit-carte-uac` dans Chromium et exécuter dans la page :

```js
() => ({
  tablistVisible: !document.querySelector('.product-tabs').hidden,
  selected: document.querySelector('[role="tab"][aria-selected="true"]').id,
  visiblePanels: [...document.querySelectorAll('.products > .product')].filter((p) => !p.hidden).map((p) => p.id),
  tablistTop: Math.round(document.querySelector('.product-tabs').getBoundingClientRect().top),
})
```

Attendu : `tablistVisible` à `true`, `selected` à `onglet-carte-uac`, un seul panneau visible, `produit-carte-uac`, et `tablistTop` entre 70 et 130, sous l'en-tête fixe.

- [ ] **Étape 5 : contrôler la souris, le clavier, les liens et l'absence de script**

Toujours dans Chromium :

1. Cliquer sur l'onglet Dis oui. Attendu : son panneau seul visible, `aria-selected` à `true` sur cet onglet.
2. Donner le focus à l'onglet Oeil 360° Finance, puis presser la flèche droite, Fin, Début et la flèche gauche. Attendu : les onglets Dis oui, Carte UAC, Oeil 360° Finance puis Carte UAC sont successivement sélectionnés et prennent le focus, avec un contour visible.
3. Remonter en haut de la page, cliquer sur la vignette PROVIA, faire défiler, puis cliquer de nouveau sur la même vignette. Attendu : chaque fois l'onglet PROVIA est sélectionné et la liste d'onglets revient sous l'en-tête.
4. Cliquer sur l'exemple « Carte UAC » des services. Attendu : l'onglet Carte UAC est sélectionné.
5. Ouvrir un nouveau contexte Chromium avec JavaScript désactivé et charger `http://127.0.0.1:8080/`. Attendu : la liste d'onglets n'apparaît pas et les quatre panneaux s'affichent les uns sous les autres.
6. Lire la console : aucune erreur.

Arrêter le serveur et supprimer `.captures`.

- [ ] **Étape 6 : vérification complète et commit**

Lancer la vérification complète des conventions, puis :

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .captures
git add public/assets/js/site.js
git commit -F - <<'EOF'
feat(produits): présente les produits en onglets accessibles au clavier
EOF
```

---

### Tâche 6 : réalisations en cartes et services en pastilles

**Fichiers :**

- Modifier : `templates/home.php` (liste des services), `public/assets/css/site.css`, `tests/Unit/Page/HomePageTest.php`

**Interfaces :**

- Consomme : `Service::$examples`, `Service::examplesLabel()`, `ServiceExample::hasLink()`, classes de section de la tâche 3.
- Produit : liste `.service__examples` avec `aria-label`, pastilles `.service__example`.

- [ ] **Étape 1 : écrire le test qui échoue**

Dans `tests/Unit/Page/HomePageTest.php`, dans `testProjectsServicesAndStepsAreListed()`, supprimer la ligne qui attend `'PROVIA et Carte UAC'`. Puis ajouter après `testEveryProductStaysVisibleWithoutTheScript()` :

```php
    public function testServiceExamplesAreClickablePills(): void
    {
        self::assertSame(['PROVIA', 'Carte UAC'], $this->html->texts('.service:nth-child(2) .service__example'));
        self::assertSame(['#produit-provia', '#produit-carte-uac'], $this->html->attributes('.service:nth-child(2) a.service__example', 'href'));
        self::assertSame('Exemples', $this->html->attribute('.service:nth-child(2) .service__examples', 'aria-label'));
    }
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomePageTest
```

Attendu : un échec, aucune pastille `.service__example`.

- [ ] **Étape 3 : écrire les pastilles**

Dans `templates/home.php`, dans la section `#services`, remplacer toute la liste `<ul class="services" role="list">…</ul>` par :

```php
    <ul class="services" role="list">
      <?php foreach ($services as $service) : ?>
        <li class="service">
          <h3 class="service__name"><?= e($service->name) ?></h3>
          <p class="service__description"><?= e($service->description) ?></p>
          <ul class="service__examples" role="list" aria-label="<?= e($service->examplesLabel()) ?>">
            <?php foreach ($service->examples as $example) : ?>
              <li>
                <?php if ($example->hasLink()) : ?>
                  <a class="service__example" href="<?= e($example->href) ?>"><?= e($example->label) ?></a>
                <?php else : ?>
                  <span class="service__example"><?= e($example->label) ?></span>
                <?php endif ?>
              </li>
            <?php endforeach ?>
          </ul>
        </li>
      <?php endforeach ?>
    </ul>
```

- [ ] **Étape 4 : écrire les styles**

Dans `public/assets/css/site.css`, remplacer tout le bloc qui va de la ligne `/* Autres réalisations */` jusqu'à la ligne précédant `/* Méthode : une vraie séquence, donc numérotée */` par :

```css
/* Autres réalisations : cartes blanches sur fond bleu clair */

.projects {
  display: grid;
  gap: var(--space-4);
  padding: 0;
  list-style: none;
}

.project {
  display: grid;
  align-content: start;
  gap: var(--space-3);
  padding: var(--space-5);
  border-radius: var(--radius-panel);
  background: var(--white);
}

.project__head {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-2) var(--space-3);
}

.project__name {
  font-size: 1.25rem;
}

.project__nature {
  padding: 0.2rem 0.625rem;
  border-radius: 999px;
  background: var(--ice);
  color: var(--blue-dark);
  font-size: 0.75rem;
  font-weight: 500;
}

.project__description {
  color: var(--charcoal-soft);
}

.project__link {
  display: inline-flex;
  align-items: center;
  min-height: 2.75rem;
  font-family: var(--font-display);
  font-size: 0.9375rem;
  font-weight: 600;
}

@media (min-width: 48rem) {
  .projects {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--space-5);
  }

  .project {
    padding: var(--space-6);
  }
}

/* Services : fond charbon, un filet bleu par service, exemples en pastilles */

.services {
  display: grid;
  gap: var(--space-7);
  padding: 0;
  list-style: none;
}

.service {
  padding-top: var(--space-4);
  border-top: 2px solid var(--blue);
}

.service__name {
  font-size: 1.25rem;
  font-weight: 600;
}

.service__description {
  margin-top: var(--space-2);
  color: var(--on-charcoal);
}

.service__examples {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
  margin-top: var(--space-4);
  padding: 0;
  list-style: none;
}

.service__example {
  display: inline-flex;
  align-items: center;
  min-height: 2.75rem;
  padding: 0 0.875rem;
  border: 1px solid rgba(255, 255, 255, 0.35);
  border-radius: 999px;
  color: var(--white);
  font-size: 0.8125rem;
}

a.service__example:hover,
a.service__example:focus-visible {
  border-color: var(--white);
  color: var(--white);
  text-decoration: none;
}

@media (min-width: 48rem) {
  .services {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: var(--space-7);
  }
}

```

Dans le palier `@media (min-width: 48rem)` situé sous « Grands écrans », supprimer les règles `.project { … }`, `.project__head { … }` et `.services { … }`, puis le palier lui-même s'il est vide.

- [ ] **Étape 5 : relancer les tests**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
npm run format
npm run format:check
```

Attendu : tous les tests verts.

- [ ] **Étape 6 : committer**

```bash
cd /c/wamp64/www/TECHNUM
git add templates/home.php public/assets/css/site.css tests/Unit/Page/HomePageTest.php
git commit -F - <<'EOF'
feat(accueil): passe les réalisations en cartes et les exemples en pastilles
EOF
```

---

### Tâche 7 : méthode en bandeau bleu et contact repensé

**Fichiers :**

- Modifier : `templates/home.php` (section `#contact`), `templates/partials/contact-direct.php`, `public/assets/css/site.css`, `tests/Unit/Page/HomePageTest.php`

**Interfaces :**

- Consomme : `.section--blue` et `.section--contact` (tâche 3), `.button--light` (tâche 2), partiels `contact-form` et `contact-direct`.
- Produit : grille `.contact` avec les zones `head`, `form` et `direct` ; carte `.contact-direct` bleue.

- [ ] **Étape 1 : écrire le test qui échoue**

Dans `tests/Unit/Page/HomePageTest.php`, ajouter `use Dom\Element;` aux imports, puis ajouter après `testServiceExamplesAreClickablePills()` :

```php
    public function testContactShowsTheFormBeforeTheDirectCard(): void
    {
        $classes = array_map(
            static fn (Element $element): string => (string) $element->getAttribute('class'),
            $this->html->elements('#contact .contact > *'),
        );

        self::assertSame(['section__head', 'contact-form-area', 'contact-direct'], $classes);
        self::assertSame('button button--light contact-direct__action', $this->html->attribute('.contact-direct__action', 'class'));
    }
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter HomePageTest
```

Attendu : un échec, la grille `.contact` ne contient pas encore l'en-tête et le bouton n'a pas la classe `button--light`.

- [ ] **Étape 3 : réécrire la section de contact**

Dans `templates/home.php`, remplacer toute la section `#contact` par :

```php
<section class="section section--contact" id="contact" aria-labelledby="titre-contact">
  <div class="section__inner contact">
    <div class="section__head">
      <h2 class="section__title" id="titre-contact">Parlons de votre projet</h2>
      <p class="section__context">Décrivez votre besoin en quelques lignes. Nous vous répondons par e-mail.</p>
    </div>
    <?= $view->render('partials/contact-form', ['form' => $form, 'needs' => $needs]) ?>
    <?= $view->render('partials/contact-direct') ?>
  </div>
</section>
```

Dans `templates/partials/contact-direct.php`, remplacer `button button--secondary contact-direct__action` par `button button--light contact-direct__action`.

- [ ] **Étape 4 : écrire les styles**

Dans `public/assets/css/site.css` :

1. Remplacer tout le bloc qui va de la ligne `/* Méthode : une vraie séquence, donc numérotée */` jusqu'à la ligne précédant `/* Pages de message : 404 et erreur temporaire */` par :

```css
/* Méthode : une vraie séquence, donc numérotée ; cinq étapes reliées par un trait */

.steps {
  position: relative;
  display: grid;
  gap: var(--space-6);
  padding: 0;
  list-style: none;
  counter-reset: step;
}

.steps::before {
  position: absolute;
  top: 1.375rem;
  bottom: 1.375rem;
  left: calc(1.375rem - 1px);
  width: 2px;
  background: rgba(255, 255, 255, 0.3);
  content: '';
}

.step {
  position: relative;
  display: grid;
  grid-template-columns: 2.75rem minmax(0, 1fr);
  column-gap: var(--space-4);
  counter-increment: step;
}

.step::before {
  display: grid;
  grid-row: span 2;
  place-items: center;
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 50%;
  background: var(--white);
  color: var(--blue-dark);
  font-family: var(--font-display);
  font-weight: 700;
  content: counter(step);
}

.step__title {
  align-self: center;
  font-size: 1.0625rem;
  line-height: 1.35;
}

.step__text {
  margin-top: var(--space-2);
  color: var(--on-blue);
  font-size: 0.9375rem;
}

.commitments {
  max-width: var(--measure);
  margin-top: var(--space-7);
  padding-top: var(--space-5);
  border-top: 1px solid rgba(255, 255, 255, 0.25);
  color: var(--on-blue);
}

@media (min-width: 60rem) {
  .steps {
    grid-template-columns: repeat(5, minmax(0, 1fr));
  }

  .steps::before {
    top: calc(1.375rem - 1px);
    right: 1.375rem;
    bottom: auto;
    left: 1.375rem;
    width: auto;
    height: 2px;
  }

  .step {
    grid-template-columns: minmax(0, 1fr);
    row-gap: var(--space-4);
  }

  .step::before {
    grid-row: auto;
  }

  .step__title {
    align-self: start;
  }
}

/* Contact : formulaire en carte bordée, contact direct sur une carte bleue */

.contact {
  display: grid;
  gap: var(--space-6);
}

.contact .section__head {
  margin-bottom: 0;
}

.contact-direct {
  align-self: start;
  padding: var(--space-6);
  border-radius: var(--radius-panel);
  background: var(--blue);
  color: var(--white);
}

.contact-direct :focus-visible {
  outline-color: var(--white);
}

.contact-direct__title {
  font-size: 1.1875rem;
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
  font-size: var(--text-small);
  font-weight: 500;
}

.contact-direct__list a {
  color: var(--white);
  overflow-wrap: anywhere;
  text-decoration-line: underline;
  text-decoration-color: rgba(255, 255, 255, 0.5);
}

.contact-direct__list a:hover,
.contact-direct__list a:focus-visible {
  color: var(--white);
  text-decoration-color: var(--white);
}

@media (min-width: 75rem) {
  .contact {
    grid-template-areas:
      'head form'
      'direct form';
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
    align-items: start;
    gap: var(--space-6) var(--space-8);
  }

  .contact .section__head {
    display: block;
    grid-area: head;
  }

  .contact .section__context {
    margin-top: var(--space-4);
  }

  .contact-form-area {
    grid-area: form;
  }

  .contact-direct {
    grid-area: direct;
  }
}

```

2. Dans le bloc « Formulaire de contact », remplacer la règle `.contact-form-area { … }` par :

```css
.contact-form-area {
  min-width: 0;
  padding: var(--space-5);
  border: 1px solid var(--line);
  border-radius: var(--radius-panel);
  background: var(--white);
}

@media (min-width: 48rem) {
  .contact-form-area {
    padding: var(--space-6);
  }
}
```

3. Dans le palier `@media (min-width: 75rem)` situé sous « Grands écrans », supprimer la règle `.contact { … }` et corriger le commentaire qui le précède en : `/* Grands écrans : pied de page sur quatre colonnes. En dessous, ces colonnes seraient trop étroites pour l'adresse e-mail et le téléphone. */`.

- [ ] **Étape 5 : relancer les tests et contrôler le rendu**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
npm run format
npm run format:check
```

Attendu : tous les tests verts. Puis, serveur local lancé, ouvrir `http://127.0.0.1:8080/#methode` et `http://127.0.0.1:8080/#contact` à 1440 × 900 et à 390 × 844 dans Chromium. Attendu : cinq étapes reliées par un trait horizontal sur ordinateur et vertical sur téléphone ; formulaire dans une carte bordée à droite, carte bleue sous le titre à gauche sur ordinateur ; sur téléphone, le titre, puis le formulaire, puis la carte bleue. Arrêter le serveur et supprimer `.captures`.

- [ ] **Étape 6 : committer**

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .captures
git add templates/home.php templates/partials/contact-direct.php public/assets/css/site.css tests/Unit/Page/HomePageTest.php
git commit -F - <<'EOF'
feat(accueil): passe la méthode en bandeau bleu et repense le contact
EOF
```

---

### Tâche 8 : mouvement, mouvement réduit et finitions

**Fichiers :**

- Modifier : `public/assets/css/site.css`
- Créer : `tests/Unit/StylesheetTest.php`

**Interfaces :**

- Consomme : `.hero__braces span`, `.product-strip li`, `.product-tile`, `.js .product`, `.track--animated`, `.register__item` (tâches 2 à 5).
- Produit : animations `bit-in`, `braces-open`, `tile-rise` et `panel-in`, toutes déclenchées dans le bloc `@media (prefers-reduced-motion: no-preference)`.

- [ ] **Étape 1 : écrire les tests qui échouent**

Créer `tests/Unit/StylesheetTest.php` :

```php
<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class StylesheetTest extends TestCase
{
    private const STYLESHEET = __DIR__ . '/../../public/assets/css/site.css';

    private const SCRIPT = __DIR__ . '/../../public/assets/js/site.js';

    private const MOTION = '@media (prefers-reduced-motion: no-preference) {';

    public function testLoadSequenceRunsOnlyWhenTheVisitorAcceptsMotion(): void
    {
        $css = (string) file_get_contents(self::STYLESHEET);
        [$motion, $rest] = self::splitMotion($css);

        foreach (['bit-in', 'braces-open', 'tile-rise', 'panel-in'] as $animation) {
            self::assertStringContainsString($animation, $motion);
        }
        self::assertStringNotContainsString('animation:', $rest);
        self::assertStringNotContainsString('translateY(-', $rest);
    }

    public function testStylesheetAndScriptStayLight(): void
    {
        self::assertLessThan(40_000, (int) filesize(self::STYLESHEET));
        self::assertLessThan(10_000, (int) filesize(self::SCRIPT));
    }

    /**
     * Sépare le contenu des blocs de mouvement du reste de la feuille de style.
     *
     * @return array{string, string}
     */
    private static function splitMotion(string $css): array
    {
        $motion = '';
        while (($start = strpos($css, self::MOTION)) !== false) {
            $depth = 0;
            $end = $start;
            $length = strlen($css);
            for ($i = $start + strlen(self::MOTION) - 1; $i < $length; ++$i) {
                if ($css[$i] === '{') {
                    ++$depth;
                } elseif ($css[$i] === '}') {
                    --$depth;
                    if ($depth === 0) {
                        $end = $i;
                        break;
                    }
                }
            }
            $motion .= substr($css, $start, $end - $start + 1);
            $css = substr($css, 0, $start) . substr($css, $end + 1);
        }

        return [$motion, $css];
    }
}
```

- [ ] **Étape 2 : lancer les tests et constater l'échec**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit --filter StylesheetTest
```

Attendu : un échec, `braces-open` introuvable dans le bloc de mouvement.

- [ ] **Étape 3 : écrire le mouvement**

Dans `public/assets/css/site.css`, remplacer tout le bloc qui va de la ligne `/* Seule animation de la page : chaque piste du registre se remplit une fois au chargement. */` jusqu'à la fin de la règle `@keyframes bit-in { … }` par :

```css
/* Mouvement : une seule séquence au chargement, puis des réponses aux gestes.
   Tout se déclenche ici, et rien quand l'appareil demande moins de mouvement. */

@media (prefers-reduced-motion: no-preference) {
  html {
    scroll-behavior: smooth;
  }

  .track--animated .track__bit--done::after {
    transform-origin: left center;
    animation: bit-in 360ms cubic-bezier(0.2, 0.7, 0.2, 1) both;
    animation-delay: calc(500ms + var(--row-delay, 0ms) + var(--bit-delay, 0ms));
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

  .hero__braces span {
    animation: braces-open 1.2s cubic-bezier(0.2, 0.7, 0.2, 1) both;
  }

  .product-strip li {
    animation: tile-rise 700ms cubic-bezier(0.2, 0.7, 0.2, 1) both;
    animation-delay: calc(700ms + var(--tile-delay, 0ms));
  }

  .product-strip li:nth-child(2) {
    --tile-delay: 90ms;
  }

  .product-strip li:nth-child(3) {
    --tile-delay: 180ms;
  }

  .product-strip li:nth-child(4) {
    --tile-delay: 270ms;
  }

  .product-tile:hover,
  .product-tile:focus-visible {
    transform: translateY(-4px);
  }

  .js .product:not([hidden]) {
    animation: panel-in 300ms ease both;
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

/* Sans « to », chaque accolade finit à sa position de repos, déjà écartée. */
@keyframes braces-open {
  from {
    opacity: 0;
    transform: translateX(0);
  }
}

@keyframes tile-rise {
  from {
    opacity: 0;
    transform: translateY(1.75rem);
  }

  to {
    opacity: 1;
    transform: none;
  }
}

@keyframes panel-in {
  from {
    opacity: 0;
    transform: translateY(0.625rem);
  }

  to {
    opacity: 1;
    transform: none;
  }
}
```

- [ ] **Étape 4 : relancer les tests**

```bash
cd /c/wamp64/www/TECHNUM
PHP84=/c/wamp64/bin/php/php8.4.15/php.exe
"$PHP84" vendor/bin/phpunit
npm run format
npm run format:check
```

Attendu : tous les tests verts.

- [ ] **Étape 5 : contrôler le mouvement**

Serveur local lancé, dans Chromium à 1440 × 900 :

1. Recharger `http://127.0.0.1:8080/`. Attendu : les accolades s'écartent, les pistes du registre se remplissent, puis les vignettes montent l'une après l'autre ; une seule fois.
2. Survoler une vignette. Attendu : elle monte de 4 px et prend une bordure bleue.
3. Simuler la préférence « mouvement réduit », recharger. Attendu : tout s'affiche dans son état final, accolades écartées et pistes pleines, sans animation ; au survol, la vignette ne bouge pas.

- [ ] **Étape 6 : contrôler les largeurs**

Pour chaque largeur 320, 390, 820, 1024 et 1440 px, ouvrir `http://127.0.0.1:8080/`, capturer la page entière vers `.captures/t8-<largeur>.png`, exécuter dans la page :

```js
() => ({
  horizontalScroll: document.documentElement.scrollWidth > window.innerWidth,
  fonts: document.fonts.check('700 1em Montserrat') && document.fonts.check('400 1em Poppins'),
})
```

puis lire chaque capture. Attendu : `horizontalScroll` à `false`, polices prêtes, vignettes lisibles, textes contrastés, aucun élément qui en chevauche un autre sans le vouloir. Lire la console : aucune erreur.

- [ ] **Étape 7 : mesurer avec Lighthouse**

```bash
cd /c/wamp64/www/TECHNUM
npx --yes lighthouse http://127.0.0.1:8080/ --only-categories=performance,accessibility,best-practices,seo --form-factor=mobile --chrome-flags="--headless=new" --output=json --output-path=.captures/lighthouse.json
node -e "const r=require('./.captures/lighthouse.json'); for (const [k,v] of Object.entries(r.categories)) console.log(k, Math.round(v.score*100)); console.log('CLS', r.audits['cumulative-layout-shift'].numericValue.toFixed(3));"
```

Attendu : performance d'au moins 95, accessibilité, bonnes pratiques et référencement à 100, décalage de mise en page sous 0,05. Arrêter le serveur.

- [ ] **Étape 8 : committer**

```bash
cd /c/wamp64/www/TECHNUM
rm -rf .captures
git add public/assets/css/site.css tests/Unit/StylesheetTest.php
git commit -F - <<'EOF'
feat(accueil): joue une seule séquence au chargement, rien en mouvement réduit
EOF
```

---

### Tâche 9 : relecture, version 1.3 et mise en ligne

**Fichiers :** aucun nouveau.

**Interfaces :** aucune.

- [ ] **Étape 1 : vérification complète**

Lancer la vérification complète des conventions. Attendu : tout est vert.

- [ ] **Étape 2 : pull request vers develop**

```bash
cd /c/wamp64/www/TECHNUM
ISSUE=39
git push -u origin HEAD
gh pr create --base develop --title "[#$ISSUE] Refonte visuelle du site" --body-file - <<EOF
## Objectif

Rendre le site plus vivant et plus moderne, selon la conception validée le 3 octobre 2026. Issue liée : #$ISSUE

## Changements

- Spécification et plan de la refonte
- Accueil bleu de la marque, accolades décoratives, registre sur carte blanche
- Vignettes des produits qui chevauchent l'accueil et ouvrent l'onglet du produit
- Produits à onglets accessibles au clavier, empilés sans JavaScript
- Sections en couleurs alternées : réalisations en cartes, services sur charbon, méthode en bandeau bleu
- Contact : formulaire en carte, carte bleue de contact direct
- Une seule séquence animée au chargement, rien en mouvement réduit

## Tests

- [x] Tests de l'accueil, du contenu et de la feuille de style
- [x] Navigateur : 320, 390, 820, 1024 et 1440 px, clavier, sans JavaScript, mouvement réduit
- [x] Lighthouse mobile

## Checklist auteur

- [x] Code relu par moi-même
- [x] Pas de console.log
- [x] Pas de secret exposé
- [x] Taille : plus de 400 lignes, car la refonte touche toute la feuille de style de l'accueil ; chaque commit correspond à une partie testée
EOF
```

Attendre que chaque contrôle soit en succès, puis fusionner avec `gh pr merge --merge --delete-branch` et revenir sur `develop`.

- [ ] **Étape 3 : version 1.3 sur main**

```bash
cd /c/wamp64/www/TECHNUM
git checkout develop && git pull --ff-only
RELEASE="release/$(date +%Y-%m-%d)-v1.3.0"
git checkout -b "$RELEASE"
git push -u origin HEAD
ISSUE=$(gh issue create --title "Version 1.3 de bytechnum.com" --body "Passage de develop sur main : refonte visuelle du site." | sed 's#.*/##')
gh pr create --base main --title "[#$ISSUE] Version 1.3 de bytechnum.com" --body-file - <<EOF
## Objectif

Publier la version 1.3 du site sur main. Issue liée : #$ISSUE

## Changements

- Refonte visuelle du site

## Tests

- [x] Intégration continue verte sur develop

## Checklist auteur

- [x] Relu par moi-même
- [x] Pas de secret exposé
EOF
```

Attendre que chaque contrôle soit en succès, puis :

```bash
cd /c/wamp64/www/TECHNUM
gh pr merge --merge
git checkout main && git pull --ff-only
git tag -a v1.3.0 -F - <<'EOF'
Version 1.3 de bytechnum.com
EOF
git push origin v1.3.0
git checkout develop
```

- [ ] **Étape 4 : obtenir l'accord d'Elisée pour la mise en ligne**

Demander à Elisée s'il autorise la mise en ligne de la version 1.3, et attendre sa réponse.

- [ ] **Étape 5 : mettre en ligne et contrôler la production**

Sur son accord, avec la commande SSH enregistrée dans `$TECHNUM_OUTILS/ssh-commande` :

```bash
SSH="$(cat "$TECHNUM_OUTILS/ssh-commande")"
$SSH 'cd ~/apps/technum && bash deploy.sh && git log -1 --oneline && test -z "$(git status --porcelain)" && echo "code du serveur propre"'
```

Puis contrôler la production : accueil, pages légales et page inconnue répondent 200 ou 404 comme avant ; dans Chromium à 1440 × 900 et à 390 × 844, ouvrir `https://bytechnum.com/` puis `https://bytechnum.com/#produit-carte-uac`. Attendu : le nouveau design, l'onglet Carte UAC sélectionné, console sans erreur.
