<?php namespace BigCommerce\Infrastructure\Authentication;

class Authentication
{

    private $username;
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
