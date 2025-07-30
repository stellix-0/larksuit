<?php

namespace Jeulia\Larksuit\Auth;

use Jeulia\Larksuit\Auth\Cache\FileTokenCache;
use Jeulia\Larksuit\Auth\Cache\MemoryTokenCache;
use Jeulia\Larksuit\Auth\Cache\TokenCacheInterface;
use Jeulia\Larksuit\Config\LarkConfig;
use Jeulia\Larksuit\Exception\AuthException;
use Jeulia\Larksuit\Http\HttpClient;

/**
 * Manages authentication for Lark API
 */
class AuthManager
{

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var LarkConfig
     */
    private $config;

    /**
     * @var TokenCacheInterface
     */
    private $tokenCache;

    /**
     * @var array
     */
    private $tokens = [];


    /**
     * AuthManager constructor
     *
     * @param HttpClient $httpClient
     * @param LarkConfig $config
     */
    public function __construct(HttpClient $httpClient, LarkConfig $config)
    {
        $this->httpClient = $httpClient;
        $this->config     = $config;
        $this->tokenCache = $this->initializeTokenCache();

    }


    /**
     * Get app access token
     *
     * @param  bool $forceRefresh Force token refresh
     * @return string
     * @throws AuthException
     */
    public function getAppAccessToken(bool $forceRefresh=false): string
    {
        return $this->getToken('app_access_token', $forceRefresh);

    }


    /**
     * Get tenant access token
     *
     * @param  bool $forceRefresh Force token refresh
     * @return string
     * @throws AuthException
     */
    public function getTenantAccessToken(bool $forceRefresh=false): string
    {
        return $this->getToken('tenant_access_token', $forceRefresh);

    }


    /**
     * Get user access token
     *
     * @param  string $code Authorization code
     * @return array
     * @throws AuthException
     */
    public function getUserAccessToken(string $code): array
    {
        $appId     = $this->config->get('app_id');
        $appSecret = $this->config->get('app_secret');

        $result = $this->httpClient->post(
            '/authen/v1/access_token', [
                'grant_type' => 'authorization_code',
                'code'       => $code,
                'app_id'     => $appId,
                'app_secret' => $appSecret,
            ]
        );

        if (empty($result['access_token'])) {
            throw new AuthException('Failed to get user access token');
        }

        return $result;

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
        $appId     = $this->config->get('app_id');
        $appSecret = $this->config->get('app_secret');

        $result = $this->httpClient->post(
            '/authen/v1/refresh_access_token', [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
                'app_id'        => $appId,
                'app_secret'    => $appSecret,
            ]
        );

        if (empty($result['access_token'])) {
            throw new AuthException('Failed to refresh user access token');
        }

        return $result;

    }


    /**
     * Get a token
     *
     * @param  string $tokenType
     * @param  bool   $forceRefresh
     * @return string
     * @throws AuthException
     */
    private function getToken(string $tokenType, bool $forceRefresh=false): string
    {
        // Check memory cache first
        if (!$forceRefresh && isset($this->tokens[$tokenType])) {
            $token = $this->tokens[$tokenType];
            if ($token['expires_at'] > time()) {
                return $token['token'];
            }
        }

        // Check persistent cache
        if (!$forceRefresh) {
            $cachedToken = $this->tokenCache->get($tokenType);
            if ($cachedToken && $cachedToken['expires_at'] > time()) {
                $this->tokens[$tokenType] = $cachedToken;
                return $cachedToken['token'];
            }
        }

        // Fetch new token
        return $this->fetchToken($tokenType);

    }


    /**
     * Fetch a new token
     *
     * @param  string $tokenType
     * @return string
     * @throws AuthException
     */
    private function fetchToken(string $tokenType): string
    {
        $appId     = $this->config->get('app_id');
        $appSecret = $this->config->get('app_secret');

        $endpoint    = '';
        $requestData = [];

        if ($tokenType === 'app_access_token') {
            $endpoint    = '/auth/v3/app_access_token/internal';
            $requestData = [
                'app_id'     => $appId,
                'app_secret' => $appSecret,
            ];
        } else if ($tokenType === 'tenant_access_token') {
            $endpoint    = '/auth/v3/tenant_access_token/internal';
            $requestData = [
                'app_id'     => $appId,
                'app_secret' => $appSecret,
            ];
        } else {
            throw new AuthException("Unsupported token type: {$tokenType}");
        }

        $result = $this->httpClient->post($endpoint, $requestData);

        if (empty($result[$tokenType])) {
            throw new AuthException("Failed to get {$tokenType}");
        }

        $token = [
            'token'      => $result[$tokenType],
            'expires_at' => (time() + $result['expire'] - 60),
        // Buffer of 60 seconds
        ];

        // Save to memory cache
        $this->tokens[$tokenType] = $token;

        // Save to persistent cache
        $this->tokenCache->set($tokenType, $token);

        return $token['token'];

    }


    /**
     * Initialize token cache
     *
     * @return TokenCacheInterface
     */
    private function initializeTokenCache(): TokenCacheInterface
    {
        $cacheType = $this->config->get('token_cache_type');

        if ($cacheType === 'file') {
            return new FileTokenCache($this->config->get('token_cache_path'));
        }

        return new MemoryTokenCache();

    }


}
