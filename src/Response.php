<?php
declare(strict_types=1);

namespace Readdle\BrazeClient;

/**
 * @link https://www.braze.com/docs/api/errors/#server-responses
 */
class Response
{
    private array $payload;
    private int $statusCode;
    private ?array $minorErrors = null;
    private ?array $fatalErrors = null;
    private bool $isSucceed;

    public function __construct(array $payload, int $statusCode)
    {
        $this->payload = $payload;
        $this->statusCode = $statusCode;

        $this->checkForErrors();
        $this->checkResponseStatus();
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMinorErrors(): ?array
    {
        return $this->minorErrors;
    }

    public function getFatalErrors(): ?array
    {
        return $this->fatalErrors;
    }

    public function isSucceed(): bool
    {
        return $this->isSucceed;
    }

    private function checkForErrors(): void
    {
        if (
            $this->getStatusCode() === 200 &&
            in_array($this->payload['message'], ['success', 'queued'])
        ) {
            return;
        }

        $message = $this->payload['message'];
        $errors = $this->payload['errors'] ?? [];
        if ($message === 'success' && !empty($errors)) {
            $this->minorErrors = $errors;
        }

        if ($message !== 'success' && !empty($errors)) {
            $this->fatalErrors = $errors;
        }
    }

    private function checkResponseStatus(): void
    {
        $this->isSucceed = is_null($this->minorErrors) && is_null($this->fatalErrors);
    }
}
