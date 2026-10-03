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
