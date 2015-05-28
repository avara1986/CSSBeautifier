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
use CrawlerBundle\Entity\Css;

class CssController extends Controller
{
    public function readAction(Request $request, $id, $token = "")
    {
        if(strlen($token)==0) $token = $request->get('token');
        $em = $this->getDoctrine()->getManager();
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
        return new Response($serializer->serialize($result, 'json'),200);
    }
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $token = $request->get('token');
        //var_dump($id);
        //var_dump($token);
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
        foreach($parser->getAllDeclarationBlocks() as $oBlock) {
            foreach($oBlock->getSelectors() as $oSelector) {
                //Loop over all selector parts (the comma-separated strings in a selector) and prepend the id
                $num_elements=1;
                $selector = $oSelector->getSelector();
                //var_dump($selector);
                if(!preg_match("/focus/i", $selector)){
                    $num_elements = count($crawler->filter($selector));
                }
                //var_dump($num_elements);

                if($num_elements==0){
                    $parser->removeDeclarationBlockBySelector($selector);
                }
               /* */
            }
        }
        $oFormat = OutputFormat::create()->indentWithSpaces(4)->setSpaceBetweenRules("\n");
        $css->setOriginalCompressed($css_content_original_compressed);
        $css->setBeautyCompressed($parser->render());
        $css->setBeauty($parser->render($oFormat));
        $em->persist($css);
        $em->flush();
        //var_dump($oCss);

        //print $parser->render();
        //$css_content_compressed = $parser->parse()->render();
        return new Response("OK",200);
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
            //echo "LLega1";
            return false;
        }
        //var_dump($css->getWebsite()->getToken());
        //var_dump($token);
        if($css->getWebsite()->getToken()!==$token){
            //echo "LLega2";
            return false;
        }
        $client = new Client();
        $crawler =  $client->request('GET', "http://".$css->getWebsite()->getUrl()."/".$css->getFile(), array(), array(), array(
                'HTTP_USER_AGENT' => 'BeautyCSS-bot/0.0.1',
        ));
        $status_code =  $client->getResponse()->getStatus();
        if($status_code!=200){
            //echo "LLega3";
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
}