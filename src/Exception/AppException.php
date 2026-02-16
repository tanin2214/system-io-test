<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class AppException extends ControllerAppException
{
    public function __construct(
        string $userInterfaceMessage,
        int $httpStatusCode,
        ?string $message = null,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $this->userInterfaceMessage = $userInterfaceMessage;
        $this->httpStatusCode = $httpStatusCode;

        parent::__construct($message ?? $userInterfaceMessage, $code, $previous);
    }
}
