<?php

declare(strict_types=1);

namespace Technum\Controller;

use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Page\HomePage;

final class HomeController
{
    public function __construct(private readonly HomePage $homePage)
    {
    }

    public function show(Request $request): Response
    {
        return Response::html($this->homePage->render());
    }
}
