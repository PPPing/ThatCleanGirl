<?php
/**
 * Created by PhpStorm.
 * User: Mr.Clock
 * Date: 2015/5/11
 * Time: 23:10
 */
namespace AppBundle\Command;

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
            ->setName('AppBundle:service')
            ->setDescription('Process Service Information.')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Please provide the "type".'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->clientDao = $this->getContainer()->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:ClientInfo');
            $this->serviceDao = $this->getContainer()->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:ServiceInfo');
            $this->logger = new Logger('ServiceCommand');
            $this->logger->pushHandler(new StreamHandler($this->getContainer()->getParameter('log_dir') . 'ServiceCommand.log'));
            $type = $input->getArgument('type');
            $text = 'Type - ' . $type . ' Started';
            if ($type === 'create') {
                $output->writeln($text);
                //$this->CreatePendingServices();
                $this->CreateServices();
                $text = 'Type - ' . $type . ' Finish';
            } else if ($type === 'update') {
                $output->writeln($text);
                $this->UpdateServiceStatus();
                $text = 'Type - ' . $type . ' Finish';
            } else if ($type === 'all') {
                $this->ProcessServices();
            } else {
                $text = 'Type is missing.';
            }

            $output->writeln($text);
        } catch (Exception $e) {
            $this->logger->addError($e->getMessage());
            $this->logger->addError($e->getFile() . " " . $e->getLine());
            $this->logger->addError($e->getCode());
            $this->logger->addError($e->getTraceAsString());
        }
    }

    private function CreateServices(){
        $clientList = $this->clientDao->findAllActiveClient();
        $defaultTimeZone = date_default_timezone_get();
        foreach($clientList as $client){
            $frequencyType = $client->getJobDetail()->getFrequency();
            if($frequencyType === FrequencyType::WhenNeed){
                continue;
            }else{
                $clientId = $client->getClientId();
                $teamId = '';

                $offset_h = 2;
                $offset_m = 0;
                $this->logger->addDebug($client->getClientId() ." : ". $client->getClientName());
                $lastService = $this->serviceDao->findLastService($clientId);
                if($lastService===null){
                    $serviceDate = $client->getServiceDate();
                }else{
                    $lastServiceDate = $lastService->getServiceDate();
                    $serviceDate = clone $lastServiceDate;
                    if($frequencyType === FrequencyType::Weekly) {
                         $serviceDate->modify("+1 week");
                    }else if($frequencyType === FrequencyType::Fortnightly) {
                       $serviceDate->modify("+2 week");
                    }else if($frequencyType === FrequencyType::Monthly) {
                        $serviceDate->modify("+4 week");
                    }else if($frequencyType === FrequencyType::TwiceAWeek) {
                        //$nextServiceDate = null;//$nextServiceDate->modify("+3 day");
                    }
                    $teamId = $lastService->getTeamId();

                    $serviceStartTime = $lastService->getServiceStartTime();
                    $serviceEndTime = $lastService->getServiceEndTime();

                    $start = explode ( ":" ,$serviceStartTime );
                    $start_h = $start[0];
                    $start_m= $start[1];
                    $end = explode ( ":" ,$serviceEndTime );
                    $end_h = $end[0];
                    $end_m= $end[1];

                    $offset_h = (int)$end_h - (int)$start_h;
                    $offset_m = (int)$end_m - (int)$start_m;
                    if($offset_m<0){
                        $offset_h -=1;
                        $offset_m = 60 - $offset_m;
                    }
                }

                $today = new DateTime('NOW');
                if($serviceDate>$today && $serviceDate <= $today->modify("+7 day")) {
                    $serviceStartTime = $client->getServiceTime();


                    $start = explode(":", $serviceStartTime);
                    $start_h = $start[0];
                    $start_m = $start[1];

                    $end_h = (int)$start_h + $offset_h;
                    $end_m = (int)$start_m + $offset_m;

                    if ($end_m >= 60) {
                        $end_m -= $end_m - 60;
                        $end_h += 1;
                    }
                    $end_h = $end_h > 23 ? 23 : $end_h;
                    $end_h = $end_h < 10 ? ('0' . $end_h) : $end_h;
                    $end_m = $end_m < 10 ? ('0' . $end_m) : $end_m;
                    $serviceEndTime = $end_h . ':' . $end_m;


                    $serviceDate->setTimezone(new \DateTimeZone($defaultTimeZone));
                    $this->logger->addDebug("ServiceDate-" . $serviceDate->format('Y-m-d'));
                    $this->logger->addDebug("Start-" . $serviceStartTime);
                    $this->logger->addDebug("End-" . $serviceEndTime);
                    $service = new ServiceInfo();

                    $service->setStatus(ServiceStatus::Pending);
                    $service->setIsConfirmed(false);
                    $service->setClientId($client->getClientId());
                    $service->setClientName($client->getClientName());
                    $service->setTel($client->getTel());
                    $service->setEmail($client->getEmail());
                    $service->setAddress($client->getAddress());
                    $service->setSuburb($client->getSuburb());
                    $service->setPrice($client->getPrice());
                    $service->setPaymentType($client->getPaymentType());
                    $service->setInvoiceNeeded($client->getInvoiceNeeded());
                    $service->setInvoiceTitle($client->getInvoiceTitle());
                    $service->setServiceDate($serviceDate);
                    $service->setServiceStartTime($serviceStartTime);
                    $service->setServiceEndTime($serviceEndTime);
                    $service->setTeamId($teamId);
                    $service->setNotes("");
                    $service->setJobDetail($client->getJobDetail());

                    $service->setCreatorId("auto");
                    $service->setCreateTime(new DateTime('NOW'));
                    $this->serviceDao->save($service);
                }
            }
        }
    }

    private function ProcessServices()
    {
        $this->CreatePendingServices();
        $this->UpdateServiceStatus();

    }

    private function UpdateServiceStatus()
    {
        $allServices = $this->serviceDao->findAllService();

        if ($allServices === null) {
            $this->logger->addDebug("no service");
        }

        foreach ($allServices as $serviceInfo) {
            $defaultTimeZone = date_default_timezone_get();
            $this->logger->addDebug('[Default TimeZone]' . $defaultTimeZone);
            $todayDate = new DateTime('NOW');
            $todayDate->setTimezone(new \DateTimeZone($defaultTimeZone));
            $todayDate = (int)$todayDate->format('Ymd');

            $serviceDate = $serviceInfo->getServiceDate();
            $serviceDate->setTimezone(new \DateTimeZone($defaultTimeZone));
            $serviceDate = (int)$serviceDate->format('Ymd');
            //$todayDate=20150515;
            $this->logger->addDebug('[DATE]' . $todayDate . ' : ' . $serviceDate);
            if ($todayDate === $serviceDate) {
                $curStatus = $serviceInfo->getStatus();
                if ($curStatus === ServiceStatus::Pending) {
                    if ($serviceInfo->getIsConfirmed() === true) {
                        $this->logger->addDebug('[CCC]' . $serviceInfo->getId() . ' : Set Service to Processing');
                        $serviceInfo->setStatus(ServiceStatus::Processing);
                    } else {
                        //Send Notification to remind user to confirm the service
                        $this->logger->addDebug('[AAA]' . $serviceInfo->getId() . ' : Send Notification to remind user to confirm the service');
                        //$serviceInfo->setStatus(ServiceStatus::Processing);
                        continue;
                    }
                } else if ($curStatus === ServiceStatus::Completed) {
                    //Send Notification remind user to write a feedback or comment for the service.
                    $this->logger->addDebug($serviceInfo->getId() . ' : Send Notification remind user to write a feedback or comment for the service.');
                    continue;
                } else {
                    continue;
                }
            } else if (($todayDate + 1) === $serviceDate) {
                if ($serviceInfo->getStatus() === ServiceStatus::Pending && $serviceInfo->getIsConfirmed() === false) {
                    //Send Notification to remind user to confirm the service
                    $this->logger->addDebug($serviceInfo->getId() . ' : Send Notification to remind user to confirm the service.');
                    continue;
                }
            } else if ($todayDate > $serviceDate) {
                $this->logger->addDebug($serviceInfo->getId() . ' : today > serviceDate');
                if ($serviceInfo->getStatus() === ServiceStatus::Pending && $serviceInfo->getIsConfirmed() === false) {
                    //Send Notification to inform the user canceled
                    $this->logger->addDebug($serviceInfo->getId() . ' : Send Notification to inform the user canceled.');
                    $serviceInfo->setStatus(ServiceStatus::Cancelled);
                } else if ($serviceInfo->getStatus() === ServiceStatus::Completed) {
                    $this->logger->addDebug($serviceInfo->getId() . ' : Send Notification remind user to write a feedback or comment for the service..');
                    //Send Notification remind user to write a feedback or comment for the service.
                    continue;
                } else if ($serviceInfo->getStatus() === ServiceStatus::Processing) {
                    $this->logger->addDebug($serviceInfo->getId() . ' : Send Notification remind user to processing is completed');
                    $serviceInfo->setStatus(ServiceStatus::Completed);
                }
            } else {
                continue;
            }

            $this->serviceDao->save($serviceInfo);

        }

    }

    private function CreatePendingServices()
    {
        $clientList = $this->clientDao->findAllActiveClient();
        $nextService = "";
        foreach ($clientList as $client) {
            $frequencyType = $client->getJobDetail()->getFrequency();
            if ($frequencyType === FrequencyType::WhenNeed) {
                continue;
            } else {
                $clientId = $client->getClientId();
                $pendingServices = $this->serviceDao->findPending($clientId);

                $this->logger->addDebug($client->getClientId() . " : " . $client->getClientName());

                if ($pendingServices == null) {
                    $this->logger->addDebug($client->getClientId() . " : no pending service");
                    $lastCompletedService = $this->serviceDao->findLastCompleteService($clientId);

                    if ($lastCompletedService == null) {

                        $lastServiceDate = $client->getStartDate();

                        $lastServiceTeamId = "";
                    } else {
                        $lastServiceDate = $lastCompletedService->getServiceDate();
                        $lastServiceTeamId = $lastCompletedService->getTeamId();
                    }
                    $nextServiceDate = clone $lastServiceDate;

                    if ($frequencyType === FrequencyType::Weekly) {
                        $nextServiceDate = $nextServiceDate->modify("+1 week");
                    } else if ($frequencyType === FrequencyType::Fortnightly) {
                        $nextServiceDate = $nextServiceDate->modify("+2 week");
                    } else if ($frequencyType === FrequencyType::Monthly) {
                        $nextServiceDate = $nextServiceDate->modify("+4 week");
                    } else if ($frequencyType === FrequencyType::TwiceAWeek) {
                        $nextServiceDate = null;//$nextServiceDate->modify("+3 day");
                    } else if ($frequencyType === FrequencyType::WhenNeed) {
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
