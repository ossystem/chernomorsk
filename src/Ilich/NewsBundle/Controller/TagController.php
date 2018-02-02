<?php

namespace Ilich\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Symfony\Component\HttpFoundation\Response;


/**
 * Tag controller.
 */
class TagController extends Controller
{
    /**
     * Finds and displays a Category entity.
     * 
     */
    public function showAction($id, $page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('IlichTagBundle:Tag')->findById($id);
        if (!$entity) throw $this->createNotFoundException('Unable to find Category entity.');
             
        $query = $em->getRepository('IlichNewsBundle:Post')->getPostsByTag($id);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            24
        );
        $queryCategories = $em->getRepository('IlichNewsBundle:Category')->getCategoryList();
        
        return $this->render('IlichNewsBundle:Post:index.html.twig', array(
            'cur_category' => 'ilich_news',
            'categories' => $queryCategories,
            'posts' => $pagination
        ));
    }
}
