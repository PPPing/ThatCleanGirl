<?php

namespace AppBundle\Controller;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use AppBundle\Document\JobDetail;
use \stdClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class APIServiceController extends Controller
{
	/**
     * @Route("/api/service/holiday", name="_api_service_holiday")
     */
    public function serviceHoliday()
    {
		$url ='http://www.webcal.fi/cal.php?id=136&format=json&start_year=2015&end_year=next_year&tz=Australia%2FSydney';
        $ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);

        $response =  new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
	
    /**
     * @Route("/api/service/history/{clientId}", name="_api_service_history")
     */
    public function serviceHistory($clientId)
    {
        $serviceHistory = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ServiceInfo')
            ->findCompleteService($clientId);

        $result = array();
        foreach($serviceHistory as $info){
            $result[]=$info;
        }

        $response =  new Response(json_encode($result,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/api/service/all", name="_api_service_all")
     */
    public function serviceList()
    {
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));

        $serviceDao = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ServiceInfo');

        $services = $serviceDao->findAll();
        $list = array();
        foreach($services as $item){
            $list[]=$item;
        }
        $response = new Response(json_encode($list,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/service/add", name="_add")
     */
    public function addServiceInfo()
    {
        $log = new Logger('Service');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));

        $service = new ServiceInfo();

        $service->setStatus(ServiceStatus::Pending);
        $service->setIsConfirmed(false);
        $service->setClientId("1111-1111");
        $service->setClientName("Test Client");
        $service->setTel("1123-1233");
        $service->setEmail("test@email.com");
        $service->setAddress("adsfasdfa");
        $service->setSuburb("asdfasdfasdf");
        $service->setPrice(990);
        $service->setPaymentType("cash");
        $service->setInvoiceNeeded(true);
        $service->setInvoiceTitle("company");
        $service->setServiceDate(new \DateTime('NOW'));
        $service->setServiceStartTime("10:00");
        $service->setServiceEndTime("15:00");
        $service->setTeamId("TeamA");
        $service->setNotes("nothing");
        $service->setJobDetail(new JobDetail());

        $service->setCreatorId("auto");
        $service->setCreateTime(new DateTime("Now"));


        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($service);
        $dm->flush();

        $response = new Response(json_encode($service,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/service/save", name="_api_service_save")
     */
    public function saveService(Request $request)
    {
        $log = new Logger('Service');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'Service.log', Logger::DEBUG));

        $serviceInfoArray = $request->request->get('serviceInfo');

        $log->addDebug("[A]  ".json_encode($serviceInfoArray,JSON_PRETTY_PRINT));

        $serviceInfo = new ServiceInfo();
        $serviceInfo->loadFromArray($serviceInfoArray);
        $log->addDebug("[B]  ".json_encode($serviceInfo,JSON_PRETTY_PRINT));
        $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ServiceInfo')
            ->save($serviceInfo);
        $response = new Response("SUCCESS");
        $response->headers->set('Content-Type', 'text/*');
        return $response;
    }
}
