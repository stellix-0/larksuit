<?php

namespace Jeulia\Larksuit;

use GuzzleHttp\Client;
use Jeulia\Larksuit\Enums\MsgType;

/**
 * Class Robot
 */
class Robot
{
    /** @var string */
    private string $robotWebhook;
    /** @var Client */
    private Client $client;

    /**
     * Robot constructor.
     * @param string $robotWebhook
     */
    public function __construct($robotWebhook)
    {

        $this->robotWebhook = $robotWebhook;
        $this->client = new Client();
    }

    /**
     * @param string $msgType
     * @param $content
     */
    public function send($msgType, $content)
    {
        $this->client->post(
            $this->robotWebhook,
            [
                'json' => [
                    'msg_type' => $msgType,
                    'content'  => [$msgType => $content],
                ],
            ]
        );
    }

    /**
     * Send text message
     * @param string $message
     */
    public function sendTextMessage($message)
    {
        $this->send(MsgType::TEXT->value, $message);
    }

    /**
     * Send post message
     * @param array $post
     */
    public function sendPostMessage($post)
    {
        $this->send(MsgType::POST->value, $post);
    }
}
