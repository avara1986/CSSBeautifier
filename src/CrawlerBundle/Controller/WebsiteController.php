<?php

namespace CrawlerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use CrawlerBundle\Entity\Website;

class WebsiteController extends Controller
{
    public function readAction($id, $token)
    {
        return $this->render('CrawlerBundle:Default:index.html.twig', array());
    }
    public function createAction(Request $request)
    {

        $website_url = $request->get('website');
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website_url)) {
            return new Response("ERROR URL",404);
        }
        $website_url = (preg_replace("/(https?|ftp):\/\//","",$website_url,1));
        $website_url = strrev(preg_replace("/\//","",strrev($website_url),1));
        $hash="¡Viva la gente!";
        $em = $this->getDoctrine()->getManager();
        $website = $em->getRepository('CrawlerBundle:Website')->findOneBy(array('url' => $website_url));
        if(count($website)==0) {
            $website = new Website();
        }
        $website->setUrl($website_url);
        $website->setToken(base64_encode(sha1($website_url+$hash)));

        $em->persist($website);
        $em->flush();
        return new Response("OK",200);
        //return $this->render('CrawlerBundle:Default:index.html.twig', array());
    }
    public function updateAction($id, $token)
    {
        return $this->render('CrawlerBundle:Default:index.html.twig', array());
    }
}
