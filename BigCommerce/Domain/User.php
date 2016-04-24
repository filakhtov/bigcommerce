<?php namespace BigCommerce\Domain;

class User
{

    private $id;
    private $username;
    private $password;

    public function id()
    {
        return $this->id;
    }

    public function username()
    {
        return $this->username;
    }

    public function password()
    {
        return $this->password;
    }

    public function setUsername($username)
    {
        if (false === is_null($this->id)) {
            throw new InvalidArgumentException('Can not change username of registered user.');
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

    public function setPassword($password)
    {
        if (false === is_string($password)) {
            throw new InvalidArgumentException('Invalid password value. String expected.');
        }

        $this->password = $password;

        return $this;
    }

}
