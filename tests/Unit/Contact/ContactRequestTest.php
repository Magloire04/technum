<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactRequest;

final class ContactRequestTest extends TestCase
{
    /** @return array<string, string> */
    private static function validInput(): array
    {
        return [
            'name' => 'Awa Dossou',
            'organization' => 'Coopérative Agbo',
            'email' => 'awa@example.bj',
            'need' => 'management',
            'message' => 'Nous voulons suivre nos ventes depuis le téléphone.',
            'consent' => 'oui',
        ];
    }

    public function testValidInputHasNoErrors(): void
    {
        $request = ContactRequest::fromInput(self::validInput());

        self::assertTrue($request->isValid());
        self::assertSame([], $request->errors);
        self::assertSame('Application de gestion', $request->needLabel());
        self::assertSame('Awa Dossou', $request->name);
    }

    public function testOrganizationIsOptional(): void
    {
        self::assertTrue(ContactRequest::fromInput([...self::validInput(), 'organization' => ''])->isValid());
    }

    /** @return iterable<string, array{array<string, mixed>, string, string}> */
    public static function invalidInputs(): iterable
    {
        yield 'nom absent' => [['name' => ''], 'name', 'Indiquez votre nom.'];
        yield 'nom trop court' => [['name' => 'A'], 'name', 'Votre nom doit contenir entre 2 et 100 caractères.'];
        yield 'nom trop long' => [['name' => str_repeat('a', 101)], 'name', 'Votre nom doit contenir entre 2 et 100 caractères.'];
        yield 'nom envoyé en tableau' => [['name' => ['Awa']], 'name', 'Indiquez votre nom.'];
        yield 'organisation trop longue' => [['organization' => str_repeat('o', 121)], 'organization', "Le nom de l'organisation ne doit pas dépasser 120 caractères."];
        yield 'e-mail absent' => [['email' => ''], 'email', 'Indiquez votre adresse e-mail.'];
        yield 'e-mail invalide' => [['email' => 'awa@'], 'email', "Cette adresse e-mail n'est pas valide."];
        yield 'besoin inconnu' => [['need' => 'pirater'], 'need', 'Choisissez le type de besoin.'];
        yield 'message vide' => [['message' => '   '], 'message', 'Décrivez votre besoin.'];
        yield 'message trop court' => [['message' => 'Bonjour'], 'message', 'Votre message doit contenir au moins 20 caractères.'];
        yield 'message trop long' => [['message' => str_repeat('m', 3001)], 'message', "Votre message ne doit pas dépasser 3\u{00A0}000 caractères."];
        yield 'accord absent' => [['consent' => ''], 'consent', 'Cochez la case pour que nous puissions vous répondre.'];
    }

    /**
     * @param array<string, mixed> $override
     */
    #[DataProvider('invalidInputs')]
    public function testInvalidInputIsReportedOnTheRightField(array $override, string $field, string $message): void
    {
        $request = ContactRequest::fromInput([...self::validInput(), ...$override]);

        self::assertFalse($request->isValid());
        self::assertSame($message, $request->errors[$field] ?? null);
    }

    public function testLimitsAreCountedInCharactersNotBytes(): void
    {
        $request = ContactRequest::fromInput([
            ...self::validInput(),
            'name' => str_repeat('é', 100),
            'message' => str_repeat('è', 3000),
        ]);

        self::assertTrue($request->isValid());
    }

    public function testControlCharactersAreRemovedFromSingleLineFields(): void
    {
        $request = ContactRequest::fromInput([
            ...self::validInput(),
            'name' => "Awa\r\nBcc: pirate@example.com",
            'organization' => "Agbo\x00",
        ]);

        self::assertSame('Awa Bcc: pirate@example.com', $request->name);
        self::assertSame('Agbo', $request->organization);
    }

    public function testMessageKeepsNormalizedLineBreaks(): void
    {
        $request = ContactRequest::fromInput([...self::validInput(), 'message' => "Première ligne assez longue\r\nDeuxième ligne"]);

        self::assertSame("Première ligne assez longue\nDeuxième ligne", $request->message);
    }

    public function testInvalidUtf8IsTreatedAsEmpty(): void
    {
        $request = ContactRequest::fromInput([...self::validInput(), 'name' => "\xC3\x28"]);

        self::assertSame('Indiquez votre nom.', $request->errors['name'] ?? null);
    }

    public function testValuesAreReturnedForRedisplay(): void
    {
        $values = ContactRequest::fromInput([...self::validInput(), 'email' => 'pas-valide'])->values();

        self::assertSame('pas-valide', $values['email']);
        self::assertSame('oui', $values['consent']);
        self::assertSame('management', $values['need']);
    }
}
