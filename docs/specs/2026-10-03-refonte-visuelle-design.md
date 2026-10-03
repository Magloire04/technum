# Refonte visuelle de bytechnum.com

Date : 3 octobre 2026. Conception validée par Elisée le 3 octobre 2026 : direction A, « Couleur de marque », complétée par la section « Nos produits » à onglets et la section « Comment se passe un projet » de la direction B.

## 1. Objectif

Elisée trouve la version en ligne, la 1.2, trop blanche, trop sage dans sa mise en page et trop statique. La refonte doit rendre le site plus vivant, plus moderne dans sa structure et sa présentation, professionnel et attirant.

Elle garde tout ce qui fonctionne : le contenu validé, la vitesse, l'accessibilité, la sécurité et l'absence de toute ressource externe. La demande de projet reste l'action principale de la page.

Critères de réussite :

1. Elisée valide le rendu en production, sur ordinateur et sur téléphone.
2. Lighthouse mobile donne au moins 95 en performance et 100 en accessibilité, bonnes pratiques et référencement.
3. Tous les tests passent, et chaque texte respecte un contraste d'au moins 4,5.

## 2. Ce qui ne change pas

- Les textes, les produits, les réalisations, les services, la méthode, les engagements et les coordonnées. Seule exception : un résumé court par produit pour les vignettes, décrit en 5.3.
- L'ordre des sections de l'accueil.
- La mise en page des pages légales et de la page 404. Elles reçoivent seulement le nouvel en-tête et le pied de page communs.
- Le fonctionnement du formulaire de contact.
- La sécurité : politique de contenu stricte, aucun script ni style en ligne, aucun attribut `style`, aucun cookie, aucune ressource externe.
- Les polices et les couleurs de la charte.

## 3. Charte appliquée

### 3.1 Couleurs par zone

| Zone | Fond | Texte |
| --- | --- | --- |
| En-tête | Blanc | Charbon, bouton bleu |
| Accueil | Bleu foncé `#2846B9` | Blanc, accroche `#DDE4FF` |
| Vignettes des produits | Blanc, capture sur bleu clair | Charbon |
| Nos produits | Blanc | Charbon |
| Autres réalisations | Bleu clair `#E9EDFF`, cartes blanches | Charbon |
| Services | Charbon `#373536` | Blanc, descriptions `#DCDADB` |
| Méthode | Bleu foncé `#2846B9` | Blanc, textes `#DCE3FF` |
| Contact | Blanc, carte directe bleue `#405FE0` | Charbon, blanc dans la carte |
| Pied de page | Charbon | Inchangé |

### 3.2 Contrastes mesurés

| Couple | Rapport |
| --- | --- |
| Blanc sur bleu foncé | 7,87 |
| `#DDE4FF` sur bleu foncé | 6,23 |
| Blanc sur bleu | 5,34 |
| Blanc sur charbon | 12,17 |
| `#D3D1D2` sur charbon | 8,01 |
| Bleu foncé sur bleu clair | 6,76 |
| Gris `#5E5C5D` sur bleu clair | 5,69 |

Tout texte posé sur le bleu `#405FE0` est blanc : un bleu très clair comme `#E2E8FF` n'y atteint que 4,38. Les accolades décoratives de l'accueil, à 1,47, ne portent aucun texte.

### 3.3 Typographie

- Titre de l'accueil : Montserrat 700, 62 px sur ordinateur, 40 px sur téléphone.
- Titres de section : Montserrat 700, 40 px sur ordinateur, 30 px sur téléphone.
- Corps : Poppins 400 et 500, de 15 à 19 px selon le rôle.

### 3.4 Logo

Le logo principal reste sur la barre blanche. La version claire ne sert que sur le charbon du pied de page. Aucun logo n'est posé sur un fond bleu : le « NUM » bleu y deviendrait illisible.

## 4. Structure de l'accueil sur ordinateur

### 4.1 En-tête

Barre blanche fixe en haut de l'écran, comme aujourd'hui : logo, menu (Produits, Réalisations, Services, Méthode) et bouton bleu « Parler de votre projet ».

