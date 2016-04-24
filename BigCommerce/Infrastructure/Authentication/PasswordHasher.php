<?php namespace BigCommerce\Infrastructure\Authentication;

class PasswordHasher
{

    /** @return string */
    public function encrypt($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

}
