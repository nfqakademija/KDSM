<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 4/9/2015
 * Time: 2:57 PM
 */

namespace KDSM\ContentBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;


/**
 * User
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
    }
}