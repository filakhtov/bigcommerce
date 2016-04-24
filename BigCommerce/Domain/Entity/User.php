<?php namespace BigCommerce\Domain\Entity;

use \BigCommerce\Domain\Entity\SearchHistory;
use \Doctrine\Common\Collections\ArrayCollection;
use \InvalidArgumentException;
use \LogicException;
use \Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="BigCommerce\Infrastructure\User\UserRepository")
 * @ORM\Table(name="user")
 */
class User
{

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="SearchHistory")
     * @ORM\JoinTable(
     *     name="user_history",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="history_id", referencedColumnName="id")}
     * )
     * @var ArrayCollection[SearchHistory]
     */
    private $searchHistory;

    /**
     * @ORM\Column(type="string", length=10, unique=true, nullable=false)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=60, nullable=false)
     * @var string
     */
    private $password;

    public function __construct()
    {
        $this->searchHistory = new ArrayCollection();
    }

    /** @var int */
    public function id()
    {
        return $this->id;
    }

    /** @var string */
    public function username()
    {
        return $this->username;
    }

    /** @var string */
    public function password()
    {
        return $this->password;
    }

    /** @var User */
    public function addSearchHistoryItem(SearchHistory $searchHistory)
    {
        if (false === $this->searchHistory->contains($searchHistory)) {
            $this->searchHistory->add($searchHistory);
        }
        return $this;
    }

    /** @var User */
    public function removeSearchHistoryItem(SearchHistory $searchHistory)
    {
        $this->searchHistory->removeElement($searchHistory);
        return $this;
    }

    /** @var ArrayCollection[SearchHistory] */
    public function searchHistory()
    {
        return $this->searchHistory;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        if (false === is_null($this->id())) {
            throw new LogicException('Can not change username of registered user.');
        }

        if (false === is_string($username)) {
            throw new InvalidArgumentException('Invalid username. String expected.');
        }

        if (strlen($username) < 3 || strlen($username) > 10) {
            throw new InvalidArgumentException('Invalid username. Must be between 3 and 10 characters long.');
        }

        $this->username = $username;

        return $this;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        if (false === is_string($password)) {
            throw new InvalidArgumentException('Invalid password value. String expected.');
        }

        $this->password = $password;

        return $this;
    }

}
