<?php

declare(strict_types=1);

namespace Technum\Contact;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Envoi par le SMTP de la messagerie Spacemail. Le détail d'un échec n'est jamais journalisé :
 * il peut contenir l'adresse du demandeur.
 */
final class SmtpMailer implements MailerInterface
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $username,
        private readonly string $password,
        private readonly string $senderEmail,
        private readonly string $recipientEmail,
    ) {
    }

    public function send(ContactMessage $message): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->Port = $this->port;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Timeout = 15;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->setFrom($this->senderEmail, 'Site TECHNUM');
            $mail->Sender = $this->senderEmail;
            $mail->addAddress($this->recipientEmail);
            $mail->addReplyTo($message->replyToEmail, $message->replyToName);
            $mail->Subject = $message->subject;
            $mail->isHTML(false);
            $mail->Body = $message->body;
            $mail->send();
        } catch (PHPMailerException $exception) {
            throw new MailerException("L'envoi SMTP a échoué.", 0, $exception);
        }
    }
}
