<?php namespace BigCommerce\Infrastructure\Php;

use \BigCommerce\Infrastructure\Php\CurlAdapter;
use \BigCommerce\Infrastructure\Php\CurlException;

class Curl
{

    /** @var resource */
    private $curlHandle;

    /** @var CurlAdapter */
    private $curlAdapter;

    public function __construct(CurlAdapter $curlAdapter)
    {
        $this->curlAdapter = $curlAdapter;
        $this->curlHandle = $this->curlAdapter->init();

        if (FALSE === $this->curlHandle) {
            throw new CurlException("Failed to create curl handle.");
        }

        $this->curlAdapter->setopt($this->curlHandle, CurlAdapter::RETURN_TRANSFER, TRUE);
    }

    public function __destruct()
    {
        $this->curlAdapter->close($this->curlHandle);
    }

    public function setUrl($url)
    {
        $this->curlAdapter->setopt($this->curlHandle, CurlAdapter::URL, $url);

        return $this;
    }

    public function __invoke()
    {
        $response = $this->curlAdapter->exec($this->curlHandle);

        if (FALSE === $response) {
            throw new CurlException("Failed to get response.");
        }

        return $response;
    }

}
