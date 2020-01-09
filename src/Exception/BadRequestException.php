<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky\Exception;

use Exception;

class BadRequestException extends Exception
{
    public function __construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }
}
