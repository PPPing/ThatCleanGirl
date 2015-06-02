<?php
/**
 * Created by PhpStorm.
 * User: Mr.Clock
 * Date: 2015/5/11
 * Time: 23:10
 */
namespace AppBundle\Console\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Document\ClientInfo;
use AppBundle\Document\FrequencyType;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class ServiceCommand extends ContainerAwareCommand
{
    protected $clientDao;
    protected $serviceDao;

    protected $logger;

    protected function configure()
    {
        $this
            ->setName('tcg:service')
            ->setDescription('Process Service Information.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Please provide the "type".'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            $this->clientDao = $this->getContainer()->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:ClientInfo');
            $this->serviceDao = $this->getContainer()->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:ServiceInfo');
            $this->logger = new Logger('ServiceCommand');
            $this->logger->pushHandler(new StreamHandler($this->getContainer()->getParameter('log_dir').'ServiceCommand.log'));
            $output->writeln($this->getContainer()->getParameter('log_dir'));
            $this->logger->addInfo("test logger");
            $type = $input->getArgument('type');
            $text = 'Type - '.$type.' Started';
            if ($type==='create') {
                $output->writeln($text);
                //$this->CreatePendingServices();
                $this->CreateServices();
                $text = 'Type - '.$type.' Finish';
            } else if ($type==='update') {
                $output->writeln($text);
                $this->UpdateServiceStatus();
                 $text = 'Type - '.$type.' Finish';
            } else if ($type==='all') {
                $this->ProcessServices();
            }else {
                $text = 'Type is missing.';
            }

            $output->writeln($text);
        }catch (Exception $e){
            $this->logger->addError($e->getMessage());
            $this->logger->addError($e->getFile() ." ".$e->getLine());
            $this->logger->addError($e->getCode());
            $this->logger->addError($e->getTraceAsString());
        }
    }

    private function ProcessServices(){
        $this->CreatePendingServices();
        $this->UpdateServiceStatus();

    }

    private function UpdateServiceStatus(){
        $allServices = $this->serviceDao->findAllService();

        if($allServices===null){
            $this->logger->addDebug("no service");
        }

        foreach($allServices as $serviceInfo){
            $defaultTimeZone = date_default_timezone_get();
            $this->logger->addDebug('[Default TimeZone]'.$defaultTimeZone);
            $todayDate = new DateTime('NOW');
            $todayDate->setTimezone(new \DateTimeZone($defaultTimeZone));
            $todayDate = (int) $todayDate->format('Ymd');

            $serviceDate = $serviceInfo->getServiceDate();
            $serviceDate->setTimezone(new \DateTimeZone($defaultTimeZone));
            $serviceDate = (int) $serviceDate->format('Ymd');
            //$todayDate=20150515;
            $this->logger->addDebug('[DATE]'.$todayDate.' : '.$serviceDate);
            if($todayDate === $serviceDate){
                $curStatus =  $serviceInfo->getStatus();
                if($curStatus === ServiceStatus::Pending){
                    if($serviceInfo->getIsConfirmed()===true){
                        $this->logger->addDebug('[CCC]'.$serviceInfo->getId().' : Set Service to Processing');
                        $serviceInfo->setStatus(ServiceStatus::Processing);
                    }else{
                        //Send Notification to remind user to confirm the service
                        $this->logger->addDebug('[AAA]'.$serviceInfo->getId().' : Send Notification to remind user to confirm the service');
                        //$serviceInfo->setStatus(ServiceStatus::Processing);
                        continue;
                    }
                }else if($curStatus === ServiceStatus::Completed) {
                    //Send Notification remind user to write a feedback or comment for the service.
                    $this->logger->addDebug($serviceInfo->getId().' : Send Notification remind user to write a feedback or comment for the service.');
                    continue;
                }else{
                    continue;
                }
            }else if( ($todayDate+1)=== $serviceDate ){
               if($serviceInfo->getStatus() === ServiceStatus::Pending && $serviceInfo->getIsConfirmed()===false){
                    //Send Notification to remind user to confirm the service
                    $this->logger->addDebug($serviceInfo->getId().' : Send Notification to remind user to confirm the service.');
                    continue;
                }
            }else if($todayDate > $serviceDate){
                $this->logger->addDebug($serviceInfo->getId().' : today > serviceDate');
                if($serviceInfo->getStatus() === ServiceStatus::Pending && $serviceInfo->getIsConfirmed()===false){
                        //Send Notification to inform the user canceled
                    $this->logger->addDebug($serviceInfo->getId().' : Send Notification to inform the user canceled.');
                    $serviceInfo->setStatus(ServiceStatus::Cancelled);
                }else if($serviceInfo->getStatus() === ServiceStatus::Completed){
                    $this->logger->addDebug($serviceInfo->getId().' : Send Notification remind user to write a feedback or comment for the service..');
                    //Send Notification remind user to write a feedback or comment for the service.
                    continue;
                }else if($serviceInfo->getStatus() === ServiceStatus::Processing){
                    $this->logger->addDebug($serviceInfo->getId().' : Send Notification remind user to processing is completed');
                    $serviceInfo->setStatus(ServiceStatus::Completed);
                }
            }else{
                continue;
            }

            $this->serviceDao->save($serviceInfo);

        }

    }

    private function CreateServices(){
        $clientList = $this->clientDao->findAllActiveClient();
        $nextService = "";
        foreach($clientList as $client){
            $this->logger->addDebug($client->getClientId() ." :" . $client->getClientName());
            continue;
            $frequencyType = $client->getJobDetail()->getFrequency();
            if($frequencyType === FrequencyType::WhenNeed){
                continue;
            }else{
                $clientId = $client->getClientId();
                $pendingServices = $this->serviceDao ->findPending($clientId);

                $this->logger->addDebug($client->getClientId() ." : ". $client->getClientName());

                if($pendingServices==null){
                    $this->logger->addDebug($client->getClientId() ." : no pending service");
                    $lastCompletedService = $this->serviceDao->findLastCompleteService($clientId);

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
                    $nextService->setStatus(ServiceStatus::Pending);
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

                    //$this->logger->addDebug(json_decode($nextService,JSON_PRETTY_PRINT));
                    $this->serviceDao->save($nextService);
                }
            }
        }
    }

    private function CreatePendingServices(){
            $clientList = $this->clientDao->findAllActiveClient();
            $nextService = "";
            foreach($clientList as $client){
                $frequencyType = $client->getJobDetail()->getFrequency();
                if($frequencyType === FrequencyType::WhenNeed){
                    continue;
                }else{
                    $clientId = $client->getClientId();
                    $pendingServices = $this->serviceDao ->findPending($clientId);

                    $this->logger->addDebug($client->getClientId() ." : ". $client->getClientName());

                    if($pendingServices==null){
                        $this->logger->addDebug($client->getClientId() ." : no pending service");
                        $lastCompletedService = $this->serviceDao->findLastCompleteService($clientId);

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
                        $nextService->setStatus(ServiceStatus::Pending);
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

                        //$this->logger->addDebug(json_decode($nextService,JSON_PRETTY_PRINT));
                        $this->serviceDao->save($nextService);
                    }
                }
            }
    }
}