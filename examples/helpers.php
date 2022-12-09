<?php

use Readdle\BrazeClient\Response;

function getApiKey(): string
{
    return '';
}

function createAliasName(string $bundleId, string $receiptId, ?string $transactionId): string
{
    return strtolower($bundleId . '-' . ($transactionId ?? $receiptId));
}

function logErrorsOrWarnings(Response $response): void
{
    $fatalErrors = $response->getFatalErrors();
    if ($fatalErrors) {
        echo 'Fatal Errors: ' . PHP_EOL;
        print_r($fatalErrors);
    }

    $minorErrors = $response->getMinorErrors();
    if ($minorErrors) {
        echo 'Minor Errors: ' . PHP_EOL;
        print_r($minorErrors);
    }

    echo 'Status Code:' . $response->getStatusCode() . PHP_EOL;
}

function waitFor(string $message, int $seconds = 30): void
{
    echo $message . '...' . PHP_EOL;
    sleep($seconds);
}

class Helper
{
    public static function getUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}