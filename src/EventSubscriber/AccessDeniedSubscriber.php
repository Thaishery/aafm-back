<?php
// src/EventSubscriber/AccessDeniedSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedSubscriber implements EventSubscriberInterface
{
  public static function getSubscribedEvents(): array
  {
    return [
      'kernel.exception' => 'onKernelException',
    ];
  }

  public function onKernelException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();

    if ($exception instanceof AccessDeniedHttpException) {
      // Customize your JSON response here
      $response = new JsonResponse([
        'error' => [
          'code' => $exception->getStatusCode(),
          'message' => 'Access Denied',
        ],
      ], 403);

      // Set the response to the event
      $event->setResponse($response);
    }
  }
}
