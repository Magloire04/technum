<?php

declare(strict_types=1);

namespace Technum\Contact;

use LogicException;

/**
 * E-mail envoyé à TECHNUM pour une demande valide. Répondre au message écrit au demandeur.
 */
final class ContactMessage
{
    private function __construct(
        public readonly string $replyToEmail,
        public readonly string $replyToName,
        public readonly string $subject,
        public readonly string $body,
    ) {
    }

    public static function fromRequest(ContactRequest $request): self
    {
        if (!$request->isValid()) {
            throw new LogicException('Une demande invalide ne peut pas être envoyée.');
        }
        $body = implode("\n", [
            'Nom : ' . $request->name,
            'Organisation : ' . ($request->organization !== '' ? $request->organization : 'non précisée'),
            'E-mail : ' . $request->email,
            'Besoin : ' . $request->needLabel(),
            '',
            'Message :',
            $request->message,
            '',
            '-- ',
            'Envoyé depuis le formulaire de bytechnum.com. Répondre à ce message écrit directement au demandeur.',
        ]);

        return new self(
            $request->email,
            $request->name,
            'Demande de projet : ' . $request->needLabel() . ', ' . $request->name,
            $body,
        );
    }
}
