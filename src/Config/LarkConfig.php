<?php

namespace Jeulia\Larksuit\Config;

class LarkConfig
{

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $defaultConfig = [
        'base_url'           => 'https://open.feishu.cn/open-apis/',
        'timeout'            => 30,
        'debug'              => false,
        'log_path'           => null,
        'token_cache_type'   => 'file',
        'token_cache_path'   => null,
        'retry_count'        => 2,
        // in milliseconds
        'retry_delay'        => 1000,
        'default_user_agent' => 'LarkPHPSDK/1.0',
    ];


    /**
     * LarkConfig constructor
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config=[])
    {
        $this->config = array_merge($this->defaultConfig, $config);

        // Set default token cache path if not provided
        if ($this->config['token_cache_type'] === 'file' && $this->config['token_cache_path'] === null) {
            $this->config['token_cache_path'] = sys_get_temp_dir().'/Lark_token_cache';
        }

    }


    /**
     * Get a configuration value
     *
     * @param  string $key     Configuration key
     * @param  mixed  $default Default value if key is not found
     * @return mixed
     */
    public function get(string $key, $default=null)
    {
        return ($this->config[$key] ?? $default);

    }


    /**
     * Set a configuration value
     *
     * @param  string $key   Configuration key
     * @param  mixed  $value Configuration value
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $this->config[$key] = $value;
        return $this;

    }


    /**
     * Check if a configuration key exists
     *
     * @param  string $key Configuration key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->config[$key]);

    }


    /**
     * Get all configuration values
     *
     * @return array
     */
    public function all(): array
    {
        return $this->config;

    }


}
