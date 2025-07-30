<?php

namespace Jeulia\Larksuit\Auth\Cache;

/**
 * In-memory token cache implementation
 */
class MemoryTokenCache implements TokenCacheInterface
{

    /**
     * @var array
     */
    private $cache = [];


    /**
     * {@inheritdoc}
     */
    public function get(string $key): ?array
    {
        return $this->cache[$key] ?? null;

    }


    /**
     * {@inheritdoc}
     */
    public function set(string $key, array $token): bool
    {
        $this->cache[$key] = $token;
        return true;

    }


    /**
     * {@inheritdoc}
     */
    public function remove(string $key): bool
    {
        unset($this->cache[$key]);
        return true;

    }


}
