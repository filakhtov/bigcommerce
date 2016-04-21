<?php namespace test\BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Infrastructure\Flickr\SearchFlickrRequest;

class SearchFlickrRequestTest extends \PHPUnit_Framework_TestCase
{

    /** @dataProvider constructProvider */
    public function testConstruct($searchText, $resultsPerPage, $pageNumber)
    {
        $flickrRequest = new SearchFlickrRequest($searchText, $resultsPerPage, $pageNumber);
        $this->assertEquals(['method' => 'flickr.photos.search', 'text' => $searchText, 'per_page' => $resultsPerPage, 'page' => $pageNumber], $flickrRequest->data());
    }

    public function constructProvider()
    {
        return [
            ['BigCommerce', 5, 1],
            ['Australia', 10, 2],
            ['Sydney', 5, 100]
        ];
    }

    /**
     * @dataProvider constructFailProvider
     * @expectedException \InvalidArgumentException
     */
    public function testConstructFail($searchText, $resultsPerPage, $pageNumber)
    {
        new SearchFlickrRequest($searchText, $resultsPerPage, $pageNumber);
    }

    public function constructFailProvider()
    {
        return [
            [0, 1, 1],
            [null, 1, 1],
            ['valid', false, 2],
            ['testNegativePerPage', -1, 1],
            ['testZeroPerPage', 0, 5],
            ['testZeroPage', 5, 0],
            ['testNegativePage', 5, -1]
        ];
    }

}
