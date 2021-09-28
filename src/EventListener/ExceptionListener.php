<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener 
{
    public function onKernelException(ExceptionEvent $event)
    {
        $ex = $event->getThrowable();
        $customResponse =  new JsonResponse([
            'success' => false,
            'message' => $ex->getMessage()
        ]);
        $event->setResponse($customResponse);
    }
}