<?php namespace test\BigCommerce\Infrastructure\Configuration;

use \BigCommerce\Infrastructure\Configuration\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /** @dataProvider flickrRestConfigurationInterfaceProvider */
    public function testFlickrRestConfigurationInterface($apiKey, $apiEndpoint)
    {
        $configuration = new Configuration([
            'flickr' => ['api_key' => $apiKey, 'api_endpoint' => $apiEndpoint]
        ]);

        $this->assertSame($apiKey, $configuration->apiKey());
        $this->assertSame($apiEndpoint, $configuration->apiEndpoint());
    }

    public function flickrRestConfigurationInterfaceProvider()
    {
        return [
            ['ab4244ab-1430-4b2e-b7cd-380ede566e27', 'http://api.flickr.com/services/rest/'],
            ['0d8c4486-bf1d-4f09-9e1c-13ca720c4af1', 'http://test-api.flickr.com/services/rest/']
        ];
    }

    /**
     * @dataProvider flickrRestConfigurationInvalidProvider
     * @expectedException \InvalidArgumentException
     */
    public function testFlickrRestConfigurationInvalid(array $invalidParameters)
    {
        new Configuration($invalidParameters);
    }

    public function flickrRestConfigurationInvalidProvider()
    {
        return [
            [
                []
            ],
            [
                ['flickr' => null]
            ],
            [
                ['flickr' => []]
            ],
            [
                ['flickr' => ['api_key' => false, 'api_endpoint' => 'http://api.flickr.com/services/rest/']]
            ],
            [
                ['flickr' => ['api_key' => 'b22d912d-8c6e-476c-8737-38f1409063d9', 'api_endpoint' => null]]
            ]
        ];
    }

}
