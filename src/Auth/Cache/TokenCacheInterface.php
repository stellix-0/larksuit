<?php

namespace Jeulia\Larksuit\Auth\Cache;

/**
 * Interface for token cache implementations
 */
interface TokenCacheInterface
{


    /**
     * Get a cached token
     *
     * @param  string $key
     * @return array|null
     */
    public function get(string $key): ?array;


    /**
     * Set a token in the cache
     *
     * @param  string $key
     * @param  array  $token
     * @return bool
     */
    public function set(string $key, array $token): bool;


    /**
     * Remove a token from the cache
     *
     * @param  string $key
     * @return bool
     */
    public function remove(string $key): bool;


}