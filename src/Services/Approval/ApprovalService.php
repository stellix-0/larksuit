<?php

namespace Jeulia\Larksuit\Services\Approval;

use Jeulia\Larksuit\Exception\LarkException;
use Jeulia\Larksuit\Services\BaseService;

/**
 * Service for approval-related API endpoints
 */
class ApprovalService extends BaseService
{


    /**
     * Get approval definition
     *
     * @param  string $approvalCode approval code
     * @return array
     * @throws LarkException
     */
    public function getDefinition(string $approvalCode): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get('/approval/v4/definitions/'.$approvalCode, [], $headers);

    }


    /**
     * Get approval definition list
     *
     * @param  int    $pageSize  Page size
     * @param  string $pageToken Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function getDefinitionList(int $pageSize=100, string $pageToken=''): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = ['page_size' => $pageSize];

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        return $httpClient->get('/approval/v4/definitions', $params, $headers);

    }


    /**
     * Create approval instance
     *
     * @param  string $approvalCode approval code
     * @param  array  $instanceData Instance data
     * @return array
     * @throws LarkException
     */
    public function createInstance(string $approvalCode, array $instanceData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/approval/v4/instances', array_merge(
                ['approval_code' => $approvalCode], $instanceData
            ), $headers
        );

    }


    /**
     * Get approval instance
     *
     * @param  string $instanceId Instance ID
     * @param  string $locale     Locale (zh-CN, en-US, ja-JP)
     * @return array
     * @throws LarkException
     */
    public function getInstance(string $instanceId, string $locale='zh-CN'): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get(
            '/approval/v4/instances/'.$instanceId, ['locale' => $locale], $headers
        );

    }


    /**
     * Search approval instances
     *
     * @param  array  $searchParams Search parameters
     * @param  int    $pageSize     Page size
     * @param  string $pageToken    Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function searchInstances(array $searchParams, int $pageSize=100, string $pageToken=''): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = array_merge(
            ['page_size' => $pageSize], $searchParams
        );

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        return $httpClient->post('/approval/v4/instances/search', $params, $headers);

    }


    /**
     * Approve an instance task
     *
     * @param  string $instanceId  Instance ID
     * @param  string $taskId      Task ID
     * @param  string $userId      User ID
     * @param  array  $approveData approval data
     * @return array
     * @throws LarkException
     */
    public function approveTask(string $instanceId, string $taskId, string $userId, array $approveData=[]): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/approval/v4/instances/'.$instanceId.'/approve', array_merge(
                [
                    'task_id' => $taskId,
                    'user_id' => $userId,
                ], $approveData
            ), $headers
        );

    }


    /**
     * Reject an instance task
     *
     * @param  string $instanceId Instance ID
     * @param  string $taskId     Task ID
     * @param  string $userId     User ID
     * @param  array  $rejectData Rejection data
     * @return array
     * @throws LarkException
     */
    public function rejectTask(string $instanceId, string $taskId, string $userId, array $rejectData=[]): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post(
            '/approval/v4/instances/'.$instanceId.'/reject', array_merge(
                [
                    'task_id' => $taskId,
                    'user_id' => $userId,
                ], $rejectData
            ), $headers
        );

    }


    /**
     * Cancel an approval instance
     *
     * @param  string $instanceId Instance ID
     * @param  string $userId     User ID
     * @param  string $reason     Cancellation reason
     * @return array
     * @throws LarkException
     */
    public function cancelInstance(string $instanceId, string $userId, string $reason=''): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $data = ['user_id' => $userId];

        if ($reason) {
            $data['reason'] = $reason;
        }

        return $httpClient->post('/approval/v4/instances/'.$instanceId.'/cancel', $data, $headers);

    }


}
