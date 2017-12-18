<?php declare(strict_types=1);

namespace Simlux\Curl\Options;

use Simlux\Curl\Curl;

/**
 * Class AbstractOptionBundle
 *
 * @package Simlux\Curl\Options
 */
class AbstractOptionBundle
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param Curl $curl
     */
    public function setOptions(Curl $curl)
    {
        $curl->options($this->options);
    }
}