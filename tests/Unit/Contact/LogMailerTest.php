<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactMessage;
use Technum\Contact\ContactRequest;
use Technum\Contact\LogMailer;
use Technum\Contact\MailerException;
use Technum\Tests\Support\TempDirectory;

final class LogMailerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-mail');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    public function testWritesTheMessageToTheLocalFile(): void
    {
        $file = $this->directory . '/mail-local.log';

        (new LogMailer($file))->send(self::message());

        $content = (string) file_get_contents($file);
        self::assertStringContainsString('Répondre à : Awa Dossou <awa@example.bj>', $content);
        self::assertStringContainsString('Objet : Demande de projet : Application de gestion, Awa Dossou', $content);
    }

    public function testMissingDirectoryRaisesMailerException(): void
    {
        $this->expectException(MailerException::class);

        (new LogMailer($this->directory . '/absent/mail-local.log'))->send(self::message());
    }

    private static function message(): ContactMessage
    {
        return ContactMessage::fromRequest(ContactRequest::fromInput([
            'name' => 'Awa Dossou',
            'organization' => 'Coopérative Agbo',
            'email' => 'awa@example.bj',
            'need' => 'management',
            'message' => 'Nous voulons suivre nos ventes depuis le téléphone.',
            'consent' => 'oui',
        ]));
    }
}
