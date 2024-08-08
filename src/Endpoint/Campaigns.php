<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Endpoint;

use Readdle\BrazeClient\Response;

class Campaigns extends BaseEndpoint
{
    /**
     * @link https://www.braze.com/docs/api/endpoints/messaging/send_messages/post_send_triggered_campaigns
     */
    public function triggerSend(array $payload): Response
    {
        return $this->request('/campaigns/trigger/send', 'POST', $payload);
    }
}
