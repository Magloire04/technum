# Page d'accueil de bytechnum.com : spécification de conception

- Date : 2 octobre 2026
- Statut : validée par Elisée le 2 octobre 2026, retours intégrés
- Dépôt local : `c:/wamp64/www/TECHNUM`

## 1. Objectif

Remplacer la page « Bientôt en ligne » de bytechnum.com par la page d'accueil de la marque
TECHNUM. La page dit ce que fait TECHNUM, montre les produits en service avec leur état,
liste les autres réalisations et amène le visiteur à décrire son projet.

Critère de réussite principal : des demandes de projet reçues par le formulaire, WhatsApp, le
téléphone ou l'e-mail.
Critère secondaire : des visites vers les produits.

## 2. Décisions déjà prises

| Sujet | Décision |
| --- | --- |
| Propriété | TECHNUM appartient entièrement à Elisée Magloire ATONDE |
| Voix | « Nous » |
| Conversion prioritaire | Les demandes de projet passent avant les inscriptions aux produits |
| Contenu exclu | Deux mandats clients confidentiels ne sont jamais cités. Leur liste reste hors du dépôt public |
| Coordonnées | E-mail professionnel, téléphone et WhatsApp au +229 01 50 61 73 00, GitHub, Porto-Novo |
| Produits stables | Oeil 360° Finance et Dis oui sont en version stable, sans chantier affiché |
| PROVIA | Inscriptions en chantier : la page n'invite pas à s'y inscrire |
| Mentions légales | TECHNUM n'a pas encore d'immatriculation |
| Dossier ai-learning | Supprimé du serveur le 2 octobre 2026, archive dans `~/backups`, jamais mentionné |
| Construction | Site PHP léger, sans framework |
| Exigence | Un rendu soigné, à la hauteur des produits présentés |

Le téléphone et WhatsApp partagent le même numéro. WhatsApp et le formulaire sont les deux
actions de contact, côte à côte. L'adresse Gmail personnelle n'apparaît pas sur un site de
marque.

## 3. Publics et parcours

Publics, par ordre de priorité :

1. Responsables de PME, d'institutions et d'associations au Bénin qui ont un besoin numérique.
2. Entrepreneurs et porteurs de projets qui veulent une première version de leur produit.
3. Utilisateurs des produits qui découvrent la marque.

Ils arrivent par la signature e-mail, les documents TECHNUM remis aux clients, les liens depuis
les produits et la recherche « TECHNUM » sur Google.

Parcours visé : la promesse, la preuve par les produits, les services reliés à ces preuves, la
méthode, puis le contact.

## 4. Structure de la page

Une seule page longue avec des ancres, trois pages légales et une page 404.

| Ordre | Section | Ancre | Rôle |
| --- | --- | --- | --- |
| 1 | En-tête | | Logo, navigation, bouton « Parler de votre projet » |
| 2 | Accueil | | Promesse, deux actions, registre des produits |
| 3 | Nos produits | `#produits` | Les quatre produits ouverts au public |
| 4 | Autres réalisations | `#realisations` | Preuves de concept, produit en pause, mandats clients |
| 5 | Ce que nous faisons pour vous | `#services` | Services reliés à leurs exemples |
| 6 | Comment se passe un projet | `#methode` | Étapes et engagements |
| 7 | Qui est derrière TECHNUM | `#a-propos` | Fondateur, lien vers le portfolio |
| 8 | Parlons de votre projet | `#contact` | Formulaire et e-mail direct |
| 9 | Pied de page | | Produits, pages légales, mention sans cookie |

Navigation : Produits, Réalisations, Services, Méthode, puis le bouton « Parler de votre
projet » qui mène à `#contact`.

moi.bytechnum.com n'est pas présenté comme un produit. Il apparaît dans la section « Qui est
derrière TECHNUM ».

## 5. Contenu validé

### 5.1 Accueil

- Titre : « Des solutions numériques conçues pour vos réalités. » C'est la phrase d'accueil
  proposée par le brand book.
- Texte : « TECHNUM conçoit, met en ligne et maintient des applications pour les entreprises,
  les institutions et les porteurs de projets du Bénin. Nos propres produits sont déjà en
  service : vous pouvez les essayer dès maintenant. »
- Action principale : « Parler de votre projet », vers `#contact`.
- Action secondaire : « Voir nos produits », vers `#produits`.
- Registre : titre « Nos produits aujourd'hui ». Une ligne par produit avec son icône (reprise
  de son propre site), son nom, sa piste d'état et le libellé de l'état. Sous la liste :
  « Mis à jour le 2 octobre 2026 ». Chaque ligne mène au bloc du produit.

### 5.2 Nos produits

Introduction : « Quatre outils conçus, hébergés et maintenus par TECHNUM, ouverts au public. »

Chaque bloc contient le nom, l'accroche, le public, une capture réelle avec l'adresse du produit
en légende, la piste d'état, « Déjà en place », « En cours » et le lien « Ouvrir » vers le
produit.

**Oeil 360° Finance**, <https://oeil360finance.bytechnum.com>

- Accroche : « Suivre ses revenus, ses dépenses et ses comptes en franc CFA, au même endroit. »
- Pour qui : particuliers et indépendants qui gèrent leur argent entre caisse, Mobile Money et
  banque.
- État : En service.
- Déjà en place : comptes multiples, transferts entre comptes, charges récurrentes, tableau de
  bord par période, connexion sécurisée, données traitées selon la loi n°2017-20.
- En cours : aucun chantier. Le bloc affiche « Version stable ».

**Dis oui**, <https://disoui.bytechnum.com>

- Accroche : « Transformer une demande de rendez-vous en petit jeu, avec la réponse par e-mail
  et le rendez-vous prêt pour le calendrier. »
- Pour qui : tout le monde, sans compte ni mot de passe.
- État : En service.
- Déjà en place : éditeur en six étapes, sept thèmes dont un thème TECHNUM pour les invitations
  professionnelles, partage par lien ou QR code, fichier calendrier, suppression automatique à
  l'échéance.
- En cours : aucun chantier. Le bloc affiche « Version stable ».

**PROVIA**, <https://provia.bytechnum.com>

- Accroche : « Mettre en relation les étudiants béninois et les entreprises qui cherchent des
  stagiaires. »
- Pour qui : étudiants, recruteurs et établissements.
- État : Bêta.
- Déjà en place : profils étudiants et recruteurs, publication et consultation des offres,
  candidature en ligne.
- En cours : ouverture complète des inscriptions, espace étudiant complet, espace recruteur,
  suivi par les établissements, puis calcul de compatibilité entre profils et offres.
- Le bloc n'invite pas à s'inscrire tant que les inscriptions sont en chantier.

**Carte UAC**, <https://uacmap.bytechnum.com>

- Accroche : « Trouver son chemin à pied sur le campus d'Abomey-Calavi, jusqu'à la bonne porte,
  même sans réseau. »
- Pour qui : étudiants, parents et visiteurs du campus.
- État : Pilote.
- Déjà en place : recherche par sigle ou surnom, itinéraire à pied avec guidage GPS, QR codes
  « Vous êtes ici », fonctionnement hors ligne, contributions relues avant publication.
- En cours : relevé des lieux du campus, ouverture des contributions au public.
- Mention : « Les positions affichées aujourd'hui sont des données de démonstration. »

### 5.3 Autres réalisations

Introduction : « Des preuves de concept, un produit en pause et des mandats clients. »
Les descriptions reprennent celles du profil GitHub d'Elisée.

| Réalisation | Ce que ça fait | Nature | Lien |
| --- | --- | --- | --- |
| TraçaCajou | Certificats d'origine numériques pour la filière anacarde, signés et vérifiables par QR code | Preuve de concept | Dépôt GitHub |
| Après mon bac | Estimation des chances de bourse et aide à l'orientation des bacheliers béninois, sur 224 filières publiques | Produit en pause | Dépôt GitHub |
| Identité numérique pour le CDPI | Preuve de concept d'identité numérique décentralisée avec la suite MOSIP Inji | Preuve de concept | Dépôt GitHub |
| CYPASS | Plateforme de cybersécurité pour les PME africaines, co-fondée par Elisée, qui en dirige la technique | Entreprise co-fondée | <https://cypass.netlify.app> |
| BESCAT Côte d'Ivoire | Refonte du site web | Mandat client | À préciser |
| e-pensionbj | Traitement automatisé de paiements de pensions, basé sur Mojaloop | Mandat client | Aucun, dépôt privé |

### 5.4 Ce que nous faisons pour vous

Introduction : « Chaque service renvoie à un exemple que vous pouvez ouvrir. »