### 4.2 Accueil

- Fond bleu foncé sur toute la largeur.
- Deux grandes accolades « { » et « } » en Montserrat 700, couleur bleue `#405FE0`, placées derrière le registre des produits. Elles sont décoratives et masquées aux lecteurs d'écran.
- À gauche : le titre, l'accroche et deux actions, un bouton blanc « Parler de votre projet » et un lien souligné « Voir nos produits ».
- À droite : le registre des produits actuel sur une carte blanche, avec les icônes, les pistes à quatre segments, les états et la date de mise à jour.

### 4.3 Vignettes des produits

Quatre vignettes blanches chevauchent le bas de l'accueil d'environ 100 px. Chacune montre :

- la capture du produit, sur fond bleu clair, cadrée en haut ;
- le nom et une pastille d'état : bleue pour « En service », bleu clair pour « Bêta », claire et bordée pour « Pilote » ;
- le résumé court du produit.

Chaque vignette renvoie à l'onglet de son produit, par l'ancre `#produit-…`. La vignette de PROVIA ne renvoie qu'à son onglet, jamais au produit lui-même.

### 4.4 Nos produits

- Le titre et la phrase de contexte actuels.
- Une liste de quatre onglets : icône, nom et état.
- Un panneau par produit :
  - la capture dans un cadre, sur fond bleu clair ;
  - le nom, l'accroche et le public ;
  - la piste des états, en quatre étapes nommées ;
  - les faits « Déjà en place » et « En cours » ;
  - la note éventuelle ;
  - le bouton « Ouvrir … » seulement si le produit est public, donc rien pour PROVIA.

### 4.5 Autres réalisations

Fond bleu clair. Les réalisations s'affichent en cartes blanches sur deux colonnes : nom, pastille de nature, description et lien éventuel.

### 4.6 Services

Fond charbon. Le titre et la phrase de contexte sont à gauche. Les cinq services s'affichent à droite sur deux colonnes, chacun sous un filet bleu. Les exemples deviennent des pastilles cliquables vers leurs ancres.

### 4.7 Méthode

Fond bleu foncé. Les cinq étapes sont numérotées et reliées par un trait horizontal. Les engagements actuels suivent, sous un filet clair.

### 4.8 Contact

Fond blanc. À gauche : le titre, l'accroche, puis une carte bleue « Vous préférez un échange direct ? ». Elle contient le bouton blanc « Écrire sur WhatsApp », le téléphone, l'e-mail, GitHub et l'adresse. À droite : le formulaire actuel, dans une carte bordée.

### 4.9 Pied de page

Inchangé.

## 5. Détails de réalisation

### 5.1 Onglets des produits

- Le serveur rend toujours les quatre panneaux, dans l'ordre du contenu. Il rend aussi la liste des onglets, avec l'attribut `hidden`.
- Sans JavaScript, la liste reste masquée et les quatre panneaux s'affichent les uns sous les autres, comme aujourd'hui.
- Avec JavaScript, le script affiche la liste et masque les panneaux non choisis. Il applique le modèle d'onglets accessible :
  - rôles `tablist`, `tab` et `tabpanel` ;
  - `aria-selected`, `aria-controls` et `aria-labelledby` ;
  - un seul onglet atteignable par la touche Tab ;
  - flèches gauche et droite, Début et Fin ;
  - activation au clic et au clavier.
- Le premier produit est affiché par défaut.
- Une ancre `#produit-…` sélectionne l'onglet correspondant et fait défiler jusqu'à la section, au chargement comme au clic. Ces ancres viennent des vignettes, du registre, des exemples de services ou d'un lien externe.
- Les ancres actuelles `#produit-…` restent valides : les liens déjà partagés continuent de fonctionner.

### 5.2 Mouvement

- **Au chargement, une seule séquence :**
  - les accolades s'écartent de 70 px en 1,2 s ;
  - les segments des pistes se remplissent à 130 ms d'écart ;
  - les vignettes montent de 28 px à 90 ms d'écart, à partir de 700 ms.
