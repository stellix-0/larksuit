<?php

namespace Jeulia\Larksuit\Auth\Cache;

class FileTokenCache  implements TokenCacheInterface
{

    /**
     * @var string
     */
    private $cacheDir;


    /**
     * FileTokenCache constructor
     *
     * @param string $cacheDir
     */
    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }

    }


    /**
     * {@inheritdoc}
     */
    public function get(string $key): ?array
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        $token   = json_decode($content, true);

        if (!$token || !isset($token['token']) || !isset($token['expires_at'])) {
            return null;
        }

        return $token;

    }


    /**
     * {@inheritdoc}
     */
    public function set(string $key, array $token): bool
    {
        $file = $this->getFilePath($key);
        return file_put_contents($file, json_encode($token)) !== false;

    }


    /**
     * {@inheritdoc}
     */
    public function remove(string $key): bool
    {
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;

    }


    /**
     * Get the file path for a token key
     *
     * @param  string $key
     * @return string
     */
    private function getFilePath(string $key): string
    {
        return $this->cacheDir.'/'.md5($key).'.json';

    }


}
