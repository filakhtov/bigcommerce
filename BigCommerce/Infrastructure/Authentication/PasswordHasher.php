<?php namespace BigCommerce\Infrastructure\Authentication;

class PasswordHasher
{

    public function encrypt($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

}
