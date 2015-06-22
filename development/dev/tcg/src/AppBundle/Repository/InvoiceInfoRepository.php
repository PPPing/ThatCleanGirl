<?php

namespace AppBundle\Repository;

use AppBundle\Document\InvoiceStatus;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
 * ServiceInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceInfoRepository extends DocumentRepository
{
    public function save($invoiceInfo){
        $invoiceInfo->setModifyTime(new \DateTime('NOW'));
        $this->dm->persist($invoiceInfo);
        $this->dm->flush();
    }
	
	public function setStatus($id,$status){
		$this->createQueryBuilder()
            ->findAndUpdate()
            ->field('id')->equals($id)    
            ->field('status')->set($status)
            ->getQuery()
            ->execute();
	}

    public function  findByMonth($date){
        $log = new Logger('Notification');
        $log->pushHandler(new StreamHandler( 'C:/xampp/htdocs/github/ThatCleanGirl/development/dev/tcg/app/logs/' .'Invoice.log', Logger::DEBUG));

        $date = date_create_from_format('Y-m-d', $date);
        $defaultTimeZone = date_default_timezone_get();
        $date->setTimezone(new \DateTimeZone($defaultTimeZone));

        $cur_y = (int)$date->format('Y');
        $cur_m = (int)$date->format('m');
        $startDate = date_create_from_format('Y-m-d', $cur_y.'-'.$cur_m.'-01');

        $next_y = $cur_y;
        $next_m = $cur_m+1;
        if($next_m>12){
            $next_y = $cur_y+1;
            $next_m = 1;
        }
        $endDate = date_create_from_format('Y-m-d', $next_y.'-'.$next_m.'-01');

         $query=$this->createQueryBuilder();
          $result = $query ->field('status')
                ->addAnd($query->expr()->field('serviceDate')->gte($startDate))
                ->addAnd($query->expr()->field('serviceDate')->lt($endDate))
                ->sort(array("clientId"=>'ASC'))
                ->getQuery()
                ->execute();

        $log->addDebug("Start : ".$startDate->format('Y-m-d')."   End : ".$endDate->format('Y-m-d'));
        return $result;
    }

}