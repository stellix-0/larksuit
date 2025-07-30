<?php

namespace Jeulia\Larksuit\Services\Calendar;

use Jeulia\Larksuit\Exception\LarkException;
use Jeulia\Larksuit\Services\BaseService;

/**
 * Service for calendar-related API endpoints
 */
class CalendarService extends BaseService
{


    /**
     * Get calendar list
     *
     * @param  int    $pageSize  Page size
     * @param  string $pageToken Page token for pagination
     * @param  string $syncToken Sync token for incremental sync
     * @return array
     * @throws LarkException
     */
    public function getCalendarList(int $pageSize=100, string $pageToken='', string $syncToken=''): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = ['page_size' => $pageSize];

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        if ($syncToken) {
            $params['sync_token'] = $syncToken;
        }

        return $httpClient->get('/calendar/v4/calendars', $params, $headers);

    }


    /**
     * Get calendar by ID
     *
     * @param  string $calendarId calendar ID
     * @return array
     * @throws LarkException
     */
    public function getCalendar(string $calendarId): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get('/calendar/v4/calendars/'.$calendarId, [], $headers);

    }


    /**
     * Create calendar
     *
     * @param  array $calendarData calendar data
     * @return array
     * @throws LarkException
     */
    public function createCalendar(array $calendarData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post('/calendar/v4/calendars', $calendarData, $headers);

    }


    /**
     * Update calendar
     *
     * @param  string $calendarId   calendar ID
     * @param  array  $calendarData calendar data
     * @return array
     * @throws LarkException
     */
    public function updateCalendar(string $calendarId, array $calendarData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->patch('/calendar/v4/calendars/'.$calendarId, $calendarData, $headers);

    }


    /**
     * Delete calendar
     *
     * @param  string $calendarId calendar ID
     * @return array
     * @throws LarkException
     */
    public function deleteCalendar(string $calendarId): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->delete('/calendar/v4/calendars/'.$calendarId, [], $headers);

    }


    /**
     * Get event list
     *
     * @param  string $calendarId calendar ID
     * @param  string $startTime  Start time (RFC3339 format)
     * @param  string $endTime    End time (RFC3339 format)
     * @param  int    $pageSize   Page size
     * @param  string $pageToken  Page token for pagination
     * @return array
     * @throws LarkException
     */
    public function getEventList(
        string $calendarId,
        string $startTime,
        string $endTime,
        int $pageSize=100,
        string $pageToken=''
    ): array {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        $params = [
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'page_size'  => $pageSize,
        ];

        if ($pageToken) {
            $params['page_token'] = $pageToken;
        }

        return $httpClient->get('/calendar/v4/calendars/'.$calendarId.'/events', $params, $headers);

    }


    /**
     * Get event by ID
     *
     * @param  string $calendarId calendar ID
     * @param  string $eventId    Event ID
     * @return array
     * @throws LarkException
     */
    public function getEvent(string $calendarId, string $eventId): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->get('/calendar/v4/calendars/'.$calendarId.'/events/'.$eventId, [], $headers);

    }


    /**
     * Create event
     *
     * @param  string $calendarId calendar ID
     * @param  array  $eventData  Event data
     * @return array
     * @throws LarkException
     */
    public function createEvent(string $calendarId, array $eventData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post('/calendar/v4/calendars/'.$calendarId.'/events', $eventData, $headers);

    }


    /**
     * Update event
     *
     * @param  string $calendarId calendar ID
     * @param  string $eventId    Event ID
     * @param  array  $eventData  Event data
     * @return array
     * @throws LarkException
     */
    public function updateEvent(string $calendarId, string $eventId, array $eventData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->patch('/calendar/v4/calendars/'.$calendarId.'/events/'.$eventId, $eventData, $headers);

    }


    /**
     * Delete event
     *
     * @param  string $calendarId calendar ID
     * @param  string $eventId    Event ID
     * @return array
     * @throws LarkException
     */
    public function deleteEvent(string $calendarId, string $eventId): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->delete('/calendar/v4/calendars/'.$calendarId.'/events/'.$eventId, [], $headers);

    }


    /**
     * Subscribe calendar
     *
     * @param  array $subscriptionData Subscription data
     * @return array
     * @throws LarkException
     */
    public function subscribeCalendar(array $subscriptionData): array
    {
        $httpClient = $this->client->getHttpClient();
        $headers    = $this->getTenantAccessTokenHeader();

        return $httpClient->post('/calendar/v4/calendars/subscribe', $subscriptionData, $headers);

    }


}
