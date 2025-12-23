<?php
declare(strict_types=1);

namespace Readdle\BrazeClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Message;
use Readdle\BrazeClient\Endpoint\BaseEndpoint;
use Readdle\BrazeClient\Endpoint\Campaigns;
use Readdle\BrazeClient\Endpoint\Messages;
use Readdle\BrazeClient\Endpoint\Users;
use Readdle\BrazeClient\Exception\BadRequestException;
use Readdle\BrazeClient\Exception\BrazeException;
use Readdle\BrazeClient\Exception\RateLimitException;
use stdClass;

class Braze
{
    public const US01 = 'https://rest.iad-01.braze.com';
    public const US02 = 'https://rest.iad-02.braze.com';
    public const US03 = 'https://rest.iad-03.braze.com';
    public const US04 = 'https://rest.iad-04.braze.com';
    public const US05 = 'https://rest.iad-05.braze.com';
    public const US06 = 'https://rest.iad-06.braze.com';
    public const US07 = 'https://rest.iad-07.braze.com';
    public const US08 = 'https://rest.iad-08.braze.com';
    public const EU01 = 'https://rest.fra-01.braze.eu';
    public const EU02 = 'https://rest.fra-02.braze.eu';

    private Client $client;
    private string $apiKey;
    private string $baseUrl;

    /** @var BaseEndpoint[] */
    private array $endpoints;

    private array $statusCodesToExceptions = [
        'default'   => BadRequestException::class,
        429         => RateLimitException::class,
    ];

    public function __construct(
        string $apiKey,
        string $endpoint = self::EU01,
        ?Client $client = null
    ) {
        $this->apiKey = $apiKey;
        $this->baseUrl = $endpoint;
        $this->client = $client ?? new Client();
    }

    public function users(): Users
    {
        return $this->init(Users::class);
    }

    public function messages(): Messages
    {
        return $this->init(Messages::class);
    }

    public function campaigns(): Campaigns
    {
        return $this->init(Campaigns::class);
    }

    /**
     * @param string $path
     * @param string $method
     * @param array|stdClass $payload
     *
     * @throws BadRequestException
     * @throws RateLimitException
     * @throws BrazeException
     *
     * @return Response
     */
    public function executeAPIRequest(string $path, string $method, array|stdClass|null $payload = null): Response
    {
        $options = [
            'headers' => [
                'Content-Type'      => 'application/json',
                'Authorization'     => 'Bearer ' . $this->apiKey,
            ]
        ];
        if (!empty($payload)) {
            $options['body'] = json_encode($payload);
        }

        $url = $this->baseUrl . $path;
        try {
            $result = $this->client->request($method, $url, $options);
            $payload = (array)json_decode((string)$result->getBody(), true);

            return new Response($payload, $result->getStatusCode());
        } catch (ClientException $e) {
            throw $this->createClientException($e);
        } catch (GuzzleException $e) {
            $exception = new BrazeException(
                'Fail to execute Braze request to: ' . $url . ' ' . $e->getMessage(),
                $e->getCode()
            );

            if (method_exists($e, 'getResponse')) {
                $exception->setBody((string)$e->getResponse()->getBody());
                $exception->setFullResponse(Message::toString($e->getResponse()));
            }
            throw $exception;
        }
    }

    protected function init(string $className)
    {
        if (!isset($this->endpoints[$className])) {
            $this->endpoints[$className] = new $className($this);
        }

        return $this->endpoints[$className];
    }

    private function createClientException(ClientException $e)
    {
        $code = $e->getResponse()->getStatusCode();
        $exceptionName = $this->statusCodesToExceptions[$code] ?? $this->statusCodesToExceptions['default'];

        $url = $e->getRequest()->getUri();
        $exception = new $exceptionName(
            'Client error on execution Braze request to: ' . $url,
            $e->getCode(),
            $e
        );
        $exception->setBody((string)$e->getResponse()->getBody());
        $exception->setFullResponse(Message::toString($e->getResponse()));

        return $exception;
    }
}
