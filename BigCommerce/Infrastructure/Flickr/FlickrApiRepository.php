<?php namespace BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Domain\Contract\ApiRepositoryInterface;
use \BigCommerce\Domain\Gallery;
use \BigCommerce\Domain\Image;
use \BigCommerce\Infrastructure\Flickr\FlickrRestService;
use \BigCommerce\Infrastructure\Flickr\SearchFlickrRequest;

class FlickrApiRepository implements ApiRepositoryInterface
{

    private $flickrRestService;

    public function __construct(FlickrRestService $flickrRestService)
    {
        $this->flickrRestService = $flickrRestService;
    }

    public function findGallery($seachQuery, $pageNumber)
    {
        $searchRequest = new SearchFlickrRequest($seachQuery, 5, $pageNumber);

        $flickrRestService = $this->flickrRestService;
        $response = $flickrRestService($searchRequest);

        return $this->parseSearchResponse($response);
    }

    private function parseSearchResponse(array $response)
    {
        $gallery = new Gallery($response['photos']['page'], $response['photos']['pages']);

        foreach ($response['photos']['photo'] as $flickrImageData) {
            $gallery->addImage($this->imageFactory($flickrImageData));
        }

        return $gallery;
    }

    private function imageFactory(array $flickrImageData)
    {
        $thumbnail = sprintf(
            'https://farm%d.staticflickr.com/%d/%d_%s_q.jpg',
            $flickrImageData['farm'], $flickrImageData['server'], $flickrImageData['id'], $flickrImageData['secret']
        );

        $url = sprintf(
            'https://farm%d.staticflickr.com/%d/%d_%s_b.jpg',
            $flickrImageData['farm'], $flickrImageData['server'], $flickrImageData['id'], $flickrImageData['secret']
        );

        return new Image($thumbnail, $url, $flickrImageData['title']);
    }

}
