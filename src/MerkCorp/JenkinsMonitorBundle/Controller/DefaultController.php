<?php

namespace MerkCorp\JenkinsMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('MerkCorpJenkinsMonitorBundle:Default:index.html.twig');
    }
}
