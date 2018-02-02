<?php

namespace Ilich\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('IlichNewsBundle:Default:index.html.twig', array('name' => $name));
    }
}
