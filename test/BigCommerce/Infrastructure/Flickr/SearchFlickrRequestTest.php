<?php namespace test\BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Infrastructure\Flickr\SearchFlickrRequest;

class SearchFlickrRequestTest extends \PHPUnit_Framework_TestCase
{

    /** @dataProvider constructProvider */
    public function testConstruct($searchText)
    {
        $flickrRequest = new SearchFlickrRequest($searchText);
        $this->assertEquals(['method' => 'flickr.photos.search', 'text' => $searchText], $flickrRequest->data());
    }

    public function constructProvider()
    {
        return [
            ['BigCommerce'],
            ['Australia'],
            ['Sydney']
        ];
    }

    /**
     * @dataProvider constructFailProvider
     * @expectedException \InvalidArgumentException
     */
    public function testConstructFail($invalidSearchText)
    {
        new SearchFlickrRequest($invalidSearchText);
    }

    public function constructFailProvider()
    {
        return [
            [0], [null], [false], [true]
        ];
    }

}
