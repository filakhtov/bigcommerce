<?php namespace BigCommerce\Domain\Contract;

interface ApiRepositoryInterface
{

    public function findGallery($seachQuery, $pageNumber);
}
