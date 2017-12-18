<?php declare(strict_types=1);

namespace Simlux\Curl;

use Simlux\Curl\Extensions\ExtensionInterface;
use Simlux\Curl\Options\AbstractOptionBundle;

/**
 * Class Curl
 *
 * @package Simlux\Curl
 */
class Curl
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    private $header = [];

    /**
     * @var ExtensionInterface[]
     */
    private $extensions = [];

    /**
     * @var AbstractOptionBundle[]
     */
    private $optionBundles = [];

    /**
     * @var resource
     */
    private $curl;

    /**
     * Curl constructor.
     *
     * @param string $url
     * @param array  $options
     */
    public function __construct(string $url, array $options = [])
    {
        $this->url = $url;

        foreach ($options as $name => $value) {
            if ($value instanceof AbstractOptionBundle) {
                $this->optionBundles[] = $value;
            } else {
                $this->options[ $name ] = $value;
            }
        }
    }

    /**
     * @param ExtensionInterface $extension
     *
     * @return Curl
     */
    public function extension(ExtensionInterface $extension): Curl
    {
        $this->extensions[] = $extension;

        return $this;
    }

    private function initializeExtensions()
    {
        foreach ($this->extensions as $extension) {
            $extension->initialize($this);
        }
    }

    /**
     * @param $name
     * @param $value
     *
     * @return Curl
     */
    public function option($name, $value): Curl
    {
        $this->options[ $name ] = $value;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return Curl
     */
    public function options(array $options): Curl
    {
        foreach ($options as $name => $value) {
            $this->option($name, $value);
        }

        return $this;
    }

    /**
     * @param bool $returnTransfer
     *
     * @return Curl
     */
    public function returnTransfer(bool $returnTransfer = true): Curl
    {
        $this->options[ CURLOPT_RETURNTRANSFER ] = $returnTransfer;

        return $this;
    }

    /**
     * @param bool $autoReferrer
     *
     * @return Curl
     */
    public function autoReferrer(bool $autoReferrer = true): Curl
    {
        $this->options[ CURLOPT_AUTOREFERER ] = $autoReferrer;

        return $this;
    }

    public function referrer(string $referrer): Curl
    {
        $this->options[ CURLOPT_REFERER ] = $referrer;

        return $this;
    }

    /**
     * @param bool $followLocation
     *
     * @return Curl
     */
    public function followLocation(bool $followLocation = true): Curl
    {
        $this->options[ CURLOPT_FOLLOWLOCATION ] = $followLocation;

        return $this;
    }

    /**
     * @param string $userAgent
     *
     * @return Curl
     */
    public function userAgent(string $userAgent): Curl
    {
        $this->options[ CURLOPT_USERAGENT ] = $userAgent;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Curl
     */
    public function header(string $name, string $value): Curl
    {
        $this->header[ $name ] = $value;

        return $this;
    }

    /**
     * @return array
     */
    private function getHeaders(): array
    {
        $headers = [];
        foreach ($this->header as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value);
        }

        return $headers;
    }

    public function execute()
    {
        $this->curl                          = curl_init($this->url);
        $this->options[ CURLOPT_HTTPHEADER ] = $this->getHeaders();
        curl_setopt_array($this->curl, $this->options);
        $this->setOptions();
        $this->initializeExtensions();
    }

    private function setOptions()
    {
        foreach ($this->optionBundles as $options) {
            $options->setOptions($this);
        }
    }
}