<?php

declare(strict_types=1);

namespace Technum\Contact;

/**
 * État du formulaire affiché : jeton, valeurs saisies, erreurs par champ, message général.
 */
final class ContactFormState
{
    /**
     * @param array<string, string> $values
     * @param array<string, string> $errors
     */
    public function __construct(
        public readonly string $token,
        public readonly array $values = [],
        public readonly array $errors = [],
        public readonly string $notice = '',
        public readonly bool $sent = false,
    ) {
    }

    public function value(string $field): string
    {
        return $this->values[$field] ?? '';
    }

    public function error(string $field): string
    {
        return $this->errors[$field] ?? '';
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function errorId(string $field): string
    {
        return 'contact-' . $field . '-erreur';
    }

    /**
     * Attributs ARIA d'un champ, déjà échappés : état invalide et textes qui le décrivent.
     */
    public function ariaAttributes(string $field, string $helpId = ''): string
    {
        $describedBy = array_filter([$helpId, $this->hasError($field) ? $this->errorId($field) : '']);
        $attributes = $this->hasError($field) ? ' aria-invalid="true"' : '';
        if ($describedBy !== []) {
            $attributes .= ' aria-describedby="' . e(implode(' ', $describedBy)) . '"';
        }

        return $attributes;
    }
}
