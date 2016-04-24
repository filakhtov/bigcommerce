<?php namespace BigCommerce\Infrastructure\Authentication;

use \BigCommerce\Infrastructure\Authentication\Authentication;
use \BigCommerce\Infrastructure\Authentication\AuthenticationException;
use \BigCommerce\Infrastructure\Authentication\PasswordHasher;
use \Doctrine\ORM\EntityRepository;
use \Exception;

class AuthenticationService
{
    /** @var EntityRepository */
    private $authRepository;

    /** @var PasswordHasher */
    private $passwordHasher;

    /** @var Authentication */
    private $authentication;

    public function __construct(EntityRepository $authRepository, PasswordHasher $passwordHasher)
    {
        $this->authRepository = $authRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param string $username
     * @param string $password
     * @throws AuthenticationException
     * @return Authentication
     */
    public function authenticate($username, $password)
    {
        $authentication = $this->authRepository->find($username); /* @var $authentication Authentication */

        if (is_null($authentication)) {
            throw new AuthenticationException("User '{$username}' not found.");
        }

        if (false === $this->passwordHasher->verify($password, $authentication->password())) {
            throw new AuthenticationException("Invalid password for '{$username}'.");
        }

        $this->authentication = $authentication;

        return $this->authentication;
    }

    /**
     * @throws Exception
     * @return bool
     */
    public function isAuthenticated(Authentication $authentication)
    {
        if(is_null($this->authentication)) {
            $dbAuthentication = $this->authRepository->find($authentication->username()); /* @var $authentication Authentication */

            if (false === is_null($dbAuthentication) && $dbAuthentication->password() === $authentication->password()) {
                $this->authentication = $dbAuthentication;
            }
        }

        return false === is_null($this->authentication);
    }

    /** @return Authentication */
    public function currentAuthentication()
    {
        if(is_null($this->authentication)) {
            throw new AuthenticationException('User is not authenticated.');
        }

        return $this->authentication;
    }

}
