<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Contact;

use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactFormState;

final class ContactFormStateTest extends TestCase
{
    public function testFieldWithoutErrorOrHelpHasNoAriaAttributes(): void
    {
        self::assertSame('', (new ContactFormState('jeton'))->ariaAttributes('name'));
    }

    public function testHelpTextIsLinkedToItsField(): void
    {
        self::assertSame(
            ' aria-describedby="contact-message-aide"',
            (new ContactFormState('jeton'))->ariaAttributes('message', 'contact-message-aide'),
        );
    }

    public function testErrorMarksTheFieldInvalidAndLinksTheMessage(): void
    {
        $state = new ContactFormState('jeton', ['message' => 'court'], ['message' => 'Trop court.']);

        self::assertSame(
            ' aria-invalid="true" aria-describedby="contact-message-aide contact-message-erreur"',
            $state->ariaAttributes('message', 'contact-message-aide'),
        );
        self::assertSame('court', $state->value('message'));
        self::assertSame('', $state->value('name'));
        self::assertSame('Trop court.', $state->error('message'));
    }
}
