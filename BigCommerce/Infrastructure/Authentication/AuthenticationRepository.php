<?php namespace BigCommerce\Infrastructure\Authentication;

use \BigCommerce\Infrastructure\Authentication\AuthenticationException;
use \BigCommerce\Infrastructure\Authentication\Authentication;

class AuthenticationRepository
{
    public function authenticate()
    {
        return new Authentication;
        throw new AuthenticationException();
    }

    public function isAuthenticated(Authentication $authentication)
    {
        return false === is_null($authentication);
    }

}
