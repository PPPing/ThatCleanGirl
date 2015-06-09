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

    private function CreateServices()
    {
        $clientList = $this->clientDao->findAllActiveClient();
        $defaultTimeZone = date_default_timezone_get();
        foreach ($clientList as $client) {
            $frequencyType = $client->getJobDetail()->getFrequency();
            if ($frequencyType === FrequencyType::WhenNeed) {
                continue;
            } else {
                $clientId = $client->getClientId();
                $teamId = '';

                $offset_h = 2;
                $offset_m = 0;
                $this->logger->addDebug($client->getClientId() . " : " . $client->getClientName());
                $lastService = $this->serviceDao->findLastService($clientId);
                if ($lastService === null) {
                    $serviceDate = $client->getServiceDate();
                } else {
                    $lastServiceDate = $lastService->getServiceDate();
                    $serviceDate = clone $lastServiceDate;
                    if ($frequencyType === FrequencyType::Weekly) {
                        $serviceDate->modify("+1 week");
                    } else if ($frequencyType === FrequencyType::Fortnightly) {
                        $serviceDate->modify("+2 week");
                    } else if ($frequencyType === FrequencyType::Monthly) {
                        $serviceDate->modify("+4 week");
                    } else if ($frequencyType === FrequencyType::TwiceAWeek) {
                        //$nextServiceDate = null;//$nextServiceDate->modify("+3 day");
                    }
                    $teamId = $lastService->getTeamId();

                    $serviceStartTime = $lastService->getServiceStartTime();
                    $serviceEndTime = $lastService->getServiceEndTime();

                    $start = explode(":", $serviceStartTime);
                    $start_h = $start[0];
                    $start_m = $start[1];
                    $end = explode(":", $serviceEndTime);
                    $end_h = $end[0];
                    $end_m = $end[1];

                    $offset_h = (int)$end_h - (int)$start_h;
                    $offset_m = (int)$end_m - (int)$start_m;
                    if ($offset_m < 0) {
                        $offset_h -= 1;
                        $offset_m = 60 - $offset_m;
                    }
                }

                $today = new DateTime('NOW');
                if ($serviceDate > $today && $serviceDate <= $today->modify("+7 day")) {
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

}


