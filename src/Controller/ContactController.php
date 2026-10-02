<?php

declare(strict_types=1);

namespace Technum\Controller;

use Technum\Clock\Clock;
use Technum\Contact\ContactFormState;
use Technum\Contact\ContactMessage;
use Technum\Contact\ContactRequest;
use Technum\Contact\FormToken;
use Technum\Contact\MailerException;
use Technum\Contact\MailerInterface;
use Technum\Contact\TokenStatus;
use Technum\Content\SiteInfo;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Page\HomePage;
use Technum\Security\RateLimiter;
use Technum\Security\SecurityLog;

final class ContactController
{
    public const SUCCESS_LOCATION = '/?envoi=ok#contact';

    public function __construct(
        private readonly HomePage $homePage,
        private readonly FormToken $formToken,
        private readonly RateLimiter $rateLimiter,
        private readonly MailerInterface $mailer,
        private readonly SecurityLog $securityLog,
        private readonly Clock $clock,
        private readonly SiteInfo $site,
    ) {
    }

    public function submit(Request $request): Response
    {
        $now = $this->clock->now();
        if ($request->input('website') !== '') {
            return $this->dropSilently('contact.honeypot', $now);
        }
        $tokenStatus = $this->formToken->verify($request->input('token'), $now);
        if ($tokenStatus === TokenStatus::Invalid) {
            return $this->dropSilently('contact.token_invalid', $now);
        }
        if ($tokenStatus === TokenStatus::TooFast) {
            return $this->dropSilently('contact.too_fast', $now);
        }

        $contact = ContactRequest::fromInput($request->body);
        if ($tokenStatus === TokenStatus::Expired) {
            $this->securityLog->record('contact.token_expired', $now);

            return $this->redisplay($contact, $now, 422, 'Le formulaire a expiré. Vérifiez vos informations, puis envoyez-les de nouveau.', $contact->errors);
        }
        if (!$contact->isValid()) {
            return $this->redisplay($contact, $now, 422, "La demande n'est pas partie. Corrigez les champs signalés.", $contact->errors);
        }
        if ($this->rateLimiter->isLimited($request->clientIp, $now)) {
            $this->securityLog->record('contact.rate_limited', $now);

            return $this->redisplay($contact, $now, 429, sprintf(
                'Trop de demandes depuis cette connexion. Réessayez dans une heure, ou écrivez-nous sur WhatsApp au %s.',
                $this->site->phoneDisplay,
            ));
        }

        try {
            $this->mailer->send(ContactMessage::fromRequest($contact));
        } catch (MailerException) {
            $this->securityLog->record('contact.mail_failed', $now);

            return $this->redisplay($contact, $now, 503, sprintf(
                "L'envoi n'a pas abouti. Écrivez-nous sur WhatsApp au %s ou à %s.",
                $this->site->phoneDisplay,
                $this->site->email,
            ));
        }

        $this->rateLimiter->hit($request->clientIp, $now);
        $this->rateLimiter->purgeExpired($now);

        return Response::redirect(self::SUCCESS_LOCATION);
    }

    /**
     * Les robots reçoivent la même réponse qu'un envoi réussi, pour ne rien leur apprendre.
     */
    private function dropSilently(string $event, int $now): Response
    {
        $this->securityLog->record($event, $now);

        return Response::redirect(self::SUCCESS_LOCATION);
    }

    /**
     * @param array<string, string> $errors
     */
    private function redisplay(ContactRequest $contact, int $now, int $status, string $notice, array $errors = []): Response
    {
        $form = new ContactFormState($this->formToken->issue($now), $contact->values(), $errors, $notice);

        return Response::html($this->homePage->render($form), $status);
    }
}
