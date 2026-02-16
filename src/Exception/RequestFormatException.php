<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RequestFormatException extends ControllerAppException
{
    public function __construct(
        string $userInterfaceMessage = "Ошибка валидации запроса",
        int $httpStatusCode = Response::HTTP_BAD_REQUEST,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $this->userInterfaceMessage = $userInterfaceMessage;
        $this->httpStatusCode = $httpStatusCode;

        parent::__construct($message ?? $userInterfaceMessage, $code, $previous);
    }
}
