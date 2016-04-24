<?php namespace BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Domain\Contract\ApiRepositoryInterface;
use \BigCommerce\Domain\Entity\Gallery;
use \BigCommerce\Domain\Entity\Image;
use \BigCommerce\Infrastructure\Flickr\FlickrRestService;
use \BigCommerce\Infrastructure\Flickr\SearchFlickrRequest;

class FlickrApiRepository implements ApiRepositoryInterface
{

    /** @var FlickrRestService */
    private $flickrRestService;

    public function __construct(FlickrRestService $flickrRestService)
    {
        $this->flickrRestService = $flickrRestService;
    }

    /**
     * @param string $seachQuery
     * @param int $pageNumber
     * @return Gallery
     */
    public function findGallery($seachQuery, $pageNumber)
    {
        $searchRequest = new SearchFlickrRequest($seachQuery, 5, $pageNumber);

        $flickrRestService = $this->flickrRestService;
        $response = $flickrRestService($searchRequest);

        return $this->createGalleryFromFlickrData($response);
    }

    /** @return Gallery */
    private function createGalleryFromFlickrData(array $response)
    {
        $gallery = new Gallery($response['photos']['page'], $response['photos']['pages']);

        foreach ($response['photos']['photo'] as $flickrImageData) {
            $gallery->addImage($this->createImageFromFlickrData($flickrImageData));
        }

        return $gallery;
    }

    /** @return Image */
    private function createImageFromFlickrData(array $flickrImageData)
    {
        $thumbnail = sprintf(
            'https://farm%d.staticflickr.com/%d/%d_%s_n.jpg',
            $flickrImageData['farm'], $flickrImageData['server'], $flickrImageData['id'], $flickrImageData['secret']
        );

        $url = sprintf(
            'https://farm%d.staticflickr.com/%d/%d_%s_b.jpg',
            $flickrImageData['farm'], $flickrImageData['server'], $flickrImageData['id'], $flickrImageData['secret']
        );

        return new Image($thumbnail, $url, $flickrImageData['title']);
    }

}
