<?php

namespace KDSM\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\False;

/**
 * NotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends EntityRepository
{

    public function getAllUnviewedNotifications($userid)
    {

        $result = $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.userId = :userid')
            ->setParameter('userid', $userid)
            ->andWhere('n.viewed = 0')
            ->orderBy('n.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

//    public function setViewed($id)
//    {
//        $this->createQueryBuilder('n')
//            ->update()
//            ->set('n.viewed', 1)
//            ->where('n.id = :id')
//            ->setParameter('id', $id)
//            ->getQuery()
//            ->execute();
//    }

    public function createNotification($userid, $gameid)
    {
        $notification = new Notification();
        $notification->setGameId($gameid);
        $notification->setUserId($userid);
        $notification->setNotificationText('You have been invited for a game!');
        $notification->setViewed(0);


        $em = $this->getEntityManager();
        $em->persist($notification);
        $em->flush();
    }

    public function setViewed($notificationObject)
    {
        if ($notificationObject != null) {
            $notificationObject->setViewed(1);
        }
    }

}