| Service | Description | Exemples |
| --- | --- | --- |
| Applications de gestion | Des outils adaptés à votre façon de travailler : suivi d'activité, tableaux de bord, comptes utilisateurs, exports | Oeil 360° Finance |
| Plateformes en ligne | Des services ouverts à plusieurs publics, avec des rôles, des validations et un espace d'administration | PROVIA, Carte UAC |
| Sites web et produits numériques | Sites vitrines, refontes et premières versions de produits, pensés pour le téléphone et les connexions lentes | Dis oui, BESCAT Côte d'Ivoire |
| Sécurité et conformité | Protection des données selon la loi n°2017-20, signatures électroniques, certificats vérifiables par QR code, identité numérique | TraçaCajou, identité numérique pour le CDPI |
| Hébergement et suivi | Mise en ligne, sauvegardes, corrections et évolutions après la livraison | Nos quatre produits, que nous maintenons |

Les services s'affichent en liste de texte sur deux colonnes, sans icônes.

### 5.5 Comment se passe un projet

Les étapes forment une vraie séquence : elles sont numérotées.

1. Nous écoutons le besoin réel : qui utilisera l'outil, pour faire quoi, sur quels appareils
   et avec quelle connexion.
2. Nous cadrons par écrit : une note de cadrage et des spécifications, validées avec vous avant
   le développement.
3. Nous construisons par étapes : une version en ligne à tester à chaque étape.
4. Nous mettons en ligne : hébergement, nom de domaine, sauvegardes et conformité.
5. Nous vous accompagnons : guide d'utilisation, corrections et évolutions après la livraison.

Engagements, en texte court sous les étapes : « Vos données personnelles sont traitées selon la
loi n°2017-20. Chaque modification du code passe par des tests automatiques. La documentation
vous est remise à la livraison. » Ces engagements sont vrais pour les projets actuels. Aucun
engagement ne s'ajoute sans preuve.

### 5.6 Qui est derrière TECHNUM

« TECHNUM est basée à Porto-Novo. Elle a été fondée par Elisée Magloire ATONDE, développeur
logiciel et DevSecOps, spécialisé dans la confiance numérique : signatures électroniques,
certificats vérifiables et protection des données. »

Lien : « Voir le parcours du fondateur », vers <https://moi.bytechnum.com>.

Pas de photo en v1, faute de photo professionnelle fournie.

### 5.7 Parlons de votre projet

Introduction : « Décrivez votre besoin en quelques lignes. Nous vous répondons par e-mail. »

| Champ | Obligatoire | Règle |
| --- | --- | --- |
| Nom | Oui | 2 à 100 caractères |
| Organisation | Non | 120 caractères au plus |
| E-mail | Oui | Adresse valide, 254 caractères au plus |
| Votre besoin | Oui | Application de gestion, Plateforme en ligne, Site web ou produit numérique, Sécurité et conformité, Hébergement et suivi, Autre |
| Message | Oui | 20 à 3 000 caractères |
| Accord | Oui | Case non cochée : « J'accepte que TECHNUM utilise ces informations pour répondre à ma demande. », avec un lien vers la politique de confidentialité |

Bouton : « Envoyer la demande ».

À côté du formulaire : « Vous préférez un échange direct ? », puis un bouton « Écrire sur
WhatsApp », le téléphone, l'e-mail, le profil GitHub et « Porto-Novo, Bénin ».

- WhatsApp : `https://wa.me/2290150617300`, avec le message prérempli « Bonjour TECHNUM, je
  souhaite vous parler d'un projet. »
- Téléphone : affiché « +229 01 50 61 73 00 », lien `tel:+2290150617300`.
- E-mail : elisee.atonde@bytechnum.com, lien `mailto:`.

| Situation | Message affiché |
| --- | --- |
| Succès | « Demande envoyée. Nous vous répondons à l'adresse indiquée. » |
| Champ invalide | Un message précis sous le champ, par exemple « Indiquez votre nom. » ou « Cette adresse e-mail n'est pas valide. » |
| Trop d'envois | « Trop de demandes depuis cette connexion. Réessayez dans une heure, ou écrivez-nous sur WhatsApp au +229 01 50 61 73 00. » |
| Échec d'envoi | « L'envoi n'a pas abouti. Écrivez-nous sur WhatsApp au +229 01 50 61 73 00 ou à elisee.atonde@bytechnum.com. » |

### 5.8 Pied de page et pages légales

Pied de page sur fond Charcoal : logo en version claire avec sa signature, liens vers les
produits, liens vers les pages légales, « Ce site ne dépose aucun cookie. » et « © 2026 TECHNUM ».

