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
use AppBundle\Document\HolidayInfo;
use AppBundle\Document\NotificationInfo;
use AppBundle\Document\NotificationStatus;
use AppBundle\Document\NotificationType;
use AppBundle\Document\InvoiceInfo;
use AppBundle\Document\InvoiceStatus;
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class ServiceCommand extends ContainerAwareCommand
{
    protected $clientDao;
    protected $serviceDao;
	protected $notificationDao;
    protected $invoiceDao;
	protected $dm;
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
			$this->dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
            $this->clientDao = $this->dm->getRepository('AppBundle:ClientInfo');
            $this->serviceDao = $this->dm->getRepository('AppBundle:ServiceInfo');
            $this->notificationDao = $this->dm->getRepository('AppBundle:NotificationInfo');
            $this->invoiceDao = $this->dm->getRepository('AppBundle:InvoiceInfo');
			$this->logger = new Logger('ServiceCommand');
            $this->logger->pushHandler(new StreamHandler($this->getContainer()->getParameter('log_dir') . 'ServiceCommand.log'));
            $type = $input->getArgument('type');
            $text = 'Type - ' . $type . ' Started';
            if ($type === 'service') {
                $output->writeln($text);
                $this->CreateServices();
                $this->CreateServices();
                $this->UpdateServices();
                $text = 'Type - ' . $type . ' Finish';
            } else if ($type === 'holiday') {
                $output->writeln($text);
                //$this->CreatePendingServices();
                $this->updateHoliday();
                $text = 'Type - ' . $type . ' Finish';
			}else if ($type === 'birthday') {
                $output->writeln($text);
                //$this->CreatePendingServices();
                $this->updateBirthday();
                $text = 'Type - ' . $type . ' Finish';
			}else if ($type === 'clean') {
                $output->writeln($text);
                //$this->CreatePendingServices();
                $this->updateCleanReminder();
                $text = 'Type - ' . $type . ' Finish';
			}else {
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

                $this->logger->addDebug('[Create] '.$client->getClientId() . " : " . $client->getClientName());
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
                }

                $today = new DateTime('NOW');
                if ($serviceDate > $today && $serviceDate <= $today->modify("+14 day")) {
                    $serviceStartTime = $client->getServiceTime();

                    $serviceDate->setTimezone(new \DateTimeZone($defaultTimeZone));
                    $this->logger->addDebug("ServiceDate-" . $serviceDate->format('Y-m-d'));
                    $this->logger->addDebug("Start-" . $serviceStartTime);
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

    public function UpdateServices(){
        $defaultTimeZone = date_default_timezone_get();
        $pendingServiceList = $this->serviceDao->findPendingService();
        foreach ($pendingServiceList as $serviceInfo) {
            $serviceDate = $serviceInfo->getServiceDate();
            $serviceDate->setTimezone(new \DateTimeZone($defaultTimeZone));
            //$this->logger->addDebug('!!! '.$serviceInfo->getClientId() . " - " . $serviceInfo->getClientName() .$serviceDate->format('Y-m-d'));
            $today = new DateTime('NOW');
            if($serviceDate<$today){

                if($serviceInfo->getIsConfirmed()==true){
                    if($serviceInfo->getInvoiceNeeded()){
                        $this->logger->addDebug('[Update] Confirmed '.$serviceInfo->getClientId() . " - " . $serviceInfo->getClientName() .$serviceDate->format('Y-m-d'));

                        $invoiceInfo = new InvoiceInfo();
                        $invoiceInfo->setStatus(ServiceStatus::Pending);
                        $invoiceInfo->setIsConfirmed(false);
                        $invoiceInfo->setClientId($serviceInfo->getClientId());
                        $invoiceInfo->setClientName($serviceInfo->getClientName());
                        $invoiceInfo->setTel($serviceInfo->getTel());
                        $invoiceInfo->setEmail($serviceInfo->getEmail());
                        $invoiceInfo->setAddress($serviceInfo->getAddress());
                        $invoiceInfo->setSuburb($serviceInfo->getSuburb());
                        $invoiceInfo->setPrice($serviceInfo->getPrice());
                        $invoiceInfo->setPaymentType($serviceInfo->getPaymentType());
                        $invoiceInfo->setInvoiceNeeded($serviceInfo->getInvoiceNeeded());
                        $invoiceInfo->setInvoiceTitle($serviceInfo->getInvoiceTitle());
                        $invoiceInfo->setServiceDate($serviceInfo->getServiceDate());
                        $invoiceInfo->setServiceStartTime($serviceInfo->getServiceStartTime());

                        $invoiceInfo->setCreatorId("auto");
                        $invoiceInfo->setCreateTime(new DateTime('NOW'));

                        $this->invoiceDao->save($invoiceInfo);
                    }
                    $serviceInfo->setStatus(ServiceStatus::Completed);
                }else{
                    $this->logger->addDebug('[Update] '.$serviceInfo->getClientId() . " - " . $serviceInfo->getClientName() .$serviceDate->format('Y-m-d'));
                    $serviceInfo->setStatus(ServiceStatus::Cancelled);
                }
                $this->serviceDao->save($serviceInfo);

            }else{
                continue;
            }
        }
    }

	/*
	*update every year
	*/
	private function updateHoliday(){
        $this->dm->getDocumentCollection('AppBundle\Document\HolidayInfo')->drop();
		$url ='http://www.webcal.fi/cal.php?id=136&format=json&start_year=2015&end_year=next_year&tz=Australia%2FSydney';
        $ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
       
        $holidaysData=json_decode($data);
        foreach($holidaysData as $item){
            $this->logger->addDebug($item->name);
            $holiday = new HolidayInfo();
            $holiday->setTitle($item->name);
            $holiday->setStart($item->date);
            $this->dm->persist($holiday);
        }
        $this->dm->flush();
        
	}
	
	/**
     * update every month 31 days
     */
    public function updateBirthday()
    {
        
        $this->notificationDao->archiveBirthdayNotification();

        $clientList = $this->clientDao->findAllAvailableClient();


        foreach ($clientList as $client) {
            $this->notificationDao->UpdateClientBirthdayNotification($client);
        }
    }
	
	/**
     * update every month 31 days
     */
    public function updateCleanReminder()
    {
        $this->notificationDao->archiveCleanReminderNotification();

        $clientList = $this->clientDao->findAllAvailableClient();

        foreach ($clientList as $client) {
            $this->notificationDao->UpdateClientCleanNotification($client);
        }
    }
}


