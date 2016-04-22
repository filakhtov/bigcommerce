<?php namespace BigCommerce\Domain;

use \JsonSerializable;

class Gallery implements JsonSerializable
{

    private $images = [];
    private $page;
    private $totalPages;

    public function __construct($page, $pages)
    {
        $this->page = $page;
        $this->totalPages = $pages;
    }

    public function addImage(\BigCommerce\Domain\Image $image)
    {
        if (false === in_array($image, $this->images)) {
            $this->images[] = $image;
        }
    }

    public function images()
    {
        return $this->images();
    }

    public function totalPages()
    {
        return $this->totalPages;
    }

    public function page()
    {
        return $this->page;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}
