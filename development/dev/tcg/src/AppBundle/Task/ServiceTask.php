<?php

namespace AppBundle\Task;

use Symfony\Component\DependencyInjection\ContainerAware;
use AppBundle\Document\ClientInfo;
use AppBundle\Document\FrequencyType;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use \DateTime;

class ServiceTask extends ContainerAware{

    public $logger;

    public function CreatePendingServices(){
        $clientList = $this->container->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->findAllActiveClient();
        $nextService = "";
        foreach($clientList as $client){
            //$logger->addDebug($client->getClientId() ." : ". $client->getClientName());

            $frequencyType = $client->getJobDetail()->getFrequency();
            if($frequencyType === FrequencyType::WhenNeed){
                continue;
            }else{
                $clientId = $client->getClientId();

                $serviceDao = $this->get('doctrine_mongodb')
                    ->getManager()
                    ->getRepository('AppBundle:ServiceInfo');
                $pendingServices = $serviceDao ->findPending($clientId);

                if($pendingServices==null){
                    $log->addDebug($client->getClientId() ." : no pending service");
                    $lastCompletedService = $serviceDao->findLastCompleteService($clientId);
                    if ($lastCompletedService == null) {

                        $lastServiceDate = $client->getStartDate();

                        $lastServiceTeamId = "";
                    } else {
                        $lastServiceDate = $lastCompletedService->getServiceDate();
                        $lastServiceTeamId = $lastCompletedService ->getTeamId();
                    }
                    $nextServiceDate = clone $lastServiceDate;

                    if($frequencyType === FrequencyType::Weekly) {
                        $nextServiceDate = $nextServiceDate->modify("+1 week");
                    }else if($frequencyType === FrequencyType::Fortnightly) {
                        $nextServiceDate = $nextServiceDate->modify("+2 week");
                    }else if($frequencyType === FrequencyType::Monthly) {
                        $nextServiceDate = $nextServiceDate->modify("+4 week");
                    }else if($frequencyType === FrequencyType::TwiceAWeek) {
                        $nextServiceDate = null;//$nextServiceDate->modify("+3 day");
                    }else if($frequencyType ===FrequencyType::WhenNeed){
                        continue;
                    }

                    $nextService = new ServiceInfo();
                    $nextService->setStatus(ServiceStatus::Pendding);
                    $nextService->setIsConfirmed(false);
                    $nextService->setClientId($clientId);
                    $nextService->setClientName($client->getClientName());
                    $nextService->setServiceDate($nextServiceDate);
                    $nextService->setAddress($client->getAddress());
                    $nextService->setPrice($client->getPrice());
                    $nextService->setPaymentType($client->getPaymentType());
                    $nextService->setInvoiceNeeded($client->getInvoiceNeeded());
                    $nextService->setTeamId($lastServiceTeamId);
                    $nextService->setCreatorId("auto");
                    $nextService->setCreateTime(new DateTime("Now"));
                    $nextService->setFeedback("");

                    $serviceDao->save($nextService);
                }
            }
        }
    }

}