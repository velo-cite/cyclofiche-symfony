<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class ExceptionListener
{
    #[AsEventListener(event: ExceptionEvent::class)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Gestion des erreurs de validation
        if ($exception instanceof ValidationFailedException) {
            $errors = [];
            foreach ($exception->getViolations() as $violation) {
                $errors[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $event->setResponse(new JsonResponse([
                'status' => 422,
                'errors' => $errors,
            ], 422));

            return;
        }

        // Gestion générique pour 422
        if ($exception instanceof UnprocessableEntityHttpException) {
            $event->setResponse(new JsonResponse([
                'status' => 422,
                'error' => $exception->getMessage(),
            ], 422));

            return;
        }
    }
}