- `/mentions-legales` : éditeur (Elisée Magloire ATONDE, qui exploite la marque TECHNUM,
  Porto-Novo, e-mail, téléphone), hébergeur (Spaceship, serveur situé à Amsterdam). Aucun
  numéro d'immatriculation pour l'instant : il s'ajoutera quand il existera.
- `/confidentialite` : données du formulaire, finalité (répondre à la demande), base légale
  (accord de la personne), destinataire (boîte e-mail TECHNUM chez Spacemail), durée de
  conservation, transfert hors du Bénin, droits d'accès, de rectification et d'effacement,
  contact, recours auprès de l'APDP, absence de cookie.
- `/cgu` : objet du site, renvoi vers les conditions propres à chaque produit, propriété
  intellectuelle, responsabilité.

## 6. Direction visuelle

### 6.1 Principe

La preuve avant la promesse : le visiteur voit des produits réels avant de lire une liste de
services. Le reste de la page reste sobre, comme le demande le brand book : « une impression de
clarté avant une impression de sophistication ».

### 6.2 Élément signature

Le registre des produits, dans l'accueil. Chaque produit y a une piste d'état à quatre étapes :
Conception, Pilote, Bêta, En service. La piste reprend le motif « Flow » du brand book, une
ligne bleue.

Au chargement, une seule animation remplit chaque piste jusqu'à l'étape du produit. Elle rappelle
les bits qui « prenaient leur place » sur l'ancienne page. Elle dure moins d'une seconde et
disparaît quand l'utilisateur demande moins d'animations. La même piste réapparaît, fixe, dans
chaque bloc produit.

### 6.3 Couleurs

Palette officielle du brand book, sans ajout.

| Jeton | Valeur | Usage |
| --- | --- | --- |
| `charcoal` | `#373536` | Texte, logo, pied de page |
| `charcoal-soft` | `#5E5C5D` | Texte secondaire, contraste de 6,6:1 sur blanc |
| `blue` | `#405FE0` | Actions, liens, pistes d'état, focus |
| `blue-dark` | `#2846B9` | Survol et état actif |
| `ice` | `#E9EDFF` | Fond du registre, surlignage |
| `off-white` | `#F7F8FC` | Fond de la section contact |
| `white` | `#FFFFFF` | Fond de page, cadres |
| `line` | `#E5E7ED` | Bordures, filet vertical |

Les états des produits ne prennent pas de nouvelle couleur : la piste et le libellé suffisent.
Le blanc sur `blue` atteint 5,3:1, conforme au niveau AA.

### 6.4 Typographie

- Montserrat 600 et 700 pour les titres, la navigation et les boutons. C'est la police du logo.
- Poppins 400 et 500 pour le texte courant, les formulaires et les légendes.
- Fichiers woff2 servis par le site lui-même, sans Google Fonts : rien ne part chez un tiers.
- Échelle de ratio 1,25 sur une base de 17 px : légende 14 px, texte 17 px, titre 3 21 px,
  titre 2 33 px, titre 1 de 40 px sur mobile à 52 px sur grand écran.
- Interligne 1,6 pour le texte et 1,1 pour le titre 1. Lignes de texte d'environ 66 caractères
  au plus.
- Casse normale partout, aucune étiquette en capitales. Seule la signature du logo reste en
  capitales, puisqu'elle fait partie du logo.

### 6.5 Mise en page

- Grille de 12 colonnes, largeur utile de 1 200 px au plus, marges de 16 px sur mobile.
- À partir de la section produits, une colonne de repère à gauche, séparée du contenu par un
  filet vertical fin. Le filet reprend le trait vertical du logo, entre l'accolade et le mot
  TECHNUM. La colonne porte le nom de la section et une information de contexte. Sur mobile,
  le repère passe au-dessus du contenu.
- Texte aligné à gauche partout.
- Les quatre produits suivent le même gabarit, sans alternance : capture à gauche et texte à
  droite sur grand écran, capture au-dessus sur mobile.

Grand écran :

