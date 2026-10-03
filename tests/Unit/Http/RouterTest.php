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
