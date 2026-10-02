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
