<?php

declare(strict_types=1);

namespace Technum\Contact;

/**
 * Expéditeur du développement local : écrit chaque demande dans un fichier au lieu de l'envoyer.
 */
final class LogMailer implements MailerInterface
{
    public function __construct(private readonly string $file)
    {
    }

    public function send(ContactMessage $message): void
    {
        $directory = dirname($this->file);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new MailerException('Dossier du journal local inaccessible.');
        }
        $entry = '--- ' . gmdate('c') . "\n"
            . 'Répondre à : ' . $message->replyToName . ' <' . $message->replyToEmail . ">\n"
            . 'Objet : ' . $message->subject . "\n\n"
            . $message->body . "\n\n";
        if (file_put_contents($this->file, $entry, FILE_APPEND | LOCK_EX) === false) {
            throw new MailerException('Écriture du journal local impossible.');
        }
    }
}
