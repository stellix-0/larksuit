<?php

namespace Jeulia\Larksuit\Services\Drive;

use Jeulia\Larksuit\Exception\LarkException;
use Jeulia\Larksuit\Services\BaseService;

/**
 * Service for drive-related API endpoints
 */
class DriveService extends BaseService
{


    /**
     * Create a folder
     *
     * @param  string $name        Folder name
     * @param  string $parentToken Parent folder token
     * @param  string $parentType  Parent folder type (explorer, folder)
     * @return array
     * @throws LarkException
     */
    public function createFolder(string $name, string $parentToken, string $parentType='explorer'): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/drive/explorer/v2/folder/create', [
                'name'         => $name,
                'parent_token' => $parentToken,
                'parent_type'  => $parentType,
            ], $headers
        );

    }


    /**
     * Get metadata
     *
     * @param  string $fileToken File token
     * @return array
     * @throws LarkException
     */
    public function getMetadata(string $fileToken): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get('/drive/v1/files/'.$fileToken.'/meta', [], $headers);

    }


    /**
     * Upload file
     *
     * @param  string $parentToken Parent folder token
     * @param  string $filePath    File path
     * @param  string $fileName    File name (optional)
     * @param  string $parentType  Parent folder type (explorer, folder)
     * @return array
     * @throws LarkException
     */
    public function uploadFile(
        string $parentToken,
        string $filePath,
        string $fileName='',
        string $parentType='explorer'
    ): array {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        // Step 1: Get upload URL
        $uploadResp = $httpClient->post(
            '/drive/v1/upload_prepare', [
                'parent_token' => $parentToken,
                'parent_type'  => $parentType,
                'file_name'    => $fileName ?: basename($filePath),
                'size'         => filesize($filePath),
            ], $headers
        );

        if (empty($uploadResp['upload_id']) || empty($uploadResp['upload_url'])) {
            throw new LarkException('Failed to get upload URL');
        }

        // Step 2: Upload the file
        $file          = fopen($filePath, 'r');
        $uploadUrl     = $uploadResp['upload_url'];
        $uploadHeaders = ['Content-Type' => 'application/octet-stream'];

        $guzzleClient = $this->client->getHttpClient()->getClient();
        $response     = $guzzleClient->post(
            $uploadUrl, [
                'headers' => $uploadHeaders,
                'body'    => $file,
            ]
        );

        fclose($file);

        // Step 3: Complete the upload
        return $httpClient->post(
            '/drive/v1/upload_finish', [
                'upload_id'    => $uploadResp['upload_id'],
                'file_name'    => $fileName ?: basename($filePath),
                'parent_token' => $parentToken,
                'parent_type'  => $parentType,
                'size'         => filesize($filePath),
            ], $headers
        );

    }


    /**
     * List files
     *
     * @param  string $parentToken Parent folder token
     * @param  string $parentType  Parent folder type (explorer, folder)
     * @param  int    $pageSize    Page size
     * @param  string $pageToken   Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function listFiles(
        string $parentToken,
        string $parentType='explorer',
        int $pageSize=100,
        string $pageToken=''
    ): array {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = [
            'parent_token' => $parentToken,
            'parent_type'  => $parentType,
            'page_size'    => $pageSize,
        ];

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        return $httpClient->get('/drive/explorer/v2/folder/children', $params, $headers);

    }


    /**
     * Delete file
     *
     * @param  string $fileToken File token
     * @param  string $fileType  File type (file, docx, sheet, slide, etc.)
     * @return array
     * @throws LarkException
     */
    public function deleteFile(string $fileToken, string $fileType='file'): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/drive/explorer/v2/file/delete', [
                'token' => $fileToken,
                'type'  => $fileType,
            ], $headers
        );

    }


    /**
     * Copy file
     *
     * @param  string $fileToken         File token
     * @param  string $name              New file name
     * @param  string $fileType          File type (file, docx, sheet, slide, etc.)
     * @param  string $targetParentToken Target parent folder token
     * @param  string $targetParentType  Target parent folder type (explorer, folder)
     * @return array
     * @throws LarkException
     */
    public function copyFile(
        string $fileToken,
        string $name,
        string $fileType,
        string $targetParentToken,
        string $targetParentType='explorer'
    ): array {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/drive/explorer/v2/file/copy', [
                'token'               => $fileToken,
                'type'                => $fileType,
                'name'                => $name,
                'target_parent_token' => $targetParentToken,
                'target_parent_type'  => $targetParentType,
            ], $headers
        );

    }


    /**
     * Move file
     *
     * @param  string $fileToken         File token
     * @param  string $fileType          File type (file, docx, sheet, slide, etc.)
     * @param  string $targetParentToken Target parent folder token
     * @param  string $targetParentType  Target parent folder type (explorer, folder)
     * @return array
     * @throws LarkException
     */
    public function moveFile(
        string $fileToken,
        string $fileType,
        string $targetParentToken,
        string $targetParentType='explorer'
    ): array {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/drive/explorer/v2/file/move', [
                'token'               => $fileToken,
                'type'                => $fileType,
                'target_parent_token' => $targetParentToken,
                'target_parent_type'  => $targetParentType,
            ], $headers
        );

    }


    /**
     * Search files
     *
     * @param  string $query     Search query
     * @param  int    $pageSize  Page size
     * @param  string $pageToken Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function searchFiles(string $query, int $pageSize=100, string $pageToken=''): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = [
            'query'     => $query,
            'page_size' => $pageSize,
        ];

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        return $httpClient->get('/drive/explorer/v2/file/search', $params, $headers);

    }


}
