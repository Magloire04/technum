<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use Technum\Tests\Support\Html;

final class ContactSubmissionTest extends ApplicationTestCase
{
    /**
     * @return array<string, string>
     */
    private function validForm(int $tokenAge = 30): array
    {
        return [
            'token' => $this->tokenIssuedSecondsAgo($tokenAge),
            'website' => '',
            'name' => 'Awa Dossou',
            'organization' => 'Coopérative Agbo',
            'email' => 'awa@example.bj',
            'need' => 'management',
            'message' => 'Nous voulons suivre nos ventes depuis le téléphone.',
            'consent' => 'oui',
        ];
    }

    private function securityLog(): string
    {
        $file = $this->storageDir . '/logs/security.log';

        return is_file($file) ? (string) file_get_contents($file) : '';
    }

    public function testValidRequestSendsOneEmailAndRedirectsToTheConfirmation(): void
    {
        $response = $this->post('/contact', $this->validForm());

        self::assertSame(303, $response->status);
        self::assertSame('/?envoi=ok#contact', $response->header('Location'));
        self::assertCount(1, $this->mailer->sent);
        self::assertSame('awa@example.bj', $this->mailer->sent[0]->replyToEmail);
        self::assertStringContainsString('Application de gestion', $this->mailer->sent[0]->subject);
    }

    public function testConfirmationIsShownAfterTheRedirect(): void
    {
        $html = Html::parse($this->get('/', ['envoi' => 'ok'])->body);

        self::assertSame("Demande envoyée. Nous vous répondons à l'adresse indiquée.", $html->text('.form-status--success'));
    }

    public function testFormPostsToTheContactAnchorAndWorksWithoutJavascript(): void
    {
        $html = Html::parse($this->get('/')->body);

        self::assertSame('/contact#contact', $html->attribute('form.contact-form', 'action'));
        self::assertSame('post', $html->attribute('form.contact-form', 'method'));
        self::assertNotSame('', $html->attribute('input[name="token"]', 'value'));
        self::assertSame('-1', $html->attribute('#contact-website', 'tabindex'));
        self::assertSame(0, $html->count('#contact-consent[checked]'));
        self::assertSame(0, $html->count('.form-status'));
    }

    public function testInvalidRequestIsRedisplayedWithErrorsAndValues(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'name' => '', 'message' => 'Trop court']);
        $html = Html::parse($response->body);

        self::assertSame(422, $response->status);
        self::assertSame('Indiquez votre nom.', $html->text('#contact-name-erreur'));
        self::assertSame('true', $html->attribute('#contact-name', 'aria-invalid'));
        self::assertSame('contact-name-erreur', $html->attribute('#contact-name', 'aria-describedby'));
        self::assertSame('Trop court', $html->text('#contact-message'));
        self::assertSame('awa@example.bj', $html->attribute('#contact-email', 'value'));
        self::assertSame('management', $html->attribute('#contact-need option[selected]', 'value'));
        self::assertSame([], $this->mailer->sent);
    }

    public function testQuickCorrectionAfterAnErrorIsStillSent(): void
    {
        $first = $this->post('/contact', [...$this->validForm(), 'consent' => '']);
        $token = Html::parse($first->body)->attribute('input[name="token"]', 'value');

        $this->clock->advance(1);
        $response = $this->post('/contact', [...$this->validForm(), 'token' => $token]);

        self::assertSame(422, $first->status);
        self::assertSame(303, $response->status);
        self::assertCount(1, $this->mailer->sent);
    }

    public function testRedisplayedValuesAreEscaped(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'name' => '<script>alert(1)</script>', 'email' => 'pas-valide']);

        self::assertSame(422, $response->status);
        self::assertStringNotContainsString('<script>alert(1)</script>', $response->body);
        self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $response->body);
    }

    public function testHoneypotSubmissionIsSilentlyDropped(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'website' => 'https://spam.example']);

        self::assertSame(303, $response->status);
        self::assertSame([], $this->mailer->sent);
        self::assertStringContainsString('contact.honeypot', $this->securityLog());
    }

    public function testTamperedTokenIsSilentlyDropped(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'token' => '123.abc']);

        self::assertSame(303, $response->status);
        self::assertSame([], $this->mailer->sent);
        self::assertStringContainsString('contact.token_invalid', $this->securityLog());
    }

    public function testTooFastSubmissionIsSilentlyDropped(): void
    {
        $response = $this->post('/contact', $this->validForm(1));

        self::assertSame(303, $response->status);
        self::assertSame([], $this->mailer->sent);
        self::assertStringContainsString('contact.too_fast', $this->securityLog());
    }

    public function testExpiredTokenAsksToSendAgainAndKeepsValues(): void
    {
        $response = $this->post('/contact', $this->validForm(7201));
        $html = Html::parse($response->body);

        self::assertSame(422, $response->status);
        self::assertStringContainsString('Le formulaire a expiré', $html->text('.form-status--error'));
        self::assertSame('Awa Dossou', $html->attribute('#contact-name', 'value'));
        self::assertSame([], $this->mailer->sent);
    }

    public function testExpiredFormCanBeSentAgainRightAway(): void
    {
        $first = $this->post('/contact', $this->validForm(7201));
        $token = Html::parse($first->body)->attribute('input[name="token"]', 'value');

        $this->clock->advance(1);
        $response = $this->post('/contact', [...$this->validForm(), 'token' => $token]);

        self::assertSame(303, $response->status);
        self::assertCount(1, $this->mailer->sent);
    }

    public function testSixthValidRequestWithinAnHourIsRateLimited(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            self::assertSame(303, $this->post('/contact', $this->validForm())->status);
        }

        $response = $this->post('/contact', $this->validForm());

        self::assertSame(429, $response->status);
        self::assertStringContainsString('+229 01 50 61 73 00', Html::parse($response->body)->text('.form-status--error'));
        self::assertCount(5, $this->mailer->sent);
    }

    public function testRateLimitIsKeptPerClientAddress(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->post('/contact', $this->validForm(), '203.0.113.10');
        }

        self::assertSame(303, $this->post('/contact', $this->validForm(), '198.51.100.20')->status);
    }

    public function testMailFailureOffersTheDirectContacts(): void
    {
        $this->mailer->fails = true;

        $response = $this->post('/contact', $this->validForm());
        $notice = Html::parse($response->body)->text('.form-status--error');

        self::assertSame(503, $response->status);
        self::assertStringContainsString('+229 01 50 61 73 00', $notice);
        self::assertStringContainsString('elisee.atonde@bytechnum.com', $notice);
        self::assertStringContainsString('contact.mail_failed', $this->securityLog());
    }

    public function testArrayValuedFieldsNeverCauseAServerError(): void
    {
        $response = $this->post('/contact', [...$this->validForm(), 'name' => ['Awa'], 'message' => ['x']]);

        self::assertSame(422, $response->status);
    }
}
