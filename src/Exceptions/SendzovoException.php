<?php
namespace Sendzovo\Exceptions;

use RuntimeException;

class SendzovoException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly ?int $statusCode = null,
        private readonly ?array $response = null
    ) {
        parent::__construct($message, $statusCode ?? 0);
    }

    public function statusCode(): ?int { return $this->statusCode; }
    public function response(): ?array { return $this->response; }
}
