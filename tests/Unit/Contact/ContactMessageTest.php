<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use LogicException;
use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactMessage;
use Technum\Contact\ContactRequest;

final class ContactMessageTest extends TestCase
{
    public function testMessageCarriesTheRequestAndRepliesToTheVisitor(): void
    {
        $request = ContactRequest::fromInput([
            'name' => 'Awa Dossou',
            'organization' => '',
            'email' => 'awa@example.bj',
            'need' => 'platform',
            'message' => "Une plateforme pour nos adhérents.\nAvec deux rôles.",
            'consent' => 'oui',
        ]);

        $message = ContactMessage::fromRequest($request);

        self::assertSame('awa@example.bj', $message->replyToEmail);
        self::assertSame('Awa Dossou', $message->replyToName);
        self::assertSame('Demande de projet : Plateforme en ligne, Awa Dossou', $message->subject);
        self::assertStringContainsString('Organisation : non précisée', $message->body);
        self::assertStringContainsString("Une plateforme pour nos adhérents.\nAvec deux rôles.", $message->body);
    }

    public function testInvalidRequestCannotBecomeAMessage(): void
    {
        $this->expectException(LogicException::class);

        ContactMessage::fromRequest(ContactRequest::fromInput([]));
    }
}
