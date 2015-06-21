<?php

namespace AppBundle\Controller;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use AppBundle\Document\JobDetail;
use AppBundle\Document\InvoiceHistory;
use \stdClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use \Swift_Message;
use \Swift_Image;
class APIInvoiceController extends Controller
{
    /**
     * @Route("/api/invoice/getMonthInvoice/{dateStr}", name="_api_invoice_test")
     */
    public function findInvoice($dateStr)
    {
        $invoiceGroups = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:InvoiceInfo')
            ->findByMonth($dateStr);
        $list = array();

        foreach($invoiceGroups as $item){
           // $key = $item->getClientId();
            //if(!isset($list[$key])){
            //    $list[$key] = array();
            //}
            //$list[$key][] = $item;
            $list[] = $item;
        }
        $response =  new Response(json_encode($list,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/invoice/sendInvoice", name="_api_sendInvoice")
     */
    public function sendInvoice(Request $request)
    {
        $invoiceHistoryArray = $request->request->get('invoiceHistory');
        $log = new Logger('sendInvoice');
        $log->pushHandler(new StreamHandler($this->container->getParameter('log_dir') .'Invoice.log', Logger::DEBUG));
        $log->addDebug(json_encode($invoiceHistoryArray,JSON_PRETTY_PRINT));
        $invoiceHistory = new InvoiceHistory();
        if($invoiceHistoryArray!=null){
            $invoiceHistory->loadFromArray($invoiceHistoryArray);
        }

        $userInfo= $this->get('security.context')->getToken()->getUser();
        $invoiceHistory->setCreatorId($userInfo->getId());
        $invoiceHistory->setModifyTime(new \DateTime('NOW'));


        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($invoiceHistory);
        $dm->flush();

        $response =  new Response(json_encode($invoiceHistory,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function  sendInvoiceEmail($invoice)
    {
        $message = Swift_Message::newInstance()
            ->setSubject('[Confirm] - That Clean Girls Service')
            ->setFrom('thatcleangirl@gmail.com')
            ->setTo($invoice->getEmail())
            ->setCc('zhongyp.design@gmail.com');
       // $message = $message ->attach(Swift_Attachment::fromPath($pdfPath,'application/pdf'));
        $headerImage = $message->embed(Swift_Image::fromPath($this->get('kernel')->getRootDir().'/../web/images/invoice_header.PNG')) ;
        $data=array('subject'=>'[Invoice] '.$invoice->getInvoiceYM().' - That Clean Girl Service','invoice'=>$invoice,'headerImage'=>$headerImage);
        $emailView =  $this->renderView('AppBundle:email:invoice.html.twig',$data);

        $message = $message ->setBody($emailView,'text/html');

        $this->get('mailer')->send($message);
    }




    /**
     * @Route("/api/invoice/send", name="_api_invoice_send")
     */
    /*public function sendInvoice()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('[Invoice] - That Clean Girls Service')
            ->setFrom('thatcleangirl@gmail.com')
            ->setTo('zhongyp.design@gmail.com');
            //->setCc('zhongyp.design@gmail.com');
        $headerImage = $message->embed(Swift_Image::fromPath('C:\xampp\htdocs\github\ThatCleanGirl\development\dev\tcg\web\images\invoice_header.PNG')) ;
        $message = $message ->setBody(
                '<html>' .
                ' <head></head>' .
                ' <body>' .
                '  Here is an image <img src="' .
                $message->embed(Swift_Image::fromPath('C:\xampp\htdocs\github\ThatCleanGirl\development\dev\tcg\web\images\invoice_header.PNG')) .
                '" alt="Image" />' .
                '  Rest of message' .
                ' </body>' .
                '</html>',
                'text/html'
                //$this->renderView(
                //    'AppBundle:email:invoice.html.twig',
                //    array('title'=>'[Invoice] - That Clean Girls Service','headerImage' =>$headerImage)
                //),
                //'text/html'
            );
        $this->get('mailer')->send($message);

        $response =  new Response(json_encode("Send",JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }*/
	
	/**
     * @Route("/api/invoice/test", name="invoiceTest")
     */
    public function invoiceTest()
    {
        $invoice = $this->get('doctrine_mongodb')
            ->getManager()
           ->getRepository('AppBundle:InvoiceHistory')
           ->findOneBy(array("clientName"=>"111"));
        $headerImage = $this->container->get('router')->getContext()->getBaseUrl().'/images/invoice_header.PNG';
        $data = array('headerImage'=>$headerImage,'invoice'=>$invoice);
        //$response =  new Response(json_encode($invoice,JSON_PRETTY_PRINT));
        //$response->headers->set('Content-Type', 'application/json');
        //return $response;

        return $this->render('AppBundle:email:invoice_test.html.twig',$data);
    }
	
}
