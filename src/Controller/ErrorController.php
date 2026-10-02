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
