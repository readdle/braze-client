<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Endpoint;

use Readdle\BrazeClient\Braze;
use Readdle\BrazeClient\Response;

abstract class BaseEndpoint
{
    private Braze $braze;

    public function __construct(Braze $braze)
    {
        $this->braze = $braze;
    }

    protected function request(string $path, string $method, array $payload = []): Response
    {
        return $this->braze->executeAPIRequest($path, $method, $payload);
    }
}
