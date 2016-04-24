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
     */
    private $username;

    /** @ORM\Column(type="string", length=60, nullable=false) */
    private $password;

    public function username()
    {
        return $this->username;
    }

    public function password()
    {
        return $this->password;
    }

}
