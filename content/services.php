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
