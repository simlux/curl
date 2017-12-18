<?php declare(strict_types=1);

namespace Simlux\Curl\Options;

/**
 * Class ConnectionOptions
 *
 * @package Simlux\Curl\Options
 */
class ConnectionOptionBundle extends AbstractOptionBundle
{
    /**
     * @param int $timeout
     *
     * @return ConnectionOptionBundle
     */
    public function timeout(int $timeout): ConnectionOptionBundle
    {
        $this->options[ CURLOPT_TIMEOUT ] = $timeout;

        return $this;
    }

    /**
     * @param int $timeoutMs
     *
     * @return ConnectionOptionBundle
     */
    public function timeoutMs(int $timeoutMs): ConnectionOptionBundle
    {
        $this->options[ CURLOPT_TIMEOUT_MS ] = $timeoutMs;

        return $this;
    }

    /**
     * @param int $connectionTimeout
     *
     * @return ConnectionOptionBundle
     */
    public function connectionTimeout(int $connectionTimeout): ConnectionOptionBundle
    {
        $this->options[ CURLOPT_CONNECTTIMEOUT ] = $connectionTimeout;

        return $this;
    }

    /**
     * @param int $connectionTimeoutMs
     *
     * @return ConnectionOptionBundle
     */
    public function connectionTimeoutMs(int $connectionTimeoutMs): ConnectionOptionBundle
    {
        $this->options[ CURLOPT_CONNECTTIMEOUT_MS ] = $connectionTimeoutMs;

        return $this;
    }

    /**
     * @param int $maxConnects
     *
     * @return ConnectionOptionBundle
     */
    public function maxConnects(int $maxConnects): ConnectionOptionBundle
    {
        $this->options[ CURLOPT_MAXCONNECTS ] = $maxConnects;

        return $this;
    }
}