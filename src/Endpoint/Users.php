<?php
declare(strict_types=1);

namespace Readdle\BrazeClient\Endpoint;

use Readdle\BrazeClient\Response;
use stdClass;

class Users extends BaseEndpoint
{
    /**
     * @link https://www.braze.com/docs/api/endpoints/export/user_data/post_users_identifier/
     */
    public function byIdentifier(array|stdClass $payload): Response
    {
        return $this->request('/users/export/ids', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_user_alias/
     */
    public function newAlias(array|stdClass $payload): Response
    {
        return $this->request('/users/alias/new', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_user_identify/
     */
    public function identify(array|stdClass $payload): Response
    {
        return $this->request('/users/identify', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_user_track/
     */
    public function track(array|stdClass $payload): Response
    {
        return $this->request('/users/track', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_user_delete/
     */
    public function delete(array|stdClass $payload): Response
    {
        return $this->request('/users/delete', 'POST', $payload);
    }

    /**
     * @link https://www.braze.com/docs/api/endpoints/user_data/post_users_merge/
     */
    public function merge(array|stdClass $payload): Response
    {
        return $this->request('/users/merge', 'POST', $payload);
    }
}
