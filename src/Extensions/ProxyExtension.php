<?php declare(strict_types=1);

namespace Simlux\Curl\Extensions;

use Simlux\Curl\Curl;

/**
 * Class ProxyExtension
 *
 * @package Simlux\Curl\Extensions
 */
class ProxyExtension implements ExtensionInterface
{
    const TYPE_HTTP   = CURLPROXY_HTTP;
    const TYPE_SOCKS4 = CURLPROXY_SOCKS4;
    const TYPE_SOCKS5 = CURLPROXY_SOCKS5;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * ProxyExtension constructor.
     *
     * @param string $type
     * @param string $host
     * @param int    $port
     */
    public function __construct(string $type, string $host, int $port)
    {
        $this->type = $type;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return ProxyExtension
     */
    public function auth(string $username, string $password): ProxyExtension
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * @param Curl $curl
     */
    public function initialize(Curl $curl)
    {
        $curl->options([
            CURLOPT_PROXYTYPE     => $this->type,
            CURLOPT_PROXY         => $this->host,
            CURLOPT_PROXYPORT     => $this->port,
            CURLOPT_PROXYUSERNAME => $this->username,
            CURLOPT_PROXYPASSWORD => $this->password,
        ]);
    }
}