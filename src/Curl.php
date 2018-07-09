<?php declare(strict_types=1);

namespace Simlux\Curl;

use Simlux\Curl\Extensions\ExtensionInterface;
use Simlux\Curl\Options\AbstractOptionBundle;
use Simlux\Curl\Options\OptionTranslator;

/**
 * Class Curl
 *
 * @package Simlux\Curl
 */
class Curl
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML  = 'xml';

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
     * @var bool
     */
    private $collectInfo = false;

    /**
     * @var resource
     */
    private $fileHandle;

    /**
     * Curl constructor.
     *
     * @param string $url
     * @param array  $options
     */
    public function __construct(string $url = null, array $options = [])
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
     * @param OptionTranslator $options
     *
     * @return Curl
     */
    public function optionsFromArrayOptions(OptionTranslator $options): Curl
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
     * @param string|null $content
     *
     * @return Curl
     */
    public function post(string $content = null): Curl
    {
        $this->options[ CURLOPT_POST ] = true;
        if (!is_null($content)) {
            $this->options[ CURLOPT_POSTFIELDS ] = $content;
        }

        return $this;
    }

    /**
     * @return Curl
     */
    public function delete(): Curl
    {
        $this->options[ CURLOPT_CUSTOMREQUEST ] = 'DELETE';

        return $this;
    }

    /**
     * @param null   $data
     * @param string $format
     *
     * @return Curl
     * @throws \Exception
     */
    public function put($data = null, string $format = self::FORMAT_JSON): Curl
    {
        $this->options[ CURLOPT_CUSTOMREQUEST ] = 'PUT';

        if (!is_null($data)) {
            switch ($format) {
                case self::FORMAT_JSON:
                    $this->header('Content-Type', 'application/json');
                    $this->options[ CURLOPT_POSTFIELDS ] = json_encode($data);
                    break;

                case self::FORMAT_XML:
                    $this->header('Content-Type', 'application/xml');
                    $this->options[ CURLOPT_POSTFIELDS ] = $data;
                    break;

                default:
                    throw new \Exception('Unknown format ' . $format);
            }
        }

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

    /**
     * @param string $referrer
     *
     * @return Curl
     */
    public function referrer(string $referrer): Curl
    {
        $this->options[ CURLOPT_REFERER ] = $referrer;

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return Curl
     */
    public function file(string $filename): Curl
    {
        $this->fileHandle              = fopen($filename, 'w+');
        $this->options[ CURLOPT_FILE ] = $this->fileHandle;
        $this->binaryTransfer(true);
        $this->followLocation(true);

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
     * @param bool $reuse
     *
     * @return Curl
     */
    public function forbidReuse(bool $reuse = true): Curl
    {
        $this->options[ CURLOPT_FORBID_REUSE ] = $reuse;

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

        return $this;
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
        $response->body       = curl_exec($curl);
        $response->statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response->body === false) {
            $response->errorNumber = curl_errno($curl);
            $response->error       = curl_error($curl);
        }

        if ($this->collectInfo) {
            $response->info = curl_getinfo($curl);
        }

        curl_close($curl);
        $response->duration = microtime(true) - $start;

        if (is_resource($this->fileHandle)) {

            $meta_data = stream_get_meta_data($this->fileHandle);
            if (filesize($meta_data['uri']) === 0) {
                $response->error = 'File size is 0!';
            }

            fclose($this->fileHandle);
        }

        return $response;
    }

    private function setOptions()
    {
        foreach ($this->optionBundles as $options) {
            $options->setOptions($this);
        }
    }

    /**
     * @param string $url
     *
     * @return Curl
     */
    public function url(string $url): Curl
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return Curl
     */
    public function timeout(int $timeout): Curl
    {
        $this->options[ CURLOPT_TIMEOUT ] = $timeout;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @return Curl
     */
    public function timeoutMS(int $timeout): Curl
    {
        $this->options[ CURLOPT_TIMEOUT_MS ] = $timeout;

        return $this;
    }


    /**
     * @return string
     */
    public function getRequestUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        if (isset($this->options[ CURLOPT_CUSTOMREQUEST ])) {
            return strtoupper($this->options[ CURLOPT_CUSTOMREQUEST ]);
        }

        return 'GET';
    }

    /**
     * @return array
     */
    public function getRequestBody(): array
    {
        if (isset($this->options[ CURLOPT_POSTFIELDS ])) {
            return is_array($this->options[ CURLOPT_POSTFIELDS ])
                ? $this->options[ CURLOPT_POSTFIELDS ]
                : [$this->options[ CURLOPT_POSTFIELDS ]];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function translateOptionKey($key): string
    {
        $translate = [
            10001 => 'FILE',
            10002 => 'URL',
            3     => 'PORT',
            10004 => 'PROXY',
            10005 => 'USERPWD',
            10006 => 'PROXYUSERPWD',
            10007 => 'RANGE',
            10009 => 'INFILE',
            20011 => 'WRITEFUNCTION',
            20012 => 'READFUNCTION',
            13    => 'TIMEOUT',
            19913 => 'RETURNTRANSFER',
            14    => 'INFILESIZE',
            10015 => 'POSTFIELDS',
            10016 => 'REFERER',
            10017 => 'FTPPORT',
            10018 => 'USERAGENT',
            19    => 'LOW_SPEED_LIMIT',
            20    => 'LOW_SPEED_TIME',
            21    => 'RESUME_FROM',
            10022 => 'COOKIE',
            10023 => 'HTTPHEADER',
            10025 => 'SSLCERT',
            10026 => '(SSLCERT|SSLKEY)PASSWD',
            27    => 'CRLF',
            10028 => 'QUOTE',
            10029 => 'WRITEHEADER',
            10031 => 'COOKIEFILE',
            32    => 'SSLVERSION',
            33    => 'TIMECONDITION',
            34    => 'TIMEVALUE',
            10036 => 'CUSTOMREQUEST',
            10037 => 'STDERR',
            10039 => 'POSTQUOTE',
            41    => 'VERBOSE',
            42    => 'HEADER',
            43    => 'NOPROGRESS',
            44    => 'NOBODY',
            45    => 'FAILONERROR',
            46    => 'UPLOAD',
            47    => 'POST',
            48    => 'FTPLISTONLY',
            50    => 'FTPAPPEND',
            51    => 'NETRC',
            52    => 'FOLLOWLOCATION',
            53    => 'TRANSFERTEXT',
            54    => 'PUT',
            55    => 'MUTE',
            20056 => 'PROGRESSFUNCTION',
            58    => 'AUTOREFERER',
            59    => 'PROXYPORT',
            61    => 'HTTPPROXYTUNNEL',
            10062 => 'INTERFACE',
            10063 => 'KRB4LEVEL',
            64    => 'SSL_VERIFYPEER',
            10065 => 'CAINFO',
            20066 => 'PASSWDFUNCTION',
            68    => 'MAXREDIRS',
            10069 => 'FILETIME',
            10070 => 'TELNETOPTIONS',
            71    => 'MAXCONNECTS',
            72    => 'CLOSEPOLICY',
            74    => 'FRESH_CONNECT',
            75    => 'FORBID_REUSE',
            10076 => 'RANDOM_FILE',
            10077 => 'EGDSOCKET',
            78    => 'CONNECTTIMEOUT',
            20079 => 'HEADERFUNCTION',
            80    => 'HTTPGET',
            81    => 'SSL_VERIFYHOST',
            10082 => 'COOKIEJAR',
            10083 => 'SSL_CIPHER_LIST',
            84    => 'HTTP_VERSION',
            85    => 'FTP_USE_EPSV',
            10086 => 'SSLCERTTYPE',
            10087 => 'SSLKEY',
            10088 => 'SSLKEYTYPE',
            10089 => 'SSLENGINE',
            90    => 'SSLENGINE_DEFAULT',
            91    => 'DNS_USE_GLOBAL_CACHE',
            92    => 'DNS_CACHE_TIMEOUT',
            10093 => 'PREQUOTE',
            155   => 'TIMEOUT_MS',

        ];
        if (isset($translate[ $key ])) {
            return $translate[ $key ];
        }

        return (string) $key;
    }

    /**
     * @param bool $binaryTransfer
     *
     * @return Curl
     */
    public function binaryTransfer(bool $binaryTransfer = true): Curl
    {
        $this->options[ CURLOPT_BINARYTRANSFER ] = $binaryTransfer;

        return $this;
    }

}