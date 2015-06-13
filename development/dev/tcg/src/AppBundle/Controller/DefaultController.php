<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Document\Product;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $userInfo= $this->get('security.context')->getToken()->getUser();

        return $this->render('AppBundle:default:index.html.twig',
            array(
                'userInfo' =>  $userInfo,
            )
            );
    }

    

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $curUser= $this->get('security.context')->getToken()->getUser();

        return $this->render('AppBundle:default:admin.html.twig',
            array(
                'userInfo' =>  $curUser,
            )
        );
    }

    /**
     * @Route("/admin2", name="admin2")
     *
     */
    public function admin2Action()
    {

         if (false === $this->get('security.authorization_checker')->isGranted('ROLE_GOD')) {
             //throw $this->createAccessDeniedException('Unable to access this page!');
             return new Response("access denial !!!!!@@@@!!!");
             //throw new Exception("access denial !!!!!@@@@!!!");
         }
        $curUser= $this->get('security.context')->getToken()->getUser();

        return $this->render('AppBundle:default:admin.html.twig',
            array(
                'userInfo' =>  $curUser,
            )
        );
    }
    /**
     * @Route("/admin3", name="admin3")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function admin3Action()
    {

        $userInfo = $this->getUser();
        $userInfo = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('AppBundle:default:admin.html.twig',
            array(
                'userInfo' =>  $userInfo,
            )
        );
    }

    /**
     * @Route("/add_product", name="add_product")
     *
     */
    public function mongoAction()
    {
        $product = new Product();
        $product->setName('B Foo Bar');
        $product->setPrice('2222');

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($product);
        $dm->flush();

        return new Response( json_encode($product));
        //return new Response('Created product id '.$product->getId());
    }
    /**
     * @Route("/show_product", name="show_product")
     *
     */
    public function showAction()
    {
        $product = $this->get('doctrine_mongodb')
            ->getRepository('AppBundle:ClientInfo')
            ->findAll();

        if (!$product) {
            throw $this->createNotFoundException('No product found ');
        }
        //json_encode($product[0]);
        $response = new Response(json_encode($product,JSON_PRETTY_PRINT));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * @Route("/test_post", name="test_post")
     *
     */
    public function postAction()
    {
        $userInfo= $this->get('security.context')->getToken()->getUser();

        return $this->render('AppBundle:default:test.html.twig');

    }


}
