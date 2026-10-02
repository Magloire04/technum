<?php

declare(strict_types=1);

namespace Technum\Tests\Support;

use Technum\Contact\ContactMessage;
use Technum\Contact\MailerException;
use Technum\Contact\MailerInterface;

final class FakeMailer implements MailerInterface
{
    /** @var list<ContactMessage> */
    public array $sent = [];

    public bool $fails = false;

    public function send(ContactMessage $message): void
    {
        if ($this->fails) {
            throw new MailerException('Échec simulé.');
        }
        $this->sent[] = $message;
    }
}
