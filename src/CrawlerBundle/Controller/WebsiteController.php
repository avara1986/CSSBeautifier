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
        $crawler =  $this->getWebsiteURL($web->getUrl());
        if($crawler===false){
            return new Response("ERROR",404);
        }
        $result = array(
            'id' => $web->getId(),
            'token' => $web->getToken(),
            'css' => array(),
        );
        /**
         * Verificamos que el CSS no existe en la base de datos, lo guaramos en caso negativo y devuelve todos los CSS que usa la web
         * */
        $result['css'] = $this->checkAndSaveCSS($crawler, $web);
        $response = new Response($serializer->serialize($result, 'json'),200);
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
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
        if($this->getWebsiteURL($website_url)===false){
            return new Response("ERROR",404);
        }
        $website = $this->createWebsite($website_url);
        $result = array(
                'id' => $website->getId(),
                'token' => $website->getToken(),
        );
        $response = new Response($serializer->serialize($result, 'json'),200);
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
    public function updateAction($id, $token)
    {
        return $this->render('CrawlerBundle:Default:index.html.twig', array());
    }
    private function getWebsiteURL($web_url)
    {
        $client = new Client();
        try {
            $crawler =  $client->request('GET', "http://".$web_url, array(), array(), array(
                    'HTTP_USER_AGENT' => 'BeautyCSS-bot/0.0.1',
            ));
        }catch (\HttpConnectException $e) {
            return false;
        }
        $status_code =  $client->getResponse()->getStatus();
        if($status_code!=200){
            return false;
        }
        return $crawler;
    }
    private function createWebsite($web_url){
        $hash="¡Viva la gente!";
        $em = $this->getDoctrine()->getManager();
        $website = $em->getRepository('CrawlerBundle:Website')->findOneBy(array('url' => $web_url));
        if(count($website)==0) {
            $website = new Website();
        }
        $website->setUrl($web_url);
        $website->setToken(base64_encode(sha1($web_url.$hash)));

        $em->persist($website);
        $em->flush();
        return $website;
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
    /**
     * checkAndSaveCSS
     * @param Crawler $crawler
     * @param Website $web
     * @return multitype:multitype:number Ambigous <string, NULL>
     */
    private function checkAndSaveCSS(Crawler $crawler, Website $web){
        $em = $this->getDoctrine()->getManager();
        $result_css = array();

        foreach($crawler->filter('[rel="stylesheet"],[type="text/css"]') as $content){
            $node = new Crawler($content);
            $url_original = $node->attr('href');
            $url = preg_replace("/(https?|ftp):\/\//","",$url_original);
            $url = str_replace($web->getUrl(),"",$url);
            $url = preg_replace("/\?(.*)/", "", $url);
            if(!preg_match("/fonts\.googleapis\.com/", $url) && preg_match("/\.css/", $url)){
            	$result = true;
                if (!preg_match("/^\//", $url)){
                    $url = "/".$url;
                }
                $css_content_original = @file_get_contents("http://".$web->getUrl()."".$url);
                if($css_content_original===false){
                	$result = false;
                }
                $css = $em->getRepository('CrawlerBundle:Css')->findOneBy(array('website' =>$web ,'file' => $url));
                if(count($css)==0) {
                    $css = $this->saveCSS($url, $css_content_original, $web, $em);
                }else{
                    $web->removeCss($css);
                    $css = $this->saveCSS($url, $css_content_original, $web, $em);
                }
                $result_css[]= array(
                        'id' => $css->getId(),
                        'url' => "http://".$web->getUrl()."".$url,
                		'result' => $result
                 );
            }
        }
        $em->flush();
        return $result_css;
    }
    /**
     * saveCSS
     * Se le pasa doctrine->GetManager ya que está función es usada en un loop y por ahorrar llamadas.
     * @param string $url
     * @param string $css_content_original
     * @param Website $web
     * @param unknown $em
     * @return \CrawlerBundle\Entity\Css
     */
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
