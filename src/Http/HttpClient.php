<?php

namespace Jeulia\Larksuit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Jeulia\Larksuit\Config\LarkConfig;
use Jeulia\Larksuit\Exception\LarkException;
use Jeulia\Larksuit\Exception\NetworkException;
use Jeulia\Larksuit\Exception\RateLimitException;
use Jeulia\Larksuit\Exception\ServerException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * HTTP client for Lark API requests
 */
class HttpClient
{

    /**
     * @var LarkConfig
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * HttpClient constructor
     *
     * @param LarkConfig           $config
     * @param LoggerInterface|null $logger
     */
    public function __construct(LarkConfig $config, ?LoggerInterface $logger=null)
    {
        $this->config = $config;
        $this->logger = ($logger ?? new NullLogger());

        $stack = HandlerStack::create();

        // Add retry middleware if configured
        if ($this->config->get('retry_count') > 0) {
            $stack->push($this->retryMiddleware());
        }

        $this->client = new Client(
            [
                'base_uri' => $this->config->get('base_url'),
                'timeout'  => $this->config->get('timeout'),
                'handler'  => $stack,
                'headers'  => [
                    'User-Agent'   => $this->config->get('default_user_agent'),
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
            ]
        );

    }


    /**
     * Send a GET request
     *
     * @param  string $uri
     * @param  array  $query
     * @param  array  $headers
     * @return array
     * @throws LarkException
     */
    public function get(string $uri, array $query=[], array $headers=[]): array
    {
        return $this->request('GET', $uri, ['query' => $query, 'headers' => $headers]);

    }


    /**
     * Send a POST request
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     * @return array
     * @throws LarkException
     */
    public function post(string $uri, array $data=[], array $headers=[]): array
    {
        return $this->request(
            'POST', $uri, [
                'json'    => $data,
                'headers' => $headers,
            ]
        );

    }


    /**
     * Send a PUT request
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     * @return array
     * @throws LarkException
     */
    public function put(string $uri, array $data=[], array $headers=[]): array
    {
        return $this->request(
            'PUT', $uri, [
                'json'    => $data,
                'headers' => $headers,
            ]
        );

    }


    /**
     * Send a PATCH request
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     * @return array
     * @throws LarkException
     */
    public function patch(string $uri, array $data=[], array $headers=[]): array
    {
        return $this->request(
            'PATCH', $uri, [
                'json'    => $data,
                'headers' => $headers,
            ]
        );

    }


    /**
     * Send a DELETE request
     *
     * @param  string $uri
     * @param  array  $query
     * @param  array  $headers
     * @return array
     * @throws LarkException
     */
    public function delete(string $uri, array $query=[], array $headers=[]): array
    {
        return $this->request('DELETE', $uri, ['query' => $query, 'headers' => $headers]);

    }


    /**
     * Send a request
     *
     * @param  string $method
     * @param  string $uri
     * @param  array  $options
     * @return array
     * @throws LarkException
     */
    public function request(string $method, string $uri, array $options=[]): array
    {
        try {
            $options = $this->prepareOptions($options);

            if ($this->config->get('debug')) {
                $this->logger->debug(
                    'Lark API Request', [
                        'method'  => $method,
                        'uri'     => $uri,
                        'options' => $options,
                    ]
                );
            }

            $response = $this->client->request($method, ltrim($uri, '/'), $options);
            $contents = $response->getBody()->getContents();
            $result   = json_decode($contents, true);

            if ($this->config->get('debug')) {
                $this->logger->debug(
                    'Lark API Response', [
                        'status' => $response->getStatusCode(),
                        'body'   => $contents,
                    ]
                );
            }

            return $this->handleResponse($result);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (GuzzleException $e) {
            throw new NetworkException('Network error: '.$e->getMessage(), 0, $e);
        }//end try

    }


    /**
     * Get the Guzzle client
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;

    }


    /**
     * Handle the API response
     *
     * @param  array|null $result
     * @return array
     * @throws LarkException
     */
    private function handleResponse(?array $result): array
    {
        if (!is_array($result)) {
            throw new ServerException('Invalid JSON response from server');
        }

        $code = ($result['code'] ?? 0);

        if ($code !== 0) {
            $message = ($result['msg'] ?? 'Unknown error');

            if ($code === 99991663) {
                throw new RateLimitException('Rate limit exceeded: '.$message, $code);
            }

            throw new LarkException($message, $code);
        }

        return ($result['data'] ?? $result);

    }


    /**
     * Handle request exceptions
     *
     * @param  RequestException $e
     * @return array
     * @throws LarkException
     */
    private function handleRequestException(RequestException $e): array
    {
        $response = $e->getResponse();

        if ($response) {
            $contents = $response->getBody()->getContents();
            $result   = json_decode($contents, true);

            if ($this->config->get('debug')) {
                $this->logger->error(
                    'Lark API Error', [
                        'status' => $response->getStatusCode(),
                        'body'   => $contents,
                    ]
                );
            }

            if (is_array($result)) {
                $code    = ($result['code'] ?? 0);
                $message = ($result['msg'] ?? 'Unknown error');

                if ($response->getStatusCode() === 429) {
                    throw new RateLimitException('Rate limit exceeded: '.$message, $code);
                }

                if ($response->getStatusCode() >= 500) {
                    throw new ServerException('Server error: '.$message, $code);
                }

                throw new LarkException($message, $code);
            }
        }//end if

        throw new NetworkException('Network error: '.$e->getMessage(), 0, $e);

    }


    /**
     * Prepare request options
     *
     * @param  array $options
     * @return array
     */
    private function prepareOptions(array $options): array
    {
        // Merge headers
        if (isset($options['headers']) && is_array($options['headers'])) {
            $options['headers'] = array_merge(
                ($this->client->getConfig('headers') ?? []),
                $options['headers']
            );
        }

        return $options;

    }


    /**
     * Create a retry middleware
     *
     * @return callable
     */
    private function retryMiddleware(): callable
    {
        return Middleware::retry(
            function ($retries, Request $request, Response $response=null, $exception=null) {
                // Retry on rate limits or server errors
                if ($response) {
                    $statusCode = $response->getStatusCode();

                    if ($statusCode === 429 || $statusCode >= 500) {
                        return $retries < $this->config->get('retry_count');
                    }
                }

                // Retry on network errors
                if ($exception && $exception->getPrevious() instanceof \Exception) {
                    return $retries < $this->config->get('retry_count');
                }

                return false;
            },
            fn() => $this->config->get('retry_delay'),
        );

    }


}
