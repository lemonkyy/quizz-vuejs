<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationException) {
            $response = new JsonResponse([
                'code' => $exception->getErrorCode(),
                'error' => $exception->getMessage(),
            ]);
            $response->setStatusCode($exception->getStatusCode());
            $event->setResponse($response);
        } else if ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse([
                'error' => $exception->getMessage(),
            ]);
            $response->setStatusCode($exception->getStatusCode());
            $event->setResponse($response);
        }
    }
}
