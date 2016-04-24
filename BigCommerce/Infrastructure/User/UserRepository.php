<?php namespace BigCommerce\Infrastructure\User;

use \BigCommerce\Domain\Contract\UserRepositoryInterface;
use \BigCommerce\Domain\Entity\User;
use \Doctrine\ORM\EntityRepository;
use \Exception;
use \InvalidArgumentException;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function persist(User $user)
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush($user);
        } catch (Exception $e) {
            throw new InvalidArgumentException("User already exists.");
        }
    }
}
