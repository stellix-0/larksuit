<?php

namespace Jeulia\Larksuit\Services\Message;

use Jeulia\Larksuit\Exception\LarkException;
use Jeulia\Larksuit\Services\BaseService;

/**
 * Service for message-related API endpoints
 */
class MessageService extends BaseService
{


    /**
     * Send message to a chat
     *
     * @param  string $receiveId     Receiver ID
     * @param  string $msgType       message type
     * @param  array  $content       message content
     * @param  string $receiveIdType Receiver ID type (open_id, user_id, union_id, email, chat_id)
     * @return array
     * @throws LarkException
     */
    public function send(string $receiveId, string $msgType, array $content, string $receiveIdType = 'open_id'): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/im/v1/messages?receive_id_type=' . $receiveIdType,
            [
                'receive_id'      => $receiveId,
                'msg_type'        => $msgType,
                'content'         => json_encode($content),
            ],
            $headers
        );
    }


    /**
     * Send text message
     *
     * @param  string $receiveId     Receiver ID
     * @param  string $text          Text content
     * @param  string $receiveIdType Receiver ID type
     * @return array
     * @throws LarkException
     */
    public function sendText(string $receiveId, string $text, string $receiveIdType = 'open_id'): array
    {
        $content = ['text' => $text];
        return $this->send($receiveId, 'text', $content, $receiveIdType);
    }


    /**
     * Send image message
     *
     * @param  string $receiveId     Receiver ID
     * @param  string $imageKey      Image key
     * @param  string $receiveIdType Receiver ID type
     * @return array
     * @throws LarkException
     */
    public function sendImage(string $receiveId, string $imageKey, string $receiveIdType = 'open_id'): array
    {
        $content = ['image_key' => $imageKey];
        return $this->send($receiveId, 'image', $content, $receiveIdType);
    }


    /**
     * Send interactive message (card)
     *
     * @param  string $receiveId     Receiver ID
     * @param  array  $card          Card content
     * @param  string $receiveIdType Receiver ID type
     * @return array
     * @throws LarkException
     */
    public function sendInteractive(string $receiveId, array $card, string $receiveIdType = 'open_id'): array
    {
        $content = ['elements' => $card];
        return $this->send($receiveId, 'interactive', $content, $receiveIdType);
    }


    /**
     * Get message list
     *
     * @param  string $chatId    Chat ID
     * @param  int    $limit     Page size
     * @param  string $startTime Start time (RFC3339 format)
     * @param  string $endTime   End time (RFC3339 format)
     * @param  string $messageId message ID for pagination
     * @return array
     * @throws LarkException
     */
    public function getList(
        string $chatId,
        int $limit = 20,
        string $startTime = '',
        string $endTime = '',
        string $messageId = ''
    ): array {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = [
            'container_id_type' => 'chat',
            'container_id'      => $chatId,
            'page_size'         => $limit,
        ];

        if ($messageId) {
            $params['message_id'] = $messageId;
        }

        if ($startTime) {
            $params['start_time'] = $startTime;
        }

        if ($endTime) {
            $params['end_time'] = $endTime;
        }

        return $httpClient->get('/im/v1/messages', $params, $headers);
    }


    /**
     * Get message file
     *
     * @param  string $messageId message ID
     * @param  string $fileKey   File key
     * @return array
     * @throws LarkException
     */
    public function getMessageFile(string $messageId, string $fileKey): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get('/im/v1/messages/' . $messageId . '/resources/' . $fileKey, [], $headers);
    }


    /**
     * Upload image
     *
     * @param  string $imagePath Image file path
     * @param  string $imageType Image type (message, avatar, etc.)
     * @return array
     * @throws LarkException
     */
    public function uploadImage(string $imagePath, string $imageType = 'message'): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        // Create a multipart request
        $file     = fopen($imagePath, 'r');
        $filename = basename($imagePath);

        $multipart = [
            [
                'name'     => 'image_type',
                'contents' => $imageType,
            ],
            [
                'name'     => 'image',
                'contents' => $file,
                'filename' => $filename,
            ],
        ];

        $response = $httpClient->request(
            'POST',
            '/im/v1/images',
            [
                'multipart' => $multipart,
                'headers'   => $headers,
            ]
        );

        fclose($file);

        return $response;
    }


    /**
     * Upload file
     *
     * @param  string $filePath File path
     * @param  string $fileType File type (doc, docx, xls, xlsx, etc.)
     * @param  string $fileName File name (optional)
     * @return array
     * @throws LarkException
     */
    public function uploadFile(string $filePath, string $fileType, string $fileName = ''): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        // Create a multipart request
        $file     = fopen($filePath, 'r');
        $fileName = $fileName ?: basename($filePath);

        $multipart = [
            [
                'name'     => 'file_type',
                'contents' => $fileType,
            ],
            [
                'name'     => 'file_name',
                'contents' => $fileName,
            ],
            [
                'name'     => 'file',
                'contents' => $file,
                'filename' => $fileName,
            ],
        ];

        $response = $httpClient->request(
            'POST',
            '/im/v1/files',
            [
                'multipart' => $multipart,
                'headers'   => $headers,
            ]
        );

        fclose($file);

        return $response;
    }
}
