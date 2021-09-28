<?php

namespace App\Services;

use App\Exception\InvalidDataRequestException;
use App\Requests\ValidateRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidatorService
{
    private $validator;
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(ValidateRequestInterface $request)
    {
        if (!$request->authorize()) {
            throw new AccessDeniedHttpException('Access Denied!');
        }
        $violations = $this->validator->validate($request);
        
        if ($violations->count() > 0) {
            $errorMsg = $violations->get(0)->getMessage();
            throw new InvalidDataRequestException($errorMsg);
        }
    }
}