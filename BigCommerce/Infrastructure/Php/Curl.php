<?php namespace BigCommerce\Infrastructure\Php;

use \BigCommerce\Infrastructure\Php\CurlProxy;
use \BigCommerce\Infrastructure\Php\CurlException;

class Curl
{

    /** @var resource */
    private $curlHandle;

    /** @var CurlProxy */
    private $curlAdapter;

    public function __construct(CurlProxy $curlAdapter)
    {
        $this->curlAdapter = $curlAdapter;
        $this->curlHandle = $this->curlAdapter->init();

        if (FALSE === $this->curlHandle) {
            throw new CurlException("Failed to create curl handle.");
        }

        $this->curlAdapter->setopt($this->curlHandle, CurlProxy::RETURN_TRANSFER, TRUE);
    }

    public function __destruct()
    {
        $this->curlAdapter->close($this->curlHandle);
    }

    public function setUrl($url)
    {
        if (false === $this->curlAdapter->setopt($this->curlHandle, CurlProxy::URL, $url)) {
            throw new CurlException("Failed to set url.");
        }

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
