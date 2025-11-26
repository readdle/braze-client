<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Endpoint;

use Readdle\BrazeClient\Response;
use stdClass;

class Messages extends BaseEndpoint
{
    /**
     * @link https://www.braze.com/docs/api/endpoints/messaging/send_messages/post_send_messages
     */
    public function send(array|stdClass $payload): Response
    {
        return $this->request('/messages/send', 'POST', $payload);
    }
}
