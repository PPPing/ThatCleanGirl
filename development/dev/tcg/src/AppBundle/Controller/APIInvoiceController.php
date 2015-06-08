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
use \Swift_Message;
use \Swift_Image;
class APIInvoiceController extends Controller
{
    /**
     * @Route("/api/invoice/send", name="_api_invoice_send")
     */
    public function sendInvoice()
    {
        $message = Swift_Message::newInstance()
            ->setSubject('[Invoice] - That Clean Girls Service')
            ->setFrom('thatcleangirl@gmail.com')
            ->setTo('mialeung2010@hotmail.com')
            ->setCc('zhongyp.design@gmail.com');
        $headerImage = $message->embed(Swift_Image::fromPath('C:\xampp\htdocs\github\ThatCleanGirl\development\dev\tcg\web\images\invoice_header.PNG')) ;
        $message = $message ->setBody(
               /* '<html>' .
                ' <head></head>' .
                ' <body>' .
                '  Here is an image <img src="' .
                $message->embed(Swift_Image::fromPath('C:\xampp\htdocs\github\ThatCleanGirl\development\dev\tcg\web\images\invoice_header.PNG')) .
                '" alt="Image" />' .
                '  Rest of message' .
                ' </body>' .
                '</html>',
                'text/html'*/
                $this->renderView(
                    'AppBundle:email:invoice.html.twig',
                    array('title'=>'[Invoice] - That Clean Girls Service','headerImage' =>$headerImage)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $response =  new Response(json_encode("Send",JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
