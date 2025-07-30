<?php

namespace Jeulia\Larksuit\Services\Auth;

use Jeulia\Larksuit\Exception\AuthException;
use Jeulia\Larksuit\Services\BaseService;

/**
 * Service for authentication-related API endpoints
 */
class AuthService extends BaseService
{


    /**
     * Get app access token
     *
     * @param  bool $forceRefresh
     * @return string
     * @throws AuthException
     */
    public function getAppAccessToken(bool $forceRefresh=false): string
    {
        return $this->client->getAuthManager()->getAppAccessToken($forceRefresh);

    }


    /**
     * Get tenant access token
     *
     * @param  bool $forceRefresh
     * @return string
     * @throws AuthException
     */
    public function getTenantAccessToken(bool $forceRefresh=false): string
    {
        return $this->client->getAuthManager()->getTenantAccessToken($forceRefresh);

    }


    /**
     * Exchange authorization code for user access token
     *
     * @param  string $code
     * @return array
     * @throws AuthException
     */
    public function getUserAccessToken(string $code): array
    {
        return $this->client->getAuthManager()->getUserAccessToken($code);

    }


    /**
     * Refresh user access token
     *
     * @param  string $refreshToken
     * @return array
     * @throws AuthException
     */
    public function refreshUserAccessToken(string $refreshToken): array
    {
        return $this->client->getAuthManager()->refreshUserAccessToken($refreshToken);

    }


    /**
     * Get authorization URL
     *
     * @param  string $redirectUri
     * @param  string $state
     * @return string
     */
    public function getAuthorizationUrl(string $redirectUri, string $state=''): string
    {
        $appId = $this->client->getConfig()->get('app_id');

        $params = [
            'app_id'        => $appId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
        ];

        if ($state) {
            $params['state'] = $state;
        }

        return 'https://open.Lark.cn/open-apis/authen/v1/index?'.http_build_query($params);

    }


    /**
     * Verify user access token
     *
     * @param  string $accessToken
     * @return array
     */
    public function verifyUserAccessToken(string $accessToken): array
    {
        $httpClient = $this->client->getHttpClient();

        return $httpClient->post(
            '/authen/v1/token/verify', [], ['Authorization' => "Bearer {$accessToken}"]
        );

    }


}
