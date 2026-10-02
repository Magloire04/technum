<?php

declare(strict_types=1);

namespace Technum\Contact;

/**
 * Demande envoyée par le formulaire de contact, nettoyée puis validée.
 */
final class ContactRequest
{
    public const NEEDS = [
        'management' => 'Application de gestion',
        'platform' => 'Plateforme en ligne',
        'website' => 'Site web ou produit numérique',
        'security' => 'Sécurité et conformité',
        'hosting' => 'Hébergement et suivi',
        'other' => 'Autre',
    ];

    public const CONSENT_VALUE = 'oui';

    public const MESSAGE_MIN = 20;

    public const MESSAGE_MAX = 3000;

    /**
     * @param array<string, string> $errors
     */
    private function __construct(
        public readonly string $name,
        public readonly string $organization,
        public readonly string $email,
        public readonly string $need,
        public readonly string $message,
        public readonly bool $consent,
        public readonly array $errors,
    ) {
    }

    /**
     * @param array<array-key, mixed> $input
     */
    public static function fromInput(array $input): self
    {
        $name = self::singleLine($input['name'] ?? '');
        $organization = self::singleLine($input['organization'] ?? '');
        $email = self::singleLine($input['email'] ?? '');
        $need = self::singleLine($input['need'] ?? '');
        $message = self::multiLine($input['message'] ?? '');
        $consent = ($input['consent'] ?? '') === self::CONSENT_VALUE;

        $errors = [];
        $nameLength = mb_strlen($name);
        if ($nameLength === 0) {
            $errors['name'] = 'Indiquez votre nom.';
        } elseif ($nameLength < 2 || $nameLength > 100) {
            $errors['name'] = 'Votre nom doit contenir entre 2 et 100 caractères.';
        }
        if (mb_strlen($organization) > 120) {
            $errors['organization'] = "Le nom de l'organisation ne doit pas dépasser 120 caractères.";
        }
        if ($email === '') {
            $errors['email'] = 'Indiquez votre adresse e-mail.';
        } elseif (mb_strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = "Cette adresse e-mail n'est pas valide.";
        }
        if (!array_key_exists($need, self::NEEDS)) {
            $errors['need'] = 'Choisissez le type de besoin.';
        }
        $messageLength = mb_strlen($message);
        if ($messageLength === 0) {
            $errors['message'] = 'Décrivez votre besoin.';
        } elseif ($messageLength < self::MESSAGE_MIN) {
            $errors['message'] = 'Votre message doit contenir au moins 20 caractères.';
        } elseif ($messageLength > self::MESSAGE_MAX) {
            $errors['message'] = "Votre message ne doit pas dépasser 3\u{00A0}000 caractères.";
        }
        if (!$consent) {
            $errors['consent'] = 'Cochez la case pour que nous puissions vous répondre.';
        }

        return new self($name, $organization, $email, $need, $message, $consent, $errors);
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function needLabel(): string
    {
        return self::NEEDS[$this->need] ?? '';
    }

    /**
     * Valeurs nettoyées, pour réafficher le formulaire.
     *
     * @return array<string, string>
     */
    public function values(): array
    {
        return [
            'name' => $this->name,
            'organization' => $this->organization,
            'email' => $this->email,
            'need' => $this->need,
            'message' => $this->message,
            'consent' => $this->consent ? self::CONSENT_VALUE : '',
        ];
    }

    /**
     * Retire les caractères de contrôle, dont les retours à la ligne : aucune injection d'en-tête possible.
     */
    private static function singleLine(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }
        $clean = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value);
        if ($clean === null) {
            return '';
        }

        return trim((string) preg_replace('/\s+/u', ' ', $clean));
    }

    private static function multiLine(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }
        $normalized = str_replace(["\r\n", "\r"], "\n", $value);
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $normalized);

        return $clean === null ? '' : trim($clean);
    }
}
