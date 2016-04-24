<?php namespace BigCommerce\Domain\Entity;

use \Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="history")
 */
class SearchHistory
{

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", length=100, nullable=false, unique=true) */
    private $query;

    public function id()
    {
        return $this->id;
    }

    public function query()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        if (false === is_null($this->id())) {
            throw new LogicException('Can not change query of search history item.');
        }

        if (false === is_string($query)) {
            throw new InvalidArgumentException('Invalid query. String expected.');
        }

        if (strlen($query) < 3 || strlen($query) > 100) {
            throw new InvalidArgumentException('Invalid query. Length must be between 3 and 100 characters long.');
        }

        $this->query = $query;
    }
}
