<?php

namespace AppBundle\Repository;

use AppBundle\Document\NotificationStatus;
use AppBundle\Document\NotificationType;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * ServiceInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationInfoRepository extends DocumentRepository
{
    public function archiveBirthdayNotification(){

        $result = $this->createQueryBuilder()
            ->update()
            ->multiple(true)
            ->field("status")->equals(NotificationStatus::Unconfirmed)
            ->field("type")->equals(NotificationType::Birthday)
             ->field("status")->set(NotificationStatus::Confirmed)
             ->getQuery()
             ->execute();

        return $result;
    }
    public function archiveCleanReminderNotification(){

        $result = $this->createQueryBuilder()
            ->update()
            ->multiple(true)
            ->field("status")->equals(NotificationStatus::Unconfirmed)
            ->field("type")->equals(NotificationType::Clean)
            ->field("status")->set(NotificationStatus::Confirmed)
            ->getQuery()
            ->execute();

        return $result;
    }

    public function findUnconfirmed()
    {
       /* $this->findBy(array("status"=>NotificationStatus::Unconfirmed));
        $result = $this->createQueryBuilder()
            ->find()
            ->field("status")->equals(NotificationStatus::Unconfirmed)
            ->getQuery()
            ->execute();*/
        return $this->findBy(array("status"=>NotificationStatus::Unconfirmed));
    }
}