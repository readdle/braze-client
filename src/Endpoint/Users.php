<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Endpoint;

use Readdle\BrazeClient\Response;

class Users extends BaseEndpoint
{
    /**
     * @link https://www.braze.com/docs/api/endpoints/export/user_data/post_users_identifier/
     */
    public function byIdentifier(array $payload): Response
    {
        return $this->request('/users/export/ids', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_user_alias/
     */
    public function newAlias(array $payload): Response
    {
        return $this->request('/users/alias/new', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_user_identify/
     */
    public function identify(array $payload): Response
    {
        return $this->request('/users/identify', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_user_track/
     */
    public function track(array $payload): Response
    {
        return $this->request('/users/track', 'POST', $payload);
    }
}
