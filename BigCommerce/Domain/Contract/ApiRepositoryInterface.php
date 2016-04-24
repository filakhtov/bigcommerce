<?php namespace BigCommerce\Domain\Contract;

interface ApiRepositoryInterface
{

    /**
     * @param string $searchQuery
     * @param int $pageNumber
     * @return Gallery
     */
    public function findGallery($seachQuery, $pageNumber);
}
