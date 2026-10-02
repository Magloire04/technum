<?php

declare(strict_types=1);

namespace Technum\Controller;

use Technum\Clock\Clock;
use Technum\Contact\ContactFormState;
use Technum\Contact\FormToken;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Page\HomePage;

final class HomeController
{
    public function __construct(
        private readonly HomePage $homePage,
        private readonly FormToken $formToken,
        private readonly Clock $clock,
    ) {
    }

    public function show(Request $request): Response
    {
        $form = new ContactFormState(
            token: $this->formToken->issue($this->clock->now()),
            sent: ($request->query['envoi'] ?? '') === 'ok',
        );

        return Response::html($this->homePage->render($form));
    }
}
