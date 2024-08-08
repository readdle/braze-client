<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Endpoint;

use Readdle\BrazeClient\Response;

class Messages extends BaseEndpoint
{
    /**
     * @link https://www.braze.com/docs/api/endpoints/messaging/send_messages/post_send_messages
     */
    public function send(array $payload): Response
    {
        return $this->request('/messages/send', 'POST', $payload);
    }
}
