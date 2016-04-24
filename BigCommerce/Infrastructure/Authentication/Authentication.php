<?php namespace BigCommerce\Infrastructure\Authentication;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class Authentication
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=10, unique=true, nullable=false)
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=60, nullable=false)
     * @var string
     */
    private $password;

    /** @return string */
    public function username()
    {
        return $this->username;
    }

    /** @return string */
    public function password()
    {
        return $this->password;
    }

}
