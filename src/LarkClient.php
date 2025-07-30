<?php

namespace Jeulia\Larksuit;

use Jeulia\Larksuit\Auth\AuthManager;
use Jeulia\Larksuit\Config\LarkConfig;
use Jeulia\Larksuit\Http\HttpClient;
use Jeulia\Larksuit\Services\Approval\ApprovalService;
use Jeulia\Larksuit\Services\Auth\AuthService;
use Jeulia\Larksuit\Services\Calendar\CalendarService;
use Jeulia\Larksuit\Services\Contact\ContactService;
use Jeulia\Larksuit\Services\Drive\DriveService;
use Jeulia\Larksuit\Services\Message\MessageService;

/**
 * Main client class for the Lark SDK
 */
class LarkClient
{

    /**
     * @var LarkConfig
     */
    private $config;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var AuthManager
     */
    private $authManager;

    /**
     * @var array
     */
    private $services = [];


    /**
     * LarkClient constructor
     *
     * @param string $appId     Application ID
     * @param string $appSecret Application Secret
     * @param array  $options   Additional configuration options
     */
    public function __construct(string $appId, string $appSecret, array $options=[])
    {
        $this->config = new LarkConfig(
            array_merge(
                [
                    'app_id'     => $appId,
                    'app_secret' => $appSecret,
                ], $options
            )
        );

        $this->httpClient  = new HttpClient($this->config);
        $this->authManager = new AuthManager($this->httpClient, $this->config);

    }


    /**
     * Get the auth service
     *
     * @return AuthService
     */
    public function auth(): AuthService
    {
        return $this->getService('auth', AuthService::class);

    }


    /**
     * Get the message service
     *
     * @return MessageService
     */
    public function message(): MessageService
    {
        return $this->getService('message', MessageService::class);

    }


    /**
     * Get the contact service
     *
     * @return ContactService
     */
    public function contact(): ContactService
    {
        return $this->getService('contact', ContactService::class);

    }


    /**
     * Get the calendar service
     *
     * @return CalendarService
     */
    public function calendar(): CalendarService
    {
        return $this->getService('calendar', CalendarService::class);

    }


    /**
     * Get the drive service
     *
     * @return DriveService
     */
    public function drive(): DriveService
    {
        return $this->getService('drive', DriveService::class);

    }


    /**
     * Get the approval service
     *
     * @return ApprovalService
     */
    public function approval(): ApprovalService
    {
        return $this->getService('approval', ApprovalService::class);

    }


    /**
     * Get the HTTP client
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;

    }


    /**
     * Get the auth manager
     *
     * @return AuthManager
     */
    public function getAuthManager(): AuthManager
    {
        return $this->authManager;

    }


    /**
     * Get the configuration
     *
     * @return LarkConfig
     */
    public function getConfig(): LarkConfig
    {
        return $this->config;

    }


    /**
     * Get a service instance
     *
     * @param  string $name  Service name
     * @param  string $class Service class
     * @return mixed
     */
    private function getService(string $name, string $class)
    {
        if (!isset($this->services[$name])) {
            $this->services[$name] = new $class($this);
        }

        return $this->services[$name];

    }


}
