<?php namespace BigCommerce\Domain\Entity;

use \BigCommerce\Domain\Entity\Image;

class Gallery implements \JsonSerializable
{

    /** @var Image[] */
    private $images = [];

    /** @var int */
    private $page;

    /** @var int */
    private $totalPages;

    /**
     * @param int $page
     * @param int $pages
     */
    public function __construct($page, $pages)
    {
        $this->page = $page;
        $this->totalPages = $pages;

        if($this->page < 1) {
            $this->page = 0;
        }

        if($this->page > $this->totalPages) {
            $this->page = $this->totalPages;
        }
    }

    /** @return Gallery */
    public function addImage(Image $image)
    {
        if (false === in_array($image, $this->images)) {
            $this->images[] = $image;
        }

        return $this;
    }

    /** @return Image[] */
    public function images()
    {
        return $this->images();
    }

    /** @return int */
    public function totalPages()
    {
        return $this->totalPages;
    }

    /** @return int */
    public function page()
    {
        return $this->page;
    }

    /** @return mixed[] */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}