- **En réponse à un geste :**
  - le panneau choisi apparaît en 300 ms ;
  - au survol, une vignette monte de 4 px et prend une bordure bleue ;
  - les pastilles et les boutons changent de couleur.
- **Aucune animation au défilement.**
- **Mouvement réduit :** quand l'appareil demande moins de mouvement, aucune animation ne se joue et tout s'affiche dans son état final.
- **Délais :** ils sont calculés dans la feuille de style, avec `:nth-child`, jamais par un attribut `style`.

### 5.3 Résumés courts des vignettes

Chaque produit reçoit un champ `summary` dans `content/products.php`. Il est obligatoire, ne peut pas être vide et compte 70 caractères au plus. Textes proposés, à valider par Elisée :

| Produit | Résumé |
| --- | --- |
| Oeil 360° Finance | Revenus, dépenses et comptes en franc CFA, au même endroit. |
| Dis oui | Une demande de rendez-vous transformée en petit jeu. |
| PROVIA | Stages : étudiants et entreprises. Projet en cours. |
| Carte UAC | Le campus d'Abomey-Calavi, à pied, même sans réseau. |

### 5.4 Téléphone et tablette

Les paliers actuels restent : 30, 40, 48, 60 et 75 rem.

- **Accueil :** le texte vient d'abord, puis le registre. Les accolades rétrécissent et passent derrière le titre.
- **Vignettes :** deux colonnes sous 60 rem.
- **Onglets :** ils défilent horizontalement, avec un arrêt sur chaque onglet. Le panneau s'empile : la capture, puis le texte.
- **Réalisations et services :** une colonne.
- **Méthode :** les étapes se suivent à la verticale, reliées par un trait vertical.
- **Contact :** le titre et l'accroche, puis le formulaire, qui reste l'action principale, puis la carte directe.

### 5.5 Accessibilité

- Les contrastes de 3.2.
- Le focus est visible : contour blanc sur les fonds bleus et charbon, contour bleu sur les fonds clairs.
- Les cibles tactiles mesurent au moins 44 px.
- Les accolades et les captures des vignettes sont décoratives. Les captures des panneaux gardent leur texte alternatif.

### 5.6 Performance

- Aucune nouvelle ressource : les vignettes reprennent les captures existantes.
- Les dimensions des images et des vignettes sont réservées : le décalage de mise en page reste sous 0,05.
- La feuille de style reste sous 40 Ko et le script sous 10 Ko.

## 6. Tests

- **Tests PHP de l'accueil :**
  - quatre vignettes, chacune liée à `#produit-…` ;
  - aucun lien externe vers PROVIA ;
  - une liste d'onglets masquée par défaut, avec quatre onglets reliés à quatre panneaux ;
  - les cinq étapes de la méthode ;
  - la carte de contact directe ;
  - aucun attribut `style` dans la page.
- **Tests PHP du contenu :** chaque résumé est présent, non vide et compte 70 caractères au plus.
- **Script :** ESLint et Prettier.
- **Navigateur :**
  - largeurs 390, 820 et 1440 px ;
  - onglets à la souris et au clavier ;
  - ancres `#produit-…` au chargement ;
  - page sans JavaScript ;
  - mouvement réduit ;
  - console sans erreur ;
  - Lighthouse mobile.

## 7. Hors périmètre

- De nouvelles images ou de nouvelles pages.
- La refonte des pages légales.
- Tout changement de contenu au-delà des résumés courts.
- Un mode sombre.

## 8. Questions ouvertes

- Le lien GitHub de la carte de contact et les données structurées qui nomment Elisée comme fondateur : à garder ou à retirer. La question a été posée le 3 octobre 2026. En attendant sa réponse, ils restent.
- Les quatre résumés courts de 5.3 : à valider.

## 9. Mise en ligne

Version 1.3 sur main, déployée avec `deploy.sh`. Les vérifications en production portent sur les pages, les en-têtes, le rendu, les onglets et l'envoi du formulaire.
