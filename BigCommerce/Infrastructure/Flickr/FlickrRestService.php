<?php namespace BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Infrastructure\Flickr\Contract\FlickrRequestInterface;
use \BigCommerce\Infrastructure\Flickr\Contract\FlickrRestConfigurationInterface;
use \BigCommerce\Infrastructure\Php\Curl;

class FlickrRestService
{

    /** @var Curl */
    private $curl;

    /** @var FlickrRestConfigurationInterface */
    private $configuration;

    public function __construct(FlickrRestConfigurationInterface $flickrRestConfiguration, Curl $curl)
    {
        $this->curl = $curl;
        $this->configuration = $flickrRestConfiguration;
    }

    public function __invoke(FlickrRequestInterface $request)
    {
        $requestData = array_merge(['api_key' => $this->configuration->apiKey()], $request->data());
        $requestQuery = http_build_query($requestData);

        $curl = $this->curl;
        $curl->setUrl($this->configuration->endpointUrl() . '?' . $requestQuery);
        return $curl();
    }

}
