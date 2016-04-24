<?php namespace BigCommerce\Infrastructure\Authentication;

use \BigCommerce\Infrastructure\Authentication\Authentication;
use \BigCommerce\Infrastructure\Authentication\AuthenticationException;
use \BigCommerce\Infrastructure\Authentication\PasswordHasher;
use \Doctrine\ORM\EntityRepository;

class AuthenticationService
{
    private $ar;
    private $ph;

    public function __construct(EntityRepository $ar, PasswordHasher $ph)
    {
        $this->ar = $ar;
        $this->ph = $ph;
    }

    public function authenticate($username, $password)
    {
        $authentication = $this->ar->find($username); /* @var $authentication Authentication */

        if (is_null($authentication)) {
            throw new AuthenticationException("User '{$username}' not found.");
        }

        if (false === $this->ph->verify($password, $authentication->password())) {
            throw new AuthenticationException("Invalid password for '{$username}'.");
        }

        return $authentication;
    }

    public function isAuthenticated(Authentication $authentication)
    {
        $dbAuthentication = $this->ar->find($authentication->username()); /* @var $authentication Authentication */

        return (false === is_null($dbAuthentication)) &&
            ($dbAuthentication->password() === $authentication->password());
    }

}
