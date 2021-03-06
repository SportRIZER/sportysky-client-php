<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky\Exception;

use Exception;

class AuthenticationException extends Exception
{
    public function __construct($message, $code = 401)
    {
        parent::__construct($message, $code);
    }
}
