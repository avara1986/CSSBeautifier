<?php

namespace WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sabberworm\CSS\Parser;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //http://empleo.we-ma.com/css/style.css
        //http://gobalo.es/css/style.css
        //http://dev.lesuit.es/css/style.css
        //http://dev.lesuit.es/css/style.css
        //http://zuzumba.es/css/css.css
        //http://192.168.1.9/lesuit/landing/css/style.css
        $oCssParser = new Parser(file_get_contents('http://192.168.1.9/lesuit/landing/css/style.css'));
        $oCssDocument = $oCssParser->parse();
        //print $oCssDocument->render();
        return $this->render('WebsiteBundle:Default:index.html.twig', array());
    }
}
