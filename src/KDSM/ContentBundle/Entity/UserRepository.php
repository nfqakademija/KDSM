<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository // implements UserProviderInterface
{
    public function getUsersLookingForGame()
    {
        $query = $this->createQueryBuilder('tb');
        $query->select()
            ->where('tb.lookingForGame = true');
        $users = $query->getQuery()->getResult();
        foreach ($users as $user) {
            $result[] = $user->getId();
        }
        return $result;
    }

}
