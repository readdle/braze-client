<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Exception;

use RuntimeException;

class BrazeException extends RuntimeException
{
    protected array $responseBody = [];
    protected string $fullResponse = '';

    public function setBody(string $json): void
    {
        $this->responseBody = json_decode($json, true) ?? [];
    }

    public function getBody(): array
    {
        return $this->responseBody;
    }

    public function setFullResponse(string $fullResponse): void
    {
        $this->fullResponse = $fullResponse;
    }

    public function getFullResponse(): string
    {
        return $this->fullResponse;
    }
}
