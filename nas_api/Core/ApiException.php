<?php

declare(strict_types=1);

namespace NasApi\Core;

use RuntimeException;

final class ApiException extends RuntimeException
{
    private int $statusCode;
    private string $errorCode;

    public function __construct(
        string $message,
        int $statusCode = 400,
        string $errorCode = 'bad_request'
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
