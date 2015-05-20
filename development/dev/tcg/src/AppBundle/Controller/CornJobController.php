<?php

namespace AppBundle\Controller;
use AppBundle\Document\ClientInfo;
use AppBundle\Document\FrequencyType;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use \stdClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class CornJobController extends Controller
{


    /**
     * @Route("/job/service_create_pending", name="_job_service_create_pending")
     */
    public function serviceHistory()
    {
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));

        $clientList = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->findAllActiveClient();
        $nextService = "";
        foreach($clientList as $client){
            $log->addDebug($client->getClientId() ." : ". $client->getClientName());

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

                    //$log->addDebug(json_encode($lastServiceDate->format('Y-m-d\TH:i:sO'),JSON_PRETTY_PRINT));
                    //$log->addDebug(json_encode($nextServiceDate->format('Y-m-d\TH:i:sO'),JSON_PRETTY_PRINT));

                }
            }
        }

        $response = new Response(json_encode($nextService));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/job/test_add_service_Info", name="_test_add_service_Info")
     */
    public function addServiceInfo()
    {
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));

        $service = new ServiceInfo();

        $service->setClientId("1111-1111");
        $service->setStatus(ServiceStatus::Completed);
        $service->setServiceDate(new DateTime('NOW'));
        $service->setPrice(111);
        $service->setPaymentType("cash");
        $service->setInvoiceNeeded(false);
        $service->getTeamId("TeamA");
        $service->setCreatorId("auto");
        $service->setCreateTime(new DateTime("Now"));
        $service->setFeedback("nothing");

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($service);
        $dm->flush();

        $response = new Response(json_encode($service,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/job/serviceConfirmed", name="_api_service_Confirmed")
     */
    public function serviceConfirmedList(Request $request)
    {
        $filters = $request->request->get('filters');
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));

        $log->addDebug(json_encode($filters));

        $serviceDao = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ServiceInfo');

        $services = $serviceDao->findConfirmed($filters);

        $list = array();
        foreach($services as $item){
            $list[]=$item;
        }

        $response = new Response(json_encode($list,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/job/serviceUnconfirmed", name="_api_service_unconfirmed")
     */
    public function serviceUnconfirmedList()
    {
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));

        $serviceDao = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ServiceInfo');

        $services = $serviceDao->findUnconfirmed();

        $list = array();
        foreach($services as $item){
            $list[]=$item;
        }

        $response = new Response(json_encode($list,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/job/saveService", name="_api_confirm_service")
     */
    public function saveService(Request $request)
    {

        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));

        $serviceInfoArray = $request->request->get('serviceInfo');

        $serviceInfo = new ServiceInfo();
        $serviceInfo->loadFromArray($serviceInfoArray);

        $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ServiceInfo')
            ->save($serviceInfo);

        $response = new Response("SUCCESS");
        $response->headers->set('Content-Type', 'text/*');
        return $response;
    }


}
