<?php

namespace Jeulia\Larksuit\Webhook;

use Jeulia\Larksuit\LarkClient;
use Jeulia\Larksuit\Exception\LarkException;

class EventHandler
{
     /**
     * @var LarkClient
     */
    private $client;

    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @var callable|null
     */
    private $defaultListener = null;

    /**
     * EventHandler constructor
     *
     * @param LarkClient $client
     */
    public function __construct(LarkClient $client)
    {
        $this->client = $client;
    }

    /**
     * Register an event listener
     *
     * @param string $eventType Event type
     * @param callable $callback Callback function
     * @return $this
     */
    public function on(string $eventType, callable $callback): self
    {
        $this->listeners[$eventType] = $callback;
        return $this;
    }

    /**
     * Register a default event listener
     *
     * @param callable $callback Callback function
     * @return $this
     */
    public function onDefault(callable $callback): self
    {
        $this->defaultListener = $callback;
        return $this;
    }

    /**
     * Handle an event
     *
     * @param array $event Event data
     * @return bool
     * @throws LarkException
     */
    public function handle(array $event): bool
    {
        // Handle challenge verification
        if (isset($event['challenge'])) {
            return true;
        }
        
        // Verify event
        if (!$this->verifyEvent($event)) {
            throw new LarkException('Event verification failed');
        }
        
        // Get event type
        $eventType = $event['event']['type'] ?? null;
        
        if (!$eventType) {
            throw new LarkException('Invalid event data: missing event_type');
        }
        
        // Find a listener for the event type
        if (isset($this->listeners[$eventType])) {
            call_user_func($this->listeners[$eventType], $event['event']);
            return true;
        }
        
        // Use default listener if no specific listener is found
        if ($this->defaultListener) {
            call_user_func($this->defaultListener, $event);
            return true;
        }
        
        return false;
    }

    /**
     * Verify event signature
     *
     * @param array $event Event data
     * @return bool
     */
    private function verifyEvent(array $event): bool
    {
        // If verification token is provided, use it for verification
        $token = $this->client->getConfig()->get('verification_token');
        
        if ($token) {
            $eventToken = $event['header']['token'] ?? '';
            return $token === $eventToken;
        }
        
        // If encrypt key is provided, verify signature
        $encryptKey = $this->client->getConfig()->get('encrypt_key');
        
        if ($encryptKey && isset($event['header']['signature'])) {
            $timestamp = $event['header']['timestamp'] ?? '';
            $nonce = $event['header']['nonce'] ?? '';
            $signature = $event['header']['signature'] ?? '';
            
            $signString = $timestamp . $nonce . $encryptKey;
            $expectedSignature = hash_hmac('sha256', $signString, $encryptKey);
            
            return $signature === $expectedSignature;
        }
        
        // If no verification method is configured, return true
        return true;
    }
}