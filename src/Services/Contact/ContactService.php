<?php

namespace Jeulia\Larksuit\Services\Contact;

use Jeulia\Larksuit\Exception\LarkException;
use Jeulia\Larksuit\Services\BaseService;

/**
 * Service for contact-related API endpoints
 */
class ContactService extends BaseService
{


    /**
     * Get user information
     *
     * @param  string $userId     User ID
     * @param  string $userIdType ID type (open_id, union_id, user_id)
     * @return array
     * @throws LarkException
     */
    public function getUser(string $userId, string $userIdType='open_id'): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get(
            '/contact/v3/users/'.$userId, ['user_id_type' => $userIdType], $headers
        );

    }


    /**
     * Get user list
     *
     * @param  string $departmentId Department ID
     * @param  int    $pageSize     Page size
     * @param  string $pageToken    Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function getUserList(string $departmentId, int $pageSize=100, string $pageToken=''): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = [
            'department_id' => $departmentId,
            'page_size'     => $pageSize,
        ];

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        return $httpClient->get('/contact/v3/users', $params, $headers);

    }


    /**
     * Create user
     *
     * @param  array $userData User data
     * @return array
     * @throws LarkException
     */
    public function createUser(array $userData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post('/contact/v3/users', $userData, $headers);

    }


    /**
     * Update user
     *
     * @param  string $userId   User ID
     * @param  array  $userData User data
     * @return array
     * @throws LarkException
     */
    public function updateUser(string $userId, array $userData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->patch('/contact/v3/users/'.$userId, $userData, $headers);

    }


    /**
     * Delete user
     *
     * @param  string $userId User ID
     * @return array
     * @throws LarkException
     */
    public function deleteUser(string $userId): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->delete('/contact/v3/users/'.$userId, [], $headers);

    }


    /**
     * Get department
     *
     * @param  string $departmentId Department ID
     * @return array
     * @throws LarkException
     */
    public function getDepartment(string $departmentId): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get('/contact/v3/departments/'.$departmentId, [], $headers);

    }


    /**
     * Get department list
     *
     * @param  string $parentDepartmentId Parent department ID
     * @param  bool   $fetchChild         Fetch child departments
     * @param  int    $pageSize           Page size
     * @param  string $pageToken          Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function getDepartmentList(
        string $parentDepartmentId='0',
        bool $fetchChild=false,
        int $pageSize=100,
        string $pageToken=''
    ): array {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = [
            'parent_department_id' => $parentDepartmentId,
            'fetch_child'          => $fetchChild ? 'true' : 'false',
            'page_size'            => $pageSize,
        ];

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        return $httpClient->get('/contact/v3/departments', $params, $headers);

    }


    /**
     * Create department
     *
     * @param  array $departmentData Department data
     * @return array
     * @throws LarkException
     */
    public function createDepartment(array $departmentData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post('/contact/v3/departments', $departmentData, $headers);

    }


    /**
     * Update department
     *
     * @param  string $departmentId   Department ID
     * @param  array  $departmentData Department data
     * @return array
     * @throws LarkException
     */
    public function updateDepartment(string $departmentId, array $departmentData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->patch('/contact/v3/departments/'.$departmentId, $departmentData, $headers);

    }


    /**
     * Delete department
     *
     * @param  string $departmentId Department ID
     * @return array
     * @throws LarkException
     */
    public function deleteDepartment(string $departmentId): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->delete('/contact/v3/departments/'.$departmentId, [], $headers);

    }


    /**
     * Search users
     *
     * @param  string $query     Search query
     * @param  int    $pageSize  Page size
     * @param  string $pageToken Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function searchUsers(string $query, int $pageSize=20, string $pageToken=''): array
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

        return $httpClient->get('/contact/v3/users/search', $params, $headers);

    }


}
