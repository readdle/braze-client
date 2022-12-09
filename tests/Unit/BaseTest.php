<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function createTestClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        return new Client([
            'handler' => HandlerStack::create($mock)
        ]);
    }

    protected function loadFixture(string $pathToFile): string
    {
        $fullPath = TESTS_ROOT . '/fixtures/' . $pathToFile . '.json';
        return file_get_contents($fullPath);
    }
}
