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

    /**
     * @param string $searchQuery
     * @param int $resultsPerPage
     * @param int $pageNumber
     * @throws InvalidArgumentException
     */
    public function __construct($searchQuery, $resultsPerPage, $pageNumber)
    {
        $this->setSearchQuery($searchQuery)
            ->setPerPage($resultsPerPage)
            ->setPage($pageNumber);
    }

    /**
     * @param string $searchQuery
     * @throws InvalidArgumentException
     * @return SearchFlickrRequest
     */
    private function setSearchQuery($searchQuery)
    {
        if (!is_string($searchQuery)) {
            throw new InvalidArgumentException('Invalid search text given.');
        }

        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     * @param int $resultsPerPage
     * @throws InvalidArgumentException
     * @return SearchFlickrRequest
     */
    private function setPerPage($resultsPerPage)
    {
        $this->validatePage($resultsPerPage);
        $this->perPage = $resultsPerPage;

        return $this;
    }

    /**
     * @param int $pageNumber
     * @throws InvalidArgumentException
     * @return SearchFlickrRequest
     */
    private function setPage($pageNumber)
    {
        $this->validatePage($pageNumber);
        $this->page = $pageNumber;

        return $this;
    }

    /**
     * @param int $pageParameter
     * @throws InvalidArgumentException
     * @return void
     */
    private function validatePage($pageParameter)
    {
        if (!is_int($pageParameter)) {
            throw new InvalidArgumentException('Invalid page parameter. Integer expected.');
        }

        if ($pageParameter < 1) {
            throw new InvalidArgumentException('Invalid page parameter. Non-zero positive value expected.');
        }
    }

    /** @return mixed[] */
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
