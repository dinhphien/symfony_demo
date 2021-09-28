<?php

namespace App\Exception;

use Exception;
use Throwable;

class InvalidDataRequestException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}