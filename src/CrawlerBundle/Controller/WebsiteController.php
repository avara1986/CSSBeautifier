<?php

namespace CrawlerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;
use CrawlerBundle\Entity\Website;

class WebsiteController extends Controller
{
    public function readAction($id, $token)
    {
        $web = $this->getWebsite($id, $token);
        $crawler =  $this->getWebsiteURL($web);
        if($crawler===false){
            return new Response("ERROR",404);
        }
        foreach($crawler->filter('[type="text/css"]') as $content){
            $node = new Crawler($content);
            var_dump($node->attr('href'));
        }
        return new Response("OK",200);
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
    private function getWebsiteURL(Website $web)
    {
        $client = new Client();
        $crawler =  $client->request('GET', "http://".$web->getUrl(), array(), array(), array(
                'HTTP_USER_AGENT' => 'BeautyCSS-bot/0.0.1',
        ));
        $status_code =  $client->getResponse()->getStatus();
        if($status_code!=200){
            return false;
        }
        return $crawler;
    }
    private function getWebsite($id, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $website = $em->getRepository('CrawlerBundle:Website')->findOneBy(array('id' => $id, 'token' => $token));
        if(count($website)==0) {
            return false;
        }
        return $website;
    }
}
