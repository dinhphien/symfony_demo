<?php

namespace App\Resolvers;

use App\Exception\InvalidDataRequestException;
use App\Helper\ErrorParser;
use App\Requests\BaseValidatingRequest;
use App\Requests\ValidateRequestInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestResolver implements ArgumentValueResolverInterface
{
    private $security;
    private $validator;

    public function __construct(Security $security, ValidatorInterface $validator)
    {
        $this->security = $security;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if ((new ReflectionClass($argument->getType()))->isSubclassOf(BaseValidatingRequest::class)) {
            return true;
        }
        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        $currentUser = $this->security->getUser();
        $argRequest = new $class($request, $currentUser);
        if (!$argRequest->authorize()) {
            throw new AccessDeniedHttpException('Access Denied!');
        }
        $violations = $this->validator->validate($argRequest);
        if ($violations->count() > 0) {
            $firstViolation = $violations->get(0);
            $errorMsg = $firstViolation->getPropertyPath() . ': ' . $firstViolation->getMessage();
            throw new InvalidDataRequestException($errorMsg);
        }
        yield $argRequest;
    }
}
