<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Event\EventSubscriber\ApiExceptionSubscriber;
use App\Exception\AppException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ApiExceptionSubscriberTest extends TestCase
{
    public function testHandlesGenericExceptionAsInternalServerErrorWithoutDebug(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('isDebug')->willReturn(false);

        $subscriber = new ApiExceptionSubscriber($kernel);

        $exception = new \RuntimeException('Something went wrong');
        $request = new Request();

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertNotNull($response);
        self::assertSame(500, $response->getStatusCode());

        $content = (string) $response->getContent();
        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('Something went wrong', $data['error']);
        self::assertArrayNotHasKey('debug', $data);
    }

    public function testHandlesControllerAppExceptionWithCustomStatusAndMessage(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('isDebug')->willReturn(false);

        $subscriber = new ApiExceptionSubscriber($kernel);

        $exception = new AppException(userInterfaceMessage: 'UI error', httpStatusCode: 400);

        $request = new Request();

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertNotNull($response);
        self::assertSame(400, $response->getStatusCode());

        $content = (string) $response->getContent();
        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('UI error', $data['error']);
        self::assertArrayNotHasKey('debug', $data);
    }

    public function testIncludesDebugDataWhenKernelInDebugMode(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('isDebug')->willReturn(true);

        $subscriber = new ApiExceptionSubscriber($kernel);

        $exception = new \RuntimeException('Debug error');
        $request = new Request();

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception,
        );

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertNotNull($response);

        $content = (string) $response->getContent();
        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('debug', $data);
        /** @var array<string, mixed> $debug */
        $debug = $data['debug'];
        self::assertArrayHasKey('exception', $debug);
        /** @var array<string, mixed> $exceptionData */
        $exceptionData = $debug['exception'];

        self::assertSame(\RuntimeException::class, $exceptionData['class']);
        self::assertSame('Debug error', $exceptionData['message']);
    }
}