```text
+------------------------------------------------------------------------------+
| [logo]        Produits  Réalisations  Services  Méthode  [Parler de votre projet] |
+------------------------------------------------------------------------------+
|                                                                              |
|  Des solutions numériques            +------------------------------------+  |
|  conçues pour vos réalités.          | Nos produits aujourd'hui           |  |
|                                      | Oeil 360° Finance  o--o--o--@  En service |
|  TECHNUM conçoit, met en ligne et    | Dis oui            o--o--o--@  En service |
|  maintient des applications...       | PROVIA             o--o--@--.  Bêta         |
|                                      | Carte UAC          o--@--.--.  Pilote |
|  [Parler de votre projet]            | Mis à jour le 2 octobre 2026       |  |
|  Voir nos produits                   +------------------------------------+  |
+------------------------------------------------------------------------------+
| Nos produits    |  +--------------------------+   Oeil 360° Finance           |
| Quatre outils   |  |                          |   Accroche                    |
| ouverts au      |  |      capture réelle      |   Pour qui                    |
| public          |  |                          |   o--o--o--@  En service      |
|                 |  +--------------------------+   Déjà en place, En cours     |
|                 |  oeil360finance.bytechnum.com   Ouvrir Oeil 360° Finance    |
|                 |  (même gabarit pour les trois autres produits)               |
+-----------------+------------------------------------------------------------+
| Autres          |  Nom, ce que ça fait, nature, lien                           |
| réalisations    |                                                              |
+-----------------+------------------------------------------------------------+
| Services        |  Service et description      |  Service et description       |
|                 |  Exemple : produit           |  Exemples : produits          |
+-----------------+------------------------------------------------------------+
| Méthode         |  1 Écouter  2 Cadrer  3 Construire  4 Mettre en ligne  5 Accompagner |
+-----------------+------------------------------------------------------------+
| À propos        |  Texte, lien vers le parcours du fondateur                   |
+-----------------+------------------------------------------------------------+
| Contact         |  Formulaire                     |  WhatsApp, téléphone, e-mail |
+------------------------------------------------------------------------------+
| Pied de page Charcoal : logo clair, produits, pages légales, sans cookie       |
+------------------------------------------------------------------------------+
```

Téléphone :

```text
+--------------------------+
| [logo]            [Menu] |
+--------------------------+
| Des solutions numériques |
| conçues pour vos         |
| réalités.                |
| Texte                    |
| [Parler de votre projet] |
| Voir nos produits        |
| +----------------------+ |
| | Nos produits         | |
| | Oeil 360  En service | |
| | Dis oui   En service | |
| | PROVIA    Bêta       | |
| | Carte UAC Pilote     | |
| +----------------------+ |
+--------------------------+
| Nos produits             |
| [capture]                |
| Oeil 360° Finance        |
| Accroche, piste, lien    |
+--------------------------+
```

### 6.6 Composants

Repris de la direction d'interface du brand book.

- Bouton principal : fond `blue`, texte blanc, rayon de 8 px, `blue-dark` au survol.
- Bouton secondaire : fond blanc, bordure `line`, texte `charcoal`.
- Lien : `blue`, souligné au survol et au focus.
- Cadre de capture : bordure `line`, rayon de 12 px, sans ombre, sans faux boutons de fenêtre.
  L'adresse du produit s'affiche en légende sous le cadre.
- Champ : fond blanc, bordure `line`, contour `blue` de 2 px au focus.
- Focus clavier visible sur tous les éléments interactifs.

### 6.7 Images et logo

- Captures réelles de chaque produit, en WebP sur deux tailles, avec dimensions déclarées pour
  éviter les sauts d'affichage. Chaque produit montre la vue qui le sert le mieux : téléphone
  pour Dis oui et Carte UAC, ordinateur pour Oeil 360° Finance et PROVIA.
- Aucune photo de banque d'images, aucune illustration générée.
- Le SVG fourni dans `documentations/` est le logo complet en noir, sur un carré avec beaucoup
  de marge. On en tire quatre fichiers recadrés : logo complet aux couleurs officielles, version
  claire pour fond Charcoal, version sans signature pour l'en-tête, favicon tiré de l'accolade.
  Les couleurs suivent le logo PNG officiel, comparé côte à côte. Proportions et composition
  restent identiques.
- Image de partage de 1 200 × 630 px avec le logo et la phrase d'accueil.

### 6.8 Mouvement

- Une seule animation non déclenchée par l'utilisateur : le remplissage des pistes du registre.
- Pas d'apparition au défilement, pas de zoom au survol, pas de compteur animé.
- Les réponses aux actions restent : ouverture du menu mobile, message après l'envoi.

### 6.9 Exclusions

