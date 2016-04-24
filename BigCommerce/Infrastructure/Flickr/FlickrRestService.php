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

    /**
     * @throws CurlException
     * @return mixed[]
     */
    public function __invoke(FlickrRequestInterface $request)
    {
        $requestData = array_merge([
            'api_key' => $this->configuration->apiKey(),
            'format' => 'php_serial',
            'privacy_filter' => 'public'
        ], $request->data());

        $requestQuery = http_build_query($requestData);

        $curl = $this->curl;
        $curl->setUrl($this->configuration->apiEndpoint() . '?' . $requestQuery);
        return unserialize($curl());
    }

}
