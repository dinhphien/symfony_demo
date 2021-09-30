<?php

namespace App\Helper;

use Symfony\Component\Validator\ConstraintViolationList;

class ErrorParser
{
    public static function parseConstraintViolations(ConstraintViolationList $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $errors;
    }
}
