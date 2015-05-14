<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository // implements UserProviderInterface
{
    public function getUsersLookingForGame($myUser)
    {
        $query = $this->createQueryBuilder('tb');
        $query->select()
//            ->where('tb.lookingForGame = true')
//            ->andWhere('tb.id != ?1');
              ->where('tb.id != ?1');
        $query->setParameters(array(1 => $myUser->getId()));
        $users = $query->getQuery()->getResult();
        $result = null;
        foreach ($users as $key => $user) {
                $result[$key]['id'] = $user->getId();
                $result[$key]['value'] = $user->getUsername();
                $result[$key]['user-photo'] = $user->getProfilePicturePath();
        }
        return $result;
    }

    public function persistObject($user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
}
