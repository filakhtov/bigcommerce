<?php namespace BigCommerce\Domain\Entity;

class Image implements \JsonSerializable
{

    /** @var string */
    private $thumbnailUrl;

    /** @var string */
    private $url;

    /** @var string */
    private $title;

    /**
     * @param string $thumbnail
     * @param string $url
     * @param string $title
     */
    public function __construct($thumbnail, $url, $title)
    {
        $this->thumbnailUrl = $thumbnail;
        $this->url = $url;
        $this->title = $title;
    }

    /** @return string */
    public function thumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /** @return string */
    public function title()
    {
        return $this->title;
    }

    /** @return string */
    public function url()
    {
        return $this->url;
    }

    /** @return mixed[] */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}
