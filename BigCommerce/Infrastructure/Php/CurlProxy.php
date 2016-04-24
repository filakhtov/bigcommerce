<?php namespace BigCommerce\Infrastructure\Php;

class CurlProxy
{

    const URL = CURLOPT_URL;
    const RETURN_TRANSFER = CURLOPT_RETURNTRANSFER;

    /** @return resource */
    public function init()
    {
        return @curl_init();
    }

    /** @return bool */
    public function setopt($ch, $option, $value)
    {
        return @curl_setopt($ch, $option, $value);
    }

    /** @return string|FALSE */
    public function exec($ch)
    {
        return @curl_exec($ch);
    }

    /** @return void */
    public function close()
    {
        return @curl_close($ch);
    }

}
