<?php

namespace CrawlerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\OutputFormat;
use JMS\Serializer\SerializerBuilder;
use CrawlerBundle\Entity\Website;

class CssController extends Controller
{
    public function readAction(Request $request, $id, $token = "")
    {
        if(strlen($token)==0) $token = $request->get('token');
        $serializer = SerializerBuilder::create()->build();
        /**
         * Obtenemos el objeto website utiilizando el token
         */
        $css = $this->getCss($id, $token);
        if($css===false){
            return new Response("",404);
        }
        $result= array(
                'id' => $css->getId(),
                'created' => $css->getCreated(),
                'original' => $css->getOriginal(),
                'original_compressed' => $css->getOriginalCompressed(),
                'beauty' => $css->getBeauty(),
                'beauty_compressed' => $css->getBeautyCompressed(),

        );
        $response = new Response($serializer->serialize($result, 'json'),200);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $token = $request->get('token');
        $css = $this->getCss($id, $token);
        if($css===false){
            return new Response("",404);
        }
        $crawler =  $this->getWebsiteURL($css->getWebsite());
        if($crawler===false){
            return new Response("ERROR",404);
        }
        $parser = new Parser($css->getOriginal());
        $parser = $parser->parse();
        $css_content_original_compressed = $parser->render();
        $parser = $this->searchUnusedCss($crawler, $parser);
        $oFormat = OutputFormat::create()->indentWithSpaces(4)->setSpaceBetweenRules("\n");
        $css->setOriginalCompressed(trim(preg_replace('/\s+/', ' ', $css_content_original_compressed)));
        $css->setBeautyCompressed(trim(preg_replace('/\s+/', ' ', $parser->render())));
        $css->setBeauty($parser->render($oFormat));
        $em->persist($css);
        $em->flush();
        $response = new Response("OK",200);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
    public function updateAction($id, $token)
    {
        return $this->render('CrawlerBundle:Default:index.html.twig', array());
    }
    private function getCss($id, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $css = $em->getRepository('CrawlerBundle:Css')->find($id);
        if(count($css)==0) {
            return false;
        }
        if($css->getWebsite()->getToken()!==$token){
            return false;
        }
        $client = new Client();
        $client->request('GET', "http://".$css->getWebsite()->getUrl()."/".$css->getFile(), array(), array(), array(
                'HTTP_USER_AGENT' => 'BeautyCSS-bot/0.0.1',
        ));
        $status_code =  $client->getResponse()->getStatus();
        if($status_code!=200){
            return false;
        }
        return $css;
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
    private function searchUnusedCss(Crawler $crawler, $parser){
        foreach($parser->getAllDeclarationBlocks() as $oBlock) {
            foreach($oBlock->getSelectors() as $oSelector) {
                //Loop over all selector parts (the comma-separated strings in a selector) and prepend the id
                $num_elements=1;
                $selector = $oSelector->getSelector();
                if(!preg_match("/focus/i", $selector) && !preg_match("/:/i", $selector)){
                    $num_elements = count($crawler->filter($selector));
                }
                if($num_elements==0){
                    $parser->removeDeclarationBlockBySelector($selector);
                }
            }
        }
        return $parser;
    }
}
