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
//        $query->setParameters(array(1 => $id, 2 => 'AutoGoal'/*, 3 => 3968*/));

        return $query->getQuery()->getResult();

//        return [1122231, 1231243 ,1222334,5433322];
    }

}
