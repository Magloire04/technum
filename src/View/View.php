<?php

declare(strict_types=1);

namespace Technum\View;

use RuntimeException;
use Throwable;

/**
 * Rendu des gabarits PHP. Dans un gabarit, $view désigne cette instance.
 */
final class View
{
    /** @var array<string, mixed> */
    private array $shared = [];

    public function __construct(
        private readonly string $templatesDir,
        private readonly string $publicDir,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function share(array $data): void
    {
        $this->shared = [...$this->shared, ...$data];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $file = $this->templatesDir . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('Gabarit introuvable : ' . $template);
        }

        return $this->capture($file, [...$this->shared, ...$data]);
    }

    /**
     * Rend un gabarit de page puis l'insère dans le gabarit commun « layout ».
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     */
    public function renderPage(string $template, array $data, array $meta): string
    {
        $content = $this->render($template, $data);

        return $this->render('layout', ['content' => $content, 'meta' => $meta]);
    }

    /**
     * Adresse d'un fichier de public/assets, versionnée par sa date de modification.
     */
    public function asset(string $path): string
    {
        $relative = ltrim($path, '/');
        $file = $this->publicDir . '/assets/' . $relative;
        $version = is_file($file) ? (string) filemtime($file) : '0';

        return '/assets/' . $relative . '?v=' . $version;
    }

    /**
     * @param array<string, mixed> $variables
     */
    private function capture(string $file, array $variables): string
    {
        $view = $this;
        extract($variables, EXTR_SKIP);
        ob_start();

        try {
            require $file;
        } catch (Throwable $exception) {
            ob_end_clean();

            throw $exception;
        }

        return (string) ob_get_clean();
    }
}
