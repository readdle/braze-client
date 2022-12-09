<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Test\Unit\Endpoint;

use GuzzleHttp\Psr7\Response;
use Readdle\BrazeClient\Braze;
use Readdle\BrazeClient\Test\Unit\BaseTest;

class UsersTest extends BaseTest
{
    public function testIdentifyWithMinorError()
    {
        $body = $this->loadFixture('users/identify_minor_error');
        $responses = [
            new Response(201, [], $body)
        ];

        $braze = new Braze('api-key', 'endpoint', $this->createTestClient($responses));
        $response = $braze->users()->identify([
            'user_aliases' => [
                [
                    'user_alias' => [
                        'alias_name' => 'name',
                        'alias_label' => 'label'
                    ]
                ]
            ],
        ]);

        $this->assertFalse($response->isSucceed());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNull($response->getFatalErrors());
        $this->assertEquals([
            [
                'type' => "'alias_name' must be a String",
                'input_array' => 'user_aliases',
                'index' => 0
            ]
        ], $response->getMinorErrors());
        $this->assertEquals(json_decode($body, true), $response->getPayload());
    }

    public function testIdentifySuccess()
    {
        $body = $this->loadFixture('users/identify_success_response');
        $responses = [
            new Response(201, [], $body)
        ];

        $braze = new Braze('api-key', 'endpoint', $this->createTestClient($responses));
        $response = $braze->users()->identify([
            'user_aliases' => [
                'alias_name' => 'b-2222',
                'alias_label' => 'AppStore'
            ]
        ]);

        $this->assertTrue($response->isSucceed());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNull($response->getFatalErrors());
        $this->assertNull($response->getMinorErrors());
        $this->assertEquals(json_decode($body, true), $response->getPayload());
    }
}
