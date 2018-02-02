<?php

namespace Ilich\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{

    /**
     * Lists all Category entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('IlichNewsBundle:Category')->findAll();

        return $this->render('IlichNewsBundle:Post:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Category entity.
     *
     */
    public function showAction($slug,$page)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IlichNewsBundle:Category')->findOneBySlug($slug);

        if (!$entity) throw $this->createNotFoundException('Unable to find Category entity.');
             
        $query = $em->getRepository('IlichNewsBundle:Post')->getPostsByCategory($slug);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            24
        );
        $queryCategories = $em->getRepository('IlichNewsBundle:Category')->getCategoryList();
        
        return $this->render('IlichNewsBundle:Post:index.html.twig', array(
            'cur_category' => $entity->getTitle(),
            'categories' => $queryCategories,
            'posts' => $pagination
        ));
    }
}
