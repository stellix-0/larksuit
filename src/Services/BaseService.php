<?php

namespace Jeulia\Larksuit\Services;

use Jeulia\Larksuit\LarkClient;
use Jeulia\Larksuit\Exception\AuthException;

abstract class BaseService
{
    /**
     * @var LarkClient
     */
    protected $client;

    /**
     * BaseService constructor
     *
     * @param LarkClient $client
     */
    public function __construct(LarkClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get app access token header
     *
     * @param bool $forceRefresh
     * @return array
     * @throws AuthException
     */
    protected function getAppAccessTokenHeader(bool $forceRefresh = false): array
    {
        $token = $this->client->getAuthManager()->getAppAccessToken($forceRefresh);
        return ['Authorization' => "Bearer {$token}"];
    }

    /**
     * Get tenant access token header
     *
     * @param bool $forceRefresh
     * @return array
     * @throws AuthException
     */
    protected function getTenantAccessTokenHeader(bool $forceRefresh = false): array
    {
        $token = $this->client->getAuthManager()->getTenantAccessToken($forceRefresh);
        return ['Authorization' => "Bearer {$token}"];
    }

    /**
     * Get the client
     *
     * @return LarkClient
     */
    public function getClient(): LarkClient
    {
        return $this->client;
    }

}