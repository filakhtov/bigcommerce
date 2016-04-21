<?php namespace BigCommerce\Infrastructure\Flickr;

use \BigCommerce\Infrastructure\Flickr\Contract\FlickrRequestInterface;
use \InvalidArgumentException;

class SearchFlickrRequest implements FlickrRequestInterface
{

    /** @var string */
    private $text;

    /** @throws InvalidArgumentException */
    public function __construct($text)
    {
        if (!is_string($text)) {
            throw new InvalidArgumentException('Invalid search text given.');
        }

        $this->text = $text;
    }

    /** @return array */
    public function data()
    {
        return [
            'method' => 'flickr.photos.search',
            'text' => $this->text
        ];
    }

}
