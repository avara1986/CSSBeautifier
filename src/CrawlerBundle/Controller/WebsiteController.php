<?php

namespace CrawlerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;
use JMS\Serializer\SerializerBuilder;
use CrawlerBundle\Entity\Website;
use CrawlerBundle\Entity\Css;


class WebsiteController extends Controller
{
    public function readAction(Request $request, $id, $token = "")
    {
        if(strlen($token)==0) $token = $request->get('token');

        $serializer = SerializerBuilder::create()->build();
        /**
         * Obtenemos el objeto website utiilizando el token
         */
        $web = $this->getWebsite($id, $token);
        if($web===false){
            return new Response("ERROR",404);
        }
        /**
         * Verificamos que la url funciona y devuelve un objeto crawler
         */
        $crawler =  $this->getWebsiteURL($web);
        if($crawler===false){
            return new Response("ERROR",404);
        }
        $result = array(
            'id' => $web->getId(),
            'token' => $web->getToken(),
            'css' => array(),
        );
        $result['css'] = $this->checkAndSaveCSS($crawler, $web);

        return new Response($serializer->serialize($result, 'json'),200);
    }
    public function createAction(Request $request)
    {
        $serializer = SerializerBuilder::create()->build();

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
        $website->setToken(base64_encode(pack('H*',sha1($website_url.$hash))));

        $em->persist($website);
        $em->flush();
        $result = array(
                'id' => $website->getId(),
                'token' => $website->getToken(),
        );
        return new Response($serializer->serialize($result, 'json'),200);
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
    private function checkAndSaveCSS(Crawler $crawler, Website $web){
        $em = $this->getDoctrine()->getManager();
        $result_css = array();

        foreach($crawler->filter('[type="text/css"]') as $content){
            $node = new Crawler($content);
            $url_original = $node->attr('href');
            $url = preg_replace("/(https?|ftp):\/\//","",$url_original);
            $url = str_replace($web->getUrl(),"",$url);
            $css_content_original = file_get_contents("http://".$web->getUrl()."/".$url);
            //$parser = new Parser($css_content_original);
            //$css_content_compressed = $parser->parse()->render();
            $css = $em->getRepository('CrawlerBundle:Css')->findOneBy(array('website' =>$web ,'file' => $url));
            if(count($css)==0) {
                $css = $this->saveCSS($url, $css_content_original, $web);
            }
            $result_css[]= array(
                    'id' => $css->getId(),
                    'url' => $url_original,
            );
        }
        $em->flush();
        return $result_css;
    }
    private function saveCSS($url, $css_content_original,Website $web, $em){
        $css = new Css();
        $css->setFile($url);
        $css->setOriginal($css_content_original);
        $css->setWebsite($web);
        $em->persist($css);
        $em->flush();
        $em->flush();
        return $css;
    }
}
