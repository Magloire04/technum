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
