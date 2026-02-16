<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class ControllerAppException extends Exception
{
    protected string $userInterfaceMessage;

    protected int $httpStatusCode;

    public function getUserInterfaceMessage(): string
    {
        return $this->userInterfaceMessage;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
