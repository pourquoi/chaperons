<?php

namespace AppBundle\Repository;


use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadUserByApiKey($key)
    {
        return $this->createQueryBuilder('u')
            ->where('u.apiKey = :apiKey')
            ->setParameter('apiKey', $key)
            ->getQuery()
            ->getOneOrNullResult();
    }


}