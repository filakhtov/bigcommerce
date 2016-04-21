<?php namespace BigCommerce\Infrastructure\Flickr\Contract;

interface FlickrRestConfigurationInterface
{

    /** @return string */
    public function apiEndpoint();

    /** @return string */
    public function apiKey();
}
