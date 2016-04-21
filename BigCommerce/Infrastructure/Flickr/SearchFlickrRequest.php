<?php namespace BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Infrastructure\Flickr\Contract\FlickrRequestInterface;
use \InvalidArgumentException;

class SearchFlickrRequest implements FlickrRequestInterface
{

    /** @var string */
    private $searchQuery;

    /** @var int */
    private $perPage;

    /** @var int */
    private $page;

    /** @throws InvalidArgumentException */
    public function __construct($searchQuery, $resultsPerPage, $pageNumber)
    {
        $this->setSearchQuery($searchQuery);
        $this->setPerPage($resultsPerPage);
        $this->setPage($pageNumber);
    }

    private function setSearchQuery($searchQuery)
    {
        if (!is_string($searchQuery)) {
            throw new InvalidArgumentException('Invalid search text given.');
        }

        $this->searchQuery = $searchQuery;
    }

    private function setPerPage($resultsPerPage)
    {
        $this->validatePage($resultsPerPage);
        $this->perPage = $resultsPerPage;
    }

    private function setPage($pageNumber)
    {
        $this->validatePage($pageNumber);
        $this->page = $pageNumber;
    }

    private function validatePage($pageParameter)
    {
        if (!is_int($pageParameter)) {
            throw new InvalidArgumentException('Invalid page parameter. Integer expected.');
        }

        if ($pageParameter < 1) {
            throw new InvalidArgumentException('Invalid page parameter. Non-zero positive value expected.');
        }
    }

    /** @return array */
    public function data()
    {
        return [
            'method' => 'flickr.photos.search',
            'text' => $this->searchQuery,
            'per_page' => $this->perPage,
            'page' => $this->page
        ];
    }

}
