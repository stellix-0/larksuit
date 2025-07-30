<?php

namespace Jeulia\Larksuit\Exception;

use Exception;

/**
 * Base exception class for Lark SDK
 */
class LarkException extends Exception
{

    /**
     * @var array
     */
    protected $data = [];


    /**
     * Set response data
     *
     * @param  array $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;

    }


    /**
     * Get response data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;

    }


}
