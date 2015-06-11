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
        $new_client->changeAlart = true;
        $new_client->alartMsg = "All your non-saved information will lost.";

		$invoice_list = new stdClass();
        $invoice_list->id = "invoice-list";
        $invoice_list->name = "Invoice";
        $invoice_list->url = "";
		$invoice_list->isSubModule = false;
		
        array_push($client->modules,$dashboard,$client_list,$new_client,$invoice_list);


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


}