Fond sombre avec néons, dégradé violet, bleu et rose, blobs flous, verre dépoli, grille
« bento », badge « Nouveau » avec étincelle, bloc de six fonctionnalités à icônes, grille
tarifaire en trois colonnes, FAQ en accordéon, ombres identiques sous chaque bloc, chiffres
inventés, émojis dans les titres, tiret cadratin dans les textes, faux témoignages, polices
Inter, Space Grotesk et Geist.

### 6.10 Accessibilité

Niveau AA des WCAG 2.1 : contrastes, lien d'évitement vers le contenu, repères `header`,
`main` et `footer`, textes alternatifs sur les captures, erreurs de formulaire reliées à leur
champ, navigation complète au clavier, menu mobile utilisable au clavier et au lecteur d'écran.

## 7. Architecture technique

### 7.1 Principe

PHP 8.4 sans framework : un seul point d'entrée, des gabarits PHP, le contenu dans des fichiers
PHP versionnés. Aucune base de données. Aucune étape de construction pour le front : une feuille
CSS et un petit fichier JavaScript écrits à la main. La page fonctionne entièrement sans
JavaScript. Le script ajoute seulement le menu mobile et l'aide à la saisie.

Dépendances Composer : `phpmailer/phpmailer` pour l'envoi SMTP et `vlucas/phpdotenv` pour lire
`.env`. En développement : PHPUnit, PHPStan et PHP CS Fixer.

### 7.2 Arborescence

```text
TECHNUM/
├── public/                  racine web servie
│   ├── index.php            point d'entrée unique
│   ├── .htaccess            HTTPS, www vers bytechnum.com, réécriture, en-têtes, cache
│   ├── robots.txt
│   ├── sitemap.xml
│   └── assets/
│       ├── css/site.css
│       ├── js/site.js
│       ├── fonts/           Montserrat 600 et 700, Poppins 400 et 500
│       └── img/             logos, favicon, image de partage, captures, icônes des produits
├── src/
│   ├── Http/                Router, Request, Response
│   ├── Controller/          HomeController, LegalController, ContactController
│   ├── Contact/             ContactRequest, FormToken, RateLimiter, MailerInterface, SmtpMailer
│   ├── Content/             ContentRepository, ProductStage
│   └── View/                View et la fonction d'échappement e()
├── content/
│   ├── site.php             e-mail, téléphone, WhatsApp, GitHub, ville, date de mise à jour
│   ├── products.php
│   ├── projects.php
│   └── services.php
├── templates/
│   ├── layout.php
│   ├── home.php
│   ├── partials/            en-tête, registre, bloc produit, formulaire, pied de page
│   ├── legal/               mentions-legales.php, confidentialite.php, cgu.php
│   └── errors/404.php
├── storage/rate-limit/      seul dossier écrit par l'application, ignoré par Git
├── tests/                   Unit/ et Integration/
├── docs/                    spécification et plan de réalisation
├── composer.json, phpunit.xml.dist, phpstan.neon, .php-cs-fixer.dist.php
├── package.json             ESLint et Prettier, en développement seulement
├── .env.example
├── deploy.sh
├── CONTRIBUTING.md
└── README.md
```

### 7.3 Routes

| Méthode | Chemin | Réponse |
| --- | --- | --- |
| GET | `/` | Page d'accueil |
| POST | `/contact` | 303 vers `/?envoi=ok#contact` en cas de succès, sinon la page avec les erreurs (422, 429 ou 503) |
| GET | `/mentions-legales` | Page légale |
| GET | `/confidentialite` | Page légale |
| GET | `/cgu` | Page légale |
| Autre | | Page 404 |

Le `.htaccess` force HTTPS, redirige `www.bytechnum.com` vers `bytechnum.com` en 301, envoie
vers `index.php` toute requête qui ne vise pas un fichier existant et pose les en-têtes de cache
des fichiers statiques.

### 7.4 Contenu

- Les quatre fichiers de `content/` renvoient des tableaux PHP, lus par `ContentRepository`.
- Les états viennent de l'énumération `ProductStage` : `conception`, `pilote`, `beta`,
  `en-service`. Chacun porte son libellé affiché (Conception, Pilote, Bêta, En service) et sa
  position sur la piste.
- Un test contrôle chaque fichier : champs obligatoires présents, adresses en https, image
  existante avec texte alternatif, état valide, aucun des mots confidentiels, contrôlés par empreintes.

### 7.5 Formulaire de contact

Le formulaire fonctionne sans cookie ni session.

1. La page génère un jeton signé par HMAC SHA-256 avec `APP_SECRET`, qui contient l'heure
   d'affichage.
