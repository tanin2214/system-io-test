<?php

declare(strict_types=1);

namespace App\Event\EventSubscriber;

use App\Exception\ControllerAppException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

readonly class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $this->getResponseStatusCode($exception);
        $event->allowCustomResponseCode();

        $response = new JsonResponse(data: $this->getData($exception), status: $statusCode);
        $event->setResponse($response);
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(Throwable $exception): array
    {
        $data = [
            'error' => $this->getErrorMessage($exception),
        ];

        return array_merge($data, $this->getDebugData($exception));
    }

    /**
     * @return array<string, mixed>
     */
    private function getDebugData(Throwable $exception): array
    {
        if (! $this->kernel->isDebug()) {
            return [];
        }

        $exceptionData = [
            'class' => $exception::class,
            'code' => $this->getResponseStatusCode($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
        ];

        return [
            'debug' => [
                'exception' => $exceptionData,
            ],
        ];
    }

    private function getResponseStatusCode(Throwable $exception): int
    {
        return $exception instanceof ControllerAppException ? $exception->getHttpStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function getErrorMessage(Throwable $exception): string
    {
        return $exception instanceof ControllerAppException
            ? $exception->getUserInterfaceMessage()
            : $exception->getMessage();
    }
}
