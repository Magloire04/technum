<?php

declare(strict_types=1);

namespace Technum\Contact;

interface MailerInterface
{
    /**
     * @throws MailerException quand l'envoi échoue
     */
    public function send(ContactMessage $message): void;
}
