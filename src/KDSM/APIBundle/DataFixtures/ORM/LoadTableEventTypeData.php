<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/1/2015
 * Time: 6:07 PM
 */
namespace KDSM\APIBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KDSM\APIBundle\Entity\TableEventType;

class LoadTableEventTypeData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $shakeEvent = new TableEventType();
        $shakeEvent->setName('TableShake');

        $goalEvent = new TableEventType();
        $goalEvent->setName('AutoGoal');

        $swipeEvent = new TableEventType();
        $swipeEvent->setName('CardSwipe');

        $manager->persist($shakeEvent);
        $manager->persist($goalEvent);
        $manager->persist($swipeEvent);
        $manager->flush();
    }

}