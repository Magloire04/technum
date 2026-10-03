<?php

declare(strict_types=1);

namespace Technum\Contact;

enum TokenStatus
{
    case Valid;
    case Invalid;
    case TooFast;
    case Expired;
}