2. À l'envoi, le serveur vérifie la signature, refuse un jeton de plus de deux heures et refuse
   un envoi fait moins de trois secondes après l'affichage.
3. Un champ caché piège les robots. S'il est rempli, le serveur répond comme pour un succès,
   sans rien envoyer.
4. Cinq demandes valides par heure au plus pour une même connexion. Un envoi refusé par la
   validation ne compte pas. La connexion est reconnue par une empreinte HMAC de l'adresse IP,
   jamais par l'IP en clair. Les empreintes vivent dans
   `storage/rate-limit` et s'effacent après une heure.
5. Chaque champ est validé selon le tableau de la section 5.7.
6. L'envoi passe par le SMTP de Spacemail, port 465 en SSL, vers `CONTACT_RECIPIENT_EMAIL`, avec
   l'adresse du visiteur en Reply-To. L'expéditeur est une boîte bytechnum.com authentifiée.
7. Succès : redirection 303 vers `/?envoi=ok#contact`, qui affiche la confirmation.
8. Erreur : la page s'affiche de nouveau avec les valeurs saisies et un message sous chaque champ
   en cause.

Aucun accusé de réception n'est envoyé au visiteur : il permettrait d'écrire à une adresse tierce
depuis notre formulaire. Aucun message n'est stocké sur le serveur : la boîte e-mail est le seul
lieu de conservation. En cas d'échec SMTP, le journal note l'heure et le type d'erreur, sans nom,
sans e-mail et sans contenu.

Variables d'environnement, décrites dans `.env.example` : `APP_ENV`, `APP_SECRET`, `SMTP_HOST`,
`SMTP_PORT`, `SMTP_USERNAME`, `SMTP_PASSWORD`, `CONTACT_SENDER_EMAIL`,
`CONTACT_RECIPIENT_EMAIL`. Le fichier `.env` réel n'est jamais commité.

### 7.6 Sécurité

- Toute valeur affichée passe par `e()`, qui appelle `htmlspecialchars`. Aucun HTML brut issu
  d'une saisie.
- En-têtes : Content-Security-Policy stricte (`default-src 'self'`, aucun script ni style en
  ligne, `form-action 'self'`, `frame-ancestors 'none'`), Strict-Transport-Security,
  X-Content-Type-Options, Referrer-Policy et Permissions-Policy.
- Aucun cookie et aucune ressource tierce : pas de CDN, pas de police distante, pas de traceur.
- Secrets uniquement dans `.env` sur le serveur. Erreurs génériques en production, sans trace
  technique.
- Le journal note les refus (jeton invalide, limite atteinte) sans donnée personnelle.

### 7.7 Performance

- Objectif Lighthouse sur mobile : au moins 95 sur les quatre axes.
- CSS sous 30 Ko, JavaScript sous 10 Ko, quatre fichiers de police au plus.
- Captures situées sous le premier écran chargées à la demande.
- `font-display: swap` : le titre s'affiche tout de suite avec la police de secours.

### 7.8 Référencement et mesure

- Titre : « TECHNUM, solutions numériques au Bénin ».
- Description : « TECHNUM conçoit et maintient des applications, des plateformes et des sites
  pour les entreprises et les institutions du Bénin. Découvrez nos produits en service. »
- Langue `fr`, URL canonique `https://bytechnum.com/`, balises Open Graph avec l'image de
  partage.
- Données structurées JSON-LD de type `Organization` : nom, logo, e-mail, téléphone, ville,
  profil GitHub.
- `robots.txt` et `sitemap.xml`.
- Mesure sans traceur : Google Search Console et Bing Webmaster Tools après la mise en ligne,
  statistiques de l'hébergement, demandes reçues. Les liens vers les produits portent
  `?ref=bytechnum`, visible dans les journaux d'accès de chaque produit.

## 8. Tests et qualité

- Tests unitaires PHPUnit : validation de chaque champ (cas valide, limites, erreurs), jeton
  signé (valide, expiré, trop rapide, falsifié), limiteur d'envois (accepte, bloque, purge),
  échappement, énumération des états, contrôle des fichiers de contenu.
- Tests d'intégration du routeur et du contrôleur de contact avec un faux expéditeur : succès
  303, erreurs 422, piège à robots, limite 429, échec SMTP 503, page 404.
- Outils : PHP CS Fixer selon PSR-12 pour la mise en forme, PHPStan niveau 8 pour l'analyse,
  ESLint et Prettier pour le JavaScript et le CSS, gitleaks contre les fuites de secrets.
