<?php

namespace AppBundle\Controller;
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
class APIClientController extends Controller
{

    /**
     * @Route("/api/client/getClientList", name="_api_getClientList")
     */
    public function getClientList()
    {
        $clientList = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->findAllAvailableClient();
       // print_r(json_encode($clientList,JSON_PRETTY_PRINT));
        $response =  new Response(json_encode($clientList,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/client/getClientInfo/{clientId}", name="_api_getClientInfo")
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
     * @Route("/api/client/updateClientInfo", name="_api_updateClientInfo")
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

        $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:NotificationInfo')
            ->UpdateClientBirthdayNotification($clientInfo);

        $response =  new Response(json_encode($clientInfo));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/client/updateClientJobDetail", name="_api_updateClientJobDetail")
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
     * @Route("/api/client/updateClientReminderInfo", name="_api_updateClientReminderInfo")
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
        $clientInfo =  $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:ClientInfo')
            ->updateClientReminderInfo($clientId,$reminderInfo);

        $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:NotificationInfo')
            ->updateClientCleanNotification($clientInfo);

        $response =  new Response(json_encode($reminderInfo));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/client/updateClientPaymentInfo", name="_api_updateClientPaymentInfo")
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
     * @Route("/api/client/getClientComments/{clientId}", name="_api_getClientComments")
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
     * @Route("/api/client/postClientComment", name="_api_postClientComment")
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
     * @Route("/api/client/deleteClientComment", name="_api_deleteClientComment")
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
     * @Route("/api/client/deleteClientInfo/{clientId}", name="_api_deleteClientInfo")
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
     * @Route("/api/client/saveFullClientInfo", name="_api_saveFullClientInfo")
     */
    public function saveFullClientInfo(Request $request)
    {
        $clientInfoArray = $request->request->get('fullClientInfo');
        $log = new Logger('serviceHistory');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'serviceHistory.log', Logger::DEBUG));
        $log->addDebug(json_encode($clientInfoArray,JSON_PRETTY_PRINT));
        $clientInfo = new ClientInfo();
        if($clientInfoArray!=null){
            $clientInfo->loadFromArray($clientInfoArray);
        }

        $userInfo= $this->get('security.context')->getToken()->getUser();
        $clientInfo->setCreatorId($userInfo->getId());
        $clientInfo->setModifyTime(new \DateTime('NOW'));
        $clientInfo->setAvailable(true);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($clientInfo);
        $dm->flush();

        $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:NotificationInfo')
            ->UpdateClientBirthdayNotification($clientInfo);
        $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:NotificationInfo')
            ->updateClientCleanNotification($clientInfo);


        $response =  new Response(json_encode($clientInfo,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/client/create_client_info", name="_api_create_client_info")
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

}
