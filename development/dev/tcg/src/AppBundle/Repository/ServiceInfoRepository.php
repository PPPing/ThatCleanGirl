<?php

namespace AppBundle\Repository;

use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use Doctrine\ODM\MongoDB\DocumentRepository;
use AppBundle\Document\FrequencyType;
/**
 * ServiceInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ServiceInfoRepository extends DocumentRepository
{
    public function findLastRegularService($clientId){

        $last = $this->createQueryBuilder()
            ->field("clientId")->equals($clientId)
			->field("jobDetail.frequency")->notEqual(FrequencyType::WhenNeed)
            ->limit(1)
            ->sort(array("serviceDate"=>'DESC'))
            ->getQuery()
            ->getSingleResult();

        return $last;
    }

    public function findPendingService(){

        $query = $this->createQueryBuilder();
        $serviceInfoList= $query->field("status")->addOr($query->expr()->field("status")->equals(ServiceStatus::Pending))
            ->getQuery()
            ->execute();

        return $serviceInfoList;
    }
	
	public function findCompleteService(){

        $query = $this->createQueryBuilder();
        $serviceInfoList= $query->field("status")->addOr($query->expr()->field("status")->equals(ServiceStatus::Completed))
            ->getQuery()
            ->execute();

        return $serviceInfoList;
    }


    public function findConfirmed($filters){

        $query = $this->createQueryBuilder()->field("isConfirmed")->equals(true);
        if($filters!=null){
            $query->field("status");
            if($filters['pending']=="true"){
                $query->addOr($query->expr()->field("status")->equals(ServiceStatus::Pending));
            }

            if($filters['completed']=="true"){
                $query->addOr($query->expr()->field("status")->equals(ServiceStatus::Completed));
            }

            $query->addOr($query->expr()->field("status")->equals(ServiceStatus::Cancelled));
            if($filters['cancelled']=="true"){
                $query->addOr($query->expr()->field("status")->equals(ServiceStatus::Cancelled));
            }
        }
        $serviceInfo = $query->sort(array("status"=>'ASC'))
            ->sort(array("serviceDate"=>'ASC'))
            ->getQuery()
            ->execute();

        return $serviceInfo;
    }

    public function findUnconfirmed(){

        $serviceInfo = $this->createQueryBuilder()
            ->field("status")->equals(ServiceStatus::Pending)
            ->field("isConfirmed")->equals(false)
            ->sort(array("serviceDate"=>'ASC'))
            ->getQuery()
            ->execute();

        return $serviceInfo;
    }

    public function findPending($clientId){
        $activeClients = $this->findBy(array("status"=> ServiceStatus::Pending,"clientId"=>$clientId));

        return $activeClients;
    }


	public function findService($startTimestamp,$endTimestamp){
        $startTimestamp = substr($startTimestamp, 0, -3);
        $endTimestamp = substr($endTimestamp, 0, -3);
		
		$startDate = new \DateTime();
		$startDate = $startDate->setTimestamp((int)$startTimestamp);
		$startDate = $startDate->modify("-1 day");
		$endDate = new \DateTime();
        $endDate = $endDate->setTimestamp((int)$endTimestamp);
        $endDate = $endDate->modify("+1 day");
        
		$query=$this->createQueryBuilder();
          $result = $query ->field('serviceDate')
                ->addAnd($query->expr()->field('serviceDate')->gte($startDate))
                ->addAnd($query->expr()->field('serviceDate')->lt($endDate))
                ->sort(array("serviceDate"=>'ASC'))
                ->getQuery()
                ->execute();

        return $result;
	}

    public function findAllService(){
        $all = $this->findAll();
        return $all;
    }

    public function save($serviceInfo){
        $serviceInfo->setModifyTime(new \DateTime('NOW'));
        $this->dm->persist($serviceInfo);
        $this->dm->flush();
    }

}