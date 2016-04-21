<?php namespace BigCommerce\Infrastructure\Flickr\Contract;

interface FlickrRestConfigurationInterface
{

    /** @return string */
    public function endpointUrl();

    /** @return string */
    public function apiKey();
}
