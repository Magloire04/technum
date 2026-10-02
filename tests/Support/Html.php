<?php

declare(strict_types=1);

namespace Technum\Tests\Support;

use Dom\Element;
use Dom\HTMLDocument;
use RuntimeException;

/**
 * Lecture d'une page HTML rendue, par sélecteurs CSS. Les espaces insécables deviennent des espaces.
 */
final class Html
{
    private function __construct(private readonly HTMLDocument $document)
    {
    }

    public static function parse(string $html): self
    {
        return new self(HTMLDocument::createFromString($html, LIBXML_NOERROR));
    }

    public function text(string $selector): string
    {
        return self::clean($this->first($selector)->textContent ?? '');
    }

    /**
     * @return list<string>
     */
    public function texts(string $selector): array
    {
        return array_map(static fn (Element $element): string => self::clean($element->textContent ?? ''), $this->elements($selector));
    }

    public function attribute(string $selector, string $name): string
    {
        return (string) $this->first($selector)->getAttribute($name);
    }

    /**
     * @return list<string>
     */
    public function attributes(string $selector, string $name): array
    {
        return array_map(static fn (Element $element): string => (string) $element->getAttribute($name), $this->elements($selector));
    }

    public function count(string $selector): int
    {
        return count($this->elements($selector));
    }

    /**
     * @return list<Element>
     */
    public function elements(string $selector): array
    {
        $elements = [];
        foreach ($this->document->querySelectorAll($selector) as $element) {
            if ($element instanceof Element) {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    private function first(string $selector): Element
    {
        $element = $this->document->querySelector($selector);
        if ($element === null) {
            throw new RuntimeException('Élément introuvable : ' . $selector);
        }

        return $element;
    }

    private static function clean(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', str_replace(["\u{00A0}", "\u{202F}"], ' ', $text)));
    }
}
