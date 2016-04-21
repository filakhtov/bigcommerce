<?php namespace test\BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Infrastructure\Flickr\Contract\FlickrRequestInterface;
use \BigCommerce\Infrastructure\Flickr\Contract\FlickrRestConfigurationInterface;
use \BigCommerce\Infrastructure\Flickr\FlickrRestService;
use \BigCommerce\Infrastructure\Php\Curl;

class FlickrRestServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider flickrRequestProvider */
    public function testFlickrRequest($searchText, $apiKey, $endPoint, $expectedRequestUrl, $expectedResponse)
    {
        $flickrRequest = $this->getMockForAbstractClass(FlickrRequestInterface::class);
        $flickrRequest->expects($this->once())->method('data')->willReturn(['method' => 'flickr.photos.search', 'text' => $searchText]);

        $curl = $this->getMock(Curl::class, [], [], '', false);
        $curl->expects($this->once())->method('setUrl')->with($expectedRequestUrl);
        $curl->expects($this->once())->method('__invoke')->willReturn(serialize($expectedResponse));

        $flickrRestConf = $this->getMockForAbstractClass(FlickrRestConfigurationInterface::class);
        $flickrRestConf->expects($this->atLeastOnce())->method('apiEndpoint')->willReturn($endPoint);
        $flickrRestConf->expects($this->atLeastOnce())->method('apiKey')->willReturn($apiKey);

        $flickrRestService = new FlickrRestService($flickrRestConf, $curl);
        $this->assertSame($expectedResponse, $flickrRestService($flickrRequest));
    }

    public function flickrRequestProvider()
    {
        return [
            [
                'BigCommerce',
                'bc156998-5879-48af-82db-02d671895402',
                'https://api.flickr.com/services/rest/',
                'https://api.flickr.com/services/rest/?api_key=bc156998-5879-48af-82db-02d671895402&format=php_serial&method=flickr.photos.search&text=BigCommerce',
                '{"status":"OK"}'
            ],
            [
                'Australia',
                '2b29cc76-6deb-4794-8e0e-04429595461d',
                'https://api2.flickr.com/services/rest/',
                'https://api2.flickr.com/services/rest/?api_key=2b29cc76-6deb-4794-8e0e-04429595461d&format=php_serial&method=flickr.photos.search&text=Australia',
                '{"response":"yes"}'
            ]
        ];
    }

}
