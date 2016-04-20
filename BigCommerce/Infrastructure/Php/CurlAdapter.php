<?php namespace BigCommerce\Infrastructure\Php;

class CurlAdapter
{

    const URL = CURLOPT_URL;
    const RETURN_TRANSFER = CURLOPT_RETURNTRANSFER;

    public function init()
    {
        return @curl_init();
    }

    public function setopt($ch, $option, $value)
    {
        return @curl_setopt($ch, $option, $value);
    }

    public function exec($ch)
    {
        return @curl_exec($ch);
    }

    public function close()
    {
        return @curl_close($ch);
    }

}