- Intégration continue GitHub Actions sur chaque pull request, sans filtre de chemins, pour que
  le workflow tourne aussi quand on le modifie.
- Vérification visuelle réelle avant chaque mise en ligne : captures sur ordinateur et sur
  téléphone, navigation au clavier, envoi d'un vrai message de test. Les tests automatiques ne
  voient pas les défauts d'affichage.

## 9. Déploiement

| Élément | Choix |
| --- | --- |
| Code | `~/apps/technum`, cloné depuis GitHub avec une clé de déploiement en lecture seule et l'alias SSH `github.com-technum` |
| Racine web | `~/bytechnum.com` devient un lien symbolique vers `~/apps/technum/public`, comme pour oeil360finance et provia |
| Secrets | `~/apps/technum/.env`, créé sur le serveur |
| Écriture | `~/apps/technum/storage`, seul dossier accessible en écriture |
| Script | `deploy.sh` : `git pull origin main`, `composer install --no-dev --optimize-autoloader`, contrôle de syntaxe |

Première mise en ligne :

1. Archiver `~/bytechnum.com`, qui contient la page « Bientôt en ligne », dans `~/backups`.
2. Cloner le dépôt, installer les dépendances, créer `.env`.
3. Remplacer le dossier `~/bytechnum.com` par le lien symbolique. Le serveur peut mettre quelques
   minutes à servir une racine recréée.
4. Vérifier HTTPS, la redirection de www, les en-têtes, le rendu sur ordinateur et sur
   téléphone, et un envoi réel du formulaire.
5. Lancer l'audit d'après mise en ligne : indexation, Search Console, Bing, vitesse, sécurité.

Retour arrière : supprimer le lien et restaurer l'archive de la page « Bientôt en ligne ».

Chaque mise en ligne se fait depuis `main`, uniquement à la demande explicite d'Elisée.

## 10. Hors périmètre de la v1

- Version anglaise.
- Mode sombre.
- Journal des mises à jour et blog.
- Espace d'administration.
- Offre de formation.
- Vérification en direct de la disponibilité des produits.
- Lien « Un produit TECHNUM » dans le pied de page de chaque produit : à faire dans chaque dépôt
  produit, avec sa propre pull request, après la mise en ligne de bytechnum.com.
- Lien vers bytechnum.com depuis moi.bytechnum.com.

## 11. Points tranchés le 2 octobre 2026

| Point | Décision |
| --- | --- |
| Textes de la section 5 | Validés, retours intégrés |
| Oeil 360° Finance et Dis oui | Version stable, aucun chantier affiché |
| Crédit sur PROVIA | Aucun |
| Lien entre la Carte UAC et l'université | Non mentionné |
| Confirmation e-mail de PROVIA | En chantier : la page n'invite pas à s'inscrire |
| Immatriculation de TECHNUM | Aucune pour l'instant |
| Lien BESCAT Côte d'Ivoire | Aucun lien |
| Téléphone et WhatsApp | +229 01 50 61 73 00 |
| Boîte du formulaire | elisee.atonde@bytechnum.com comme expéditeur et destinataire, mot de passe saisi par Elisée dans `.env` |
| Conservation des demandes | 12 mois après le dernier échange |
| Dépôt GitHub | `Magloire04/technum`, public comme le portfolio |
| Formation | Absente de la v1 |

## 12. Conventions du dépôt

- Branches : `main` pour la production, `develop` pour l'intégration, branches
  `type/TECHNUM-{numéro d'issue}-{description}` créées depuis `develop`. Les issues GitHub
  tiennent lieu de tickets.
- Commits au format Conventional Commits, en minuscules, 72 caractères au plus.
- Pull requests vers `develop` avec le modèle objectif, changements, tests et checklist. Seul
  contributeur pour l'instant : relecture puis fusion par Elisée, écart documenté dans
  `CONTRIBUTING.md`.
- Nommage : classes en PascalCase, méthodes en camelCase sous la forme verbe et nom, constantes
  en CONSTANT_CASE, variables d'environnement en SCREAMING_SNAKE_CASE, dossiers et gabarits en
  kebab-case. Code en anglais, contenu et adresses publiques en français.
- Écart de démarrage : les premiers commits (`.gitignore`, `README.md` et cette spécification)
  précèdent la création du dépôt GitHub et de ses issues. Ils n'ont donc pas de numéro de
  ticket.
