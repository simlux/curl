<?php declare(strict_types=1);

namespace Simlux\Curl\Extensions;

use Simlux\Curl\Curl;

/**
 * Interface ExtensionInterface
 *
 * @package Simlux\Curl\Extensions
 */
interface ExtensionInterface
{
    /**
     * @param Curl $curl
     */
    public function initialize(Curl $curl);

}