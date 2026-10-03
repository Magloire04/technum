<?php

declare(strict_types=1);

namespace Technum\Http;

use Closure;

final class Router
{
    /** @var array<string, array<string, Closure(Request): Response>> */
    private array $routes = [];

    /**
     * @param Closure(Request): Response $notFound
     */
    public function __construct(private readonly Closure $notFound)
    {
    }

    /**
     * @param Closure(Request): Response $handler
     */
    public function get(string $path, Closure $handler): void
    {
        $this->routes['GET'][Request::normalizePath($path)] = $handler;
    }

    /**
     * @param Closure(Request): Response $handler
     */
    public function post(string $path, Closure $handler): void
    {
        $this->routes['POST'][Request::normalizePath($path)] = $handler;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method === 'HEAD' ? 'GET' : $request->method;
        $handler = $this->routes[$method][$request->path] ?? $this->notFound;

        return $handler($request);
    }
}
