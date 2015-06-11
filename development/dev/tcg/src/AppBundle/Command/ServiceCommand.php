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
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class ServiceCommand extends ContainerAwareCommand
{
    protected $clientDao;
    protected $serviceDao;
	protected $notificationDao;
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
			
			$this->logger = new Logger('ServiceCommand');
            $this->logger->pushHandler(new StreamHandler($this->getContainer()->getParameter('log_dir') . 'ServiceCommand.log'));
            $type = $input->getArgument('type');
            $text = 'Type - ' . $type . ' Started';
            if ($type === 'create') {
                $output->writeln($text);
                //$this->CreatePendingServices();
                $this->CreateServices();
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
                if ($serviceDate > $today && $serviceDate <= $today->modify("+14 day")) {
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

        $defaultTimeZone = date_default_timezone_get();
        $today = new \DateTime('NOW');
        $today->setTimezone(new \DateTimeZone($defaultTimeZone));
        $today_year = $today->format('Y');
        $today_md = $today->format('md');

        foreach ($clientList as $client) {
                $birthday = $client->getBirthday();

                $birthday->setTimezone(new \DateTimeZone($defaultTimeZone));
                $birthday_year = $birthday->format('Y');
                $birthday_md = $birthday->format('md');

                if($birthday_md>=$today_md&&$birthday_md<= ((int)$today_md +31)){
                    $notifyDate = date_create_from_format('Ymd',$today_year.$birthday_md);
                    $notificationInfo = new NotificationInfo();
                    $notificationInfo->setClientId($client->getClientId());
                    $notificationInfo->setStatus(NotificationStatus::Unconfirmed);
                    $notificationInfo->setType(NotificationType::Birthday);
                    $notificationInfo->setDate($notifyDate);
                    $notificationInfo->setTitle("Birthday - ". $client->getClientName());
					$notificationInfo->setClientName($client->getClientName());
                    $notificationInfo->setTel($client->getTel());
                    $notificationInfo->setEmail($client->getEmail());
                    $notificationInfo->setAddress($client->getAddress());
                    $notificationInfo->setSuburb($client->getSuburb());
                    
					$this->logger->debug($client->getClientName().' : '.$notifyDate->format('Y-m-d'));

                    $this->dm->persist($notificationInfo);
                }
        }
        $this->dm->flush();
    }
	
	/**
     * update every month 30 days
     */
    public function updateCleanReminder()
    {
        $this->notificationDao->archiveCleanReminderNotification();

        $clientList = $this->clientDao->findAllAvailableClient();

        $defaultTimeZone = date_default_timezone_get();

        foreach ($clientList as $client) {
			$notificationList = array();
            $reminderInfo = $client->getReminderInfo();
            $methods = get_class_methods($reminderInfo);
            foreach($methods as $method){
                if (strpos($method, 'get') === 0 && $this->endsWith($method, 'Date') === true) {
                    $date = $reminderInfo->$method();
                    if($date===null){
                        continue;
                    }
                    //$this->logger->debug('- '.$method .':'.$date->format('Y-m-d'));
                    $today = new DateTime('NOW');
                    if($date > $today && $date <= $today->modify("+30 day")){
                        //$this->logger->debug('* '.$method .':'.$date->format('Y-m-d'));
						$dateKey = $date->format('md');
						$itemKey = substr(substr($method,3),0,-4);
						if(!empty($notificationList[$dateKey])){
							$notificationInfo = $notificationList[$dateKey];
						}else{
							$notificationInfo = new NotificationInfo();
							$notificationInfo->setTitle("Spring Clean Reminder");
							$notificationInfo->setClientId($client->getClientId());
							$notificationInfo->setStatus(NotificationStatus::Unconfirmed);
							$notificationInfo->setType(NotificationType::Clean);
							$notificationInfo->setDate($date);
							$notificationInfo->setClientName($client->getClientName());
							$notificationInfo->setTel($client->getTel());
							$notificationInfo->setEmail($client->getEmail());
							$notificationInfo->setAddress($client->getAddress());
							$notificationInfo->setSuburb($client->getSuburb());
						}
						
						$items = $notificationInfo->getItems();
						//$item = new \stdClass();
						//$item->$itemKey= true;
						$items[] = $itemKey;
						
						$notificationInfo->setItems($items);
						$notificationList[$dateKey] = $notificationInfo;
                    }
                }
            }
			
			foreach($notificationList as $notify){
				$this->dm->persist($notify);
			}
        }
        $this->dm->flush();
    }
	
	public  static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}


