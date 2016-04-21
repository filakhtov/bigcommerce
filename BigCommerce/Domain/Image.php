<?php namespace BigCommerce\Domain;

class Image
{

    private $thumbnail;
    private $url;
    private $title;

    public function __construct($thumbnail, $url, $title)
    {
        $this->thumbnail = $thumbnail;
        $this->url = $url;
        $this->title = $title;
    }

    public function thumbnail()
    {
        return $this->thumbnail;
    }

    public function title()
    {
        return $this->title;
    }

    public function url()
    {
        return $this->url;
    }

}
