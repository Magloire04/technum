<?php

declare(strict_types=1);

// Produits de TECHNUM. « next » vide : le bloc affiche « Version stable, maintenue. ».
// « summary » : résumé des vignettes de l'accueil, 70 caractères au plus.
// « url » vide : aucun accès public, la page n'affiche ni lien ni adresse pour ce produit.
// « srcSmall » désigne la même capture en largeur moitié, ou reste vide.
return [
    [
        'slug' => 'oeil360-finance',
        'name' => 'Oeil 360° Finance',
        'url' => 'https://oeil360finance.bytechnum.com',
        'tagline' => 'Suivre ses revenus, ses dépenses et ses comptes en franc CFA, au même endroit.',
        'summary' => 'Revenus, dépenses et comptes en franc CFA, au même endroit.',
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
        'summary' => 'Une demande de rendez-vous transformée en petit jeu.',
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
        'url' => '',
        'tagline' => 'Mettre en relation les étudiants béninois et les entreprises qui cherchent des stagiaires.',
        'summary' => 'Stages : étudiants et entreprises. Projet en cours.',
        'audience' => 'Étudiants, recruteurs et établissements.',
        'stage' => 'beta',
        'done' => 'Profils étudiants et recruteurs, publication et consultation des offres, candidature en ligne.',
        'next' => 'Ouverture complète des inscriptions, espace étudiant complet, espace recruteur, suivi par les établissements, puis calcul de compatibilité entre profils et offres.',
        'note' => "PROVIA est en cours de développement : son accès n'est pas encore ouvert.",
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
        'summary' => "Le campus d'Abomey-Calavi, à pied, même sans réseau.",
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
