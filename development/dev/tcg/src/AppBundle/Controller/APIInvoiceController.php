<?php

namespace AppBundle\Controller;
use AppBundle\Document\ServiceInfo;
use AppBundle\Document\ServiceStatus;
use AppBundle\Document\JobDetail;
use AppBundle\Document\InvoiceHistory;
use AppBundle\Document\InvoiceStatus;
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
use \Swift_Attachment;
class APIInvoiceController extends Controller
{
	
	/**
     * @Route("/api/invoice/getInvoiceHistory/{invoiceYM}", name="_api_invoice_history")
     */
    public function findInvoiceHistory($invoiceYM)
    {
        $invoiceHistory = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('AppBundle:InvoiceHistory')
			->findBy(array("invoiceYM"=>(int)$invoiceYM),array("invoiceDate"=>"DESC"));
        $list = array();

        foreach($invoiceHistory as $item){
            $list[] = $item;
        }
        $response =  new Response(json_encode($invoiceHistory,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
	
	
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
        $defaultTimeZone = date_default_timezone_get();
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
	    $this->sendInvoiceEmail($invoiceHistory);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($invoiceHistory);
        $dm->flush();

		$invoiceInfoDao = $this->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:InvoiceInfo');
		
		foreach($invoiceHistory->getItems() as $item){
			$log->addDebug($item['invoiceId']);
			$invoiceInfoDao->setStatus($item['invoiceId'],InvoiceStatus::Sent);
		}
		
        $response =  new Response(json_encode($invoiceHistory,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
	

    private function  sendInvoiceEmail($invoice)
    {
		$defaultTimeZone = date_default_timezone_get();
		$today=new DateTime('Now');
        $today->setTimezone(new \DateTimeZone($defaultTimeZone));
		$subject = '[Invoice] #'.$invoice->getInvoiceYM().' - That Clean Girl Service';
		
		$data=array('subject'=>'subject','invoice'=>$invoice);
        $pdfView = $this->renderView('AppBundle:email:invoice_pdf.html.twig',$data);
        $pdfPath = $this->container->getParameter('pdf_dir').'[Invoice]#'.$invoice->getInvoiceYM().'_'.$invoice->getClientId().'_'.$today->format('Y_m_d_his').'.pdf';
        $pdf = $this->container->get('knp_snappy.pdf');
        $pdf->generateFromHtml($pdfView,$pdfPath,array(),true);
			
		
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('thatcleangirl@gmail.com')
            ->setTo($invoice->getEmail());
            //->setCc('zhongyp.design@gmail.com');
        $message = $message ->attach(Swift_Attachment::fromPath($pdfPath,'application/pdf'));
        $headerImage = $message->embed(Swift_Image::fromPath($this->get('kernel')->getRootDir().'/../web/images/invoice_header.PNG')) ;
        $data=array('subject'=>$subject ,'invoice'=>$invoice,'headerImage'=>$headerImage);
        $emailView =  $this->renderView('AppBundle:email:invoice.html.twig',$data);

        $message = $message ->setBody($emailView,'text/html');

        $this->get('mailer')->send($message);
    }


	
	/**
     * @Route("/api/invoice/test", name="invoiceTest")
     */
    public function invoiceTest()
    {
        $defaultTimeZone = date_default_timezone_get();
        $invoice = $this->get('doctrine_mongodb')
            ->getManager()
           ->getRepository('AppBundle:InvoiceHistory')
           ->findOneBy(array("clientName"=>"111"));
        $headerImage = $this->container->get('router')->getContext()->getBaseUrl().'/images/invoice_header.PNG';
        $subject = '[Invoice] #'.$invoice->getInvoiceYM().' - That Clean Girl Service';
		
		$data = array('subject'=>$subject,'headerImage'=>$headerImage,'invoice'=>$invoice);
        return $this->render('AppBundle:email:invoice_test.html.twig',$data);
    }
	
}
