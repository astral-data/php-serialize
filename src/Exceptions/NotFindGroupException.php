<?php

namespace Astral\Serialize\Exceptions;

use Exception;

class NotFindGroupException extends Exception
{
    public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
