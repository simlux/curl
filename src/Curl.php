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
     * @var bool
     */
    private $collectInfo = false;

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
     * @param AbstractOptionBundle $optionBundle
     *
     * @return Curl
     */
    public function optionBundle(AbstractOptionBundle $optionBundle): Curl
    {
        $this->optionBundles[] = $optionBundle;

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
     * @param bool $collectInfo
     *
     * @return Curl
     */
    public function collectInfo(bool $collectInfo = true): Curl
    {
        $this->collectInfo = $collectInfo;

        return $this;
    }

    /**
     * @param bool $failOnError
     *
     * @return Curl
     */
    public function failOnError(bool $failOnError = true): Curl
    {
        $this->options[ CURLOPT_FAILONERROR ] = $failOnError;

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
     *
     * @param array $headers
     *
     * @return Curl
     */
    public function headers(array $headers): Curl
    {
        foreach ($headers as $name => $value) {
            $this->header[ $name ] = $value;
        }

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

    /**
     * @return Response
     */
    public function execute(): Response
    {
        $start                               = microtime(true);
        $curl                                = curl_init($this->url);
        $this->options[ CURLOPT_HTTPHEADER ] = $this->getHeaders();
        curl_setopt_array($curl, $this->options);
        $this->setOptions();
        $this->initializeExtensions();

        $response             = new Response();
        $response->statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response->body       = curl_exec($curl);

        if ($response->body === false) {
            $response->errorNumber = curl_errno($curl);
            $response->error       = curl_error($curl);
        }

        if ($this->collectInfo) {
            $response->info = curl_getinfo($curl);
        }

        curl_close($curl);
        $response->duration = $start - microtime(true);

        return $response;
    }

    private function setOptions()
    {
        foreach ($this->optionBundles as $options) {
            $options->setOptions($this);
        }
    }
}