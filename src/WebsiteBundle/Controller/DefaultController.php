<?php

namespace WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sabberworm\CSS\Parser;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WebsiteBundle:Default:index.html.twig', array());
    }
}
