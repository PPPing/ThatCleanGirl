<?php

namespace AppBundle\Controller;
use AppBundle\Document\JobDetail;
use AppBundle\Document\JobDetailKey;
use AppBundle\Document\JobDetailPet;
use AppBundle\Document\JobDetailItem;
use AppBundle\Document\ReminderInfo;
use \stdClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Document\ClientInfo;
use AppBundle\Document\ClientComment;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class APIController extends Controller
{


    /**
     * @Route("/api/menu_info", name="_api_menu_info")
     */
    public function GetMenuInfo()
    {
        $components = array();

        $client = new stdClass();
        $client->id = "topMenu1";
        $client->name = "Client Management";
        $client->modules = array();

        $dashboard = new stdClass();
        $dashboard->id = "dashboard";
        $dashboard->name = "dashboard";
        $dashboard->url = "";
        $dashboard->isSubModule = false;


        $client_list = new stdClass();
        $client_list->id = "client-list";
        $client_list->name = "Client List";
        $client_list->url = "";
        $client_list->isSubModule = false;

        $new_client = new stdClass();
        $new_client->id = "new-client";
        $new_client->name = "New Client";
        $new_client->url = "";
        $new_client->isSubModule = false;

        array_push($client->modules,$dashboard,$client_list,$new_client);


        $staff = new stdClass();
        $staff->id = "topMenu2";
        $staff->name = "Staff Management";
        $staff->modules = array();

        $staff_list = new stdClass();
        $staff_list->id = "staff-list";
        $staff_list->name = "Staff List";
        $staff_list->url = "";
        //$staff_list->isSubModule = false;

        array_push($staff->modules,$staff_list);

        array_push($components,$client);

        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            array_push($components,$staff);
        }


        $response = new Response(json_encode($components));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/user_info", name="_api_user_info")
     */
    public function userProfile()
    {

        $curUser= $this->get('security.context')->getToken()->getUser();
        $response = new Response(json_encode($curUser));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/check_clientId", name="_api_check_clientId")
     */
    public function checkClientId()
    {


        $response =  new Response(json_encode("",JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/api/getClientList", name="_api_getClientList")
     */
    public function getClientList()
    {
        $clientList = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->findAll();
       // print_r(json_encode($clientList,JSON_PRETTY_PRINT));
        $response =  new Response(json_encode($clientList,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/getClientInfo/{clientId}", name="_api_getClientInfo")
     */
    public function getClientInfo($clientId)
    {
        $clientInfo = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->findOneBy(array("clientId"=>$clientId));

        $response =  new Response(json_encode($clientInfo,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/updateClientInfo", name="_api_updateClientInfo")
     *
     */
    public function updateClientInfo(Request $request)
    {
        $clientInfo = $request->request->get('clientInfo');
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));
        $log->addDebug(json_encode($clientInfo,JSON_PRETTY_PRINT));
        $clientInfo =  $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->updateClientBasicInfo($clientInfo);

        $response =  new Response(json_encode($clientInfo));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/updateClientJobDetail", name="_api_updateClientJobDetail")
     *
     */
    public function updateClientJobDetail(Request $request)
    {
        $jobDetail = $request->request->get('jobDetail');

        $clientId = $request->request->get('clientId');

        $result =  $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->updateClientJobDetail($clientId,$jobDetail);

        $response =  new Response(json_encode($jobDetail));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/updateClientReminderInfo", name="_api_updateClientReminderInfo")
     *
     */
    public function updateClientReminderInfo(Request $request)
    {
        $reminderInfoArray = $request->request->get('reminderInfo');

        $clientId = $request->request->get('clientId');
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));
        $log->addDebug(json_encode($reminderInfoArray,JSON_PRETTY_PRINT));
        $reminderInfo = new ReminderInfo();
        if($reminderInfoArray!=null){
            $reminderInfo->loadFromArray($reminderInfoArray);
        }
        $result =  $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->updateClientReminderInfo($clientId,$reminderInfo);

        $response =  new Response(json_encode($reminderInfo));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/updateClientPaymentInfo", name="_api_updateClientPaymentInfo")
     *
     */
    public function updateClientPaymentInfo(Request $request)
    {
        $clientInfo = $request->request->get('clientInfo');

        $result =  $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->updateClientPaymentInfo($clientInfo);

        $response =  new Response(json_encode($clientInfo));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/getClientComments/{clientId}", name="_api_getClientComments")
     */
    public function getClientComments($clientId)
    {
        $clientInfo = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientComment')
            ->findBy(array("clientId"=>$clientId),array("createDate"));

        $response =  new Response(json_encode($clientInfo,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/postClientComment", name="_api_postClientComment")
     */
    public function postClientComment(Request $request)
    {
        $clientId = $request->request->get('clientId');
        $content = $request->request->get('content');

        $userInfo= $this->get('security.context')->getToken()->getUser();

        $comment = new ClientComment();

        $comment->setClientId($clientId);
        $comment->setAuthorId($userInfo->getId());
        $comment->setAuthorName($userInfo->getUsername());
        $comment->setAuthorTitle($userInfo->getTitle());
        $comment->setContent($content);
        $comment->setCreateDate(new DateTime('NOW'));

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($comment);
        $dm->flush();

        $response =  new Response(json_encode($comment,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/deleteClientComment", name="_api_deleteClientComment")
     */
    public function deleteClientComment(Request $request)
    {
        $comment = $request->request->get('comment');

        $userInfo= $this->get('security.context')->getToken()->getUser();

        if($userInfo->getId()!=$comment["authorId"]&&true !== $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $response =  new Response("You are not allow to delete this comment.");
        }else{
            $clientInfo = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('AppBundle:ClientComment')
                ->createQueryBuilder()
                ->findAndRemove()
                ->field('id')->equals($comment['id'])
                ->getQuery()
                ->execute();
            $response =  new Response("Success");
        }

        $response->headers->set('Content-Type', 'text/*');
        return $response;
    }

    /**
     * @Route("/api/deleteClientInfo/{clientId}", name="_api_deleteClientInfo")
     */
    public function deleteClientInfo($clientId)
    {
        $clientInfo = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->createQueryBuilder()
            ->findAndRemove()
            ->field("clientId")->equals($clientId)
            ->getQuery()
            ->execute();

        $response =  new Response("Success");
        $response->headers->set('Content-Type', 'text/*');
        return $response;
    }

    /**
     * @Route("/api/create_client_info", name="_api_create_client_info")
     */
    public function createClientInfo()
    {
        $userInfo= $this->get('security.context')->getToken()->getUser();
        $clientId =  $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->generateClientId();

        $clientInfo = new ClientInfo();
        $clientInfo->setClientId($clientId);
        $clientInfo->setCreatorId($userInfo->getId());

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($clientInfo);
        $dm->flush();
        $response =  new Response(json_encode($clientInfo,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/api/test_create_client_info", name="_api_test_create_client_info")
     */
    public function testCreateClientInfo()
    {
        $clientId =  $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->generateClientId();
        $clientInfo = new ClientInfo();

        $clientInfo->setClientId($clientId);
        $clientInfo->setClientName("TestClient");
        $clientInfo->setDriverLicense("D001231");
        $clientInfo->setTel("000-1223-2123");
        $clientInfo->setBirthday(new DateTime("2001-01-21"));

        $clientInfo->setAddress("adfadf adfadfa adfadf a dfadfasd");
        $clientInfo->setIsActive(false);

        $clientInfo->setStartDate(new DateTime('Now'));
        $clientInfo->setPrice(998);

        $r1=new stdClass();
        $r1->key = "week 1";
        $r1->value = "adfadfa dfa dfasd adf a";
        $r2=new stdClass();
        $r2->key = "week 2";
        $r2->value = "adfadfa dfa dfasd adf a";
        $r3=new stdClass();
        $r3->key = "week 3";
        $r3->value = "adfadfa dfa dfasd adf a";
        $r4=new stdClass();
        $r4->key = "week 3";
        $r4->value = "adfadfa dfa dfasd adf a";

        $clientInfo->setRotations(array($r1,$r2,$r3,$r4));
        $clientInfo->setRemark("adfafadasdfa");

        $clientInfo->setPaymentType("cash");
        $clientInfo->setInvoiceNeeded(true);
        $clientInfo->setInvoiceTitle("adf company");


        $jobDetail = new JobDetail();

        $jobDetail->setFrequency("weekly");
        $jobDetail->setAttention("adfadf dfad adfa asfas asfa ");

        $key = new JobDetailKey();
        $key->setHas(true);
        $key->setKeeping("keptInDoor");
        $key->setAlarmIn("8:00 AM");
        $key->setAlarmOut("6:00 PM");

        $jobDetail->setKey($key);

        $pet = new JobDetailPet();
        $pet->setHas(true);
        $pet->setKeeping("keptInDoor");
        $jobDetail->setPet($pet);

        $jobItem = new JobDetailItem();
        $jobItem->setName("Formal lounge");
        $jobItem->setAmount(1);
        $jobItem->setRequest("adfadf asdfas  asdfafa  adfa ");

        $jobItem1 = new JobDetailItem();
        $jobItem1->setName("Formal lounge");
        $jobItem1->setAmount(1);
        $jobItem1->setRequest("adfadf asdfas  asdfafa  adfa ");

        $jobItem2 = new JobDetailItem();
        $jobItem2->setName("Formal lounge");
        $jobItem2->setAmount(1);

        $jobItem2->setRequest("adfadf asdfas  asdfafa  adfa ");

        //$jobDetail->setItems(array($jobItem,$jobItem1,$jobItem2));
        $jobDetail->addItem($jobItem);
        $jobDetail->addItem($jobItem1);
        $jobDetail->addItem($jobItem2);


        $clientInfo->setJobDetail($jobDetail);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($clientInfo);
        $dm->flush();
        $response =  new Response(json_encode($clientInfo,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/api/serviceHistory/{clientId}", name="_api_serviceHistory")
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


}
