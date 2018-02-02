<?php

namespace Ilich\NewsBundle\Controller;

use Ilich\SiteBundle\Entity\Complaint;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Ilich\NewsBundle\Form\PostType;
use Ilich\NewsBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\SwiftmailerBundle\Command;
use Ilich\SiteBundle\Entity\Thread;

/**
 * Post controller.
 *
 */
class PostController extends Controller
{

    /**
     * Lists all Post entities.
     *
     */
    public function indexAction($page, $posts = 24, $tag = null)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('IlichNewsBundle:Post')->getPosts(
            $this->getRequest()->query->getInt('offset'),
            $this->container->getParameter('ilich_news.posts_per_page'),
            $tag 
        );

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $page,
                $posts
        );
        $pagination->setUsedRoute('news_page');
                
        $querySpecNews = $em->getRepository('IlichNewsBundle:Post')->getPosts(
            $this->getRequest()->query->getInt('offset'),
           1
        );
        $queryCategories = $em->getRepository('IlichNewsBundle:Category')->getCategoryList();
        $specNews = $paginator->paginate($querySpecNews, 1, 1);        
        
        return $this->render('IlichNewsBundle:Post:index.html.twig', array(
            'posts' => $pagination,
            'categories' => $queryCategories,
            'cur_category' => 'ilich_news',
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IlichNewsBundle:Post')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $querySpecNews = $em->getRepository('IlichNewsBundle:Post')->getPosts(
            $this->getRequest()->query->getInt('offset'),
           1
        );
        $paginator  = $this->get('knp_paginator');
        $specNews = $paginator->paginate($querySpecNews, 1, 1);
        $queryPosters = $em->getRepository('IlichPosterBundle:Poster')->getPosters(
            $this->getRequest()->query->getInt('offset'),
            1
        );
        $posters = $paginator->paginate($queryPosters, 1, 1);
        $comments = $em->getRepository('IlichSiteBundle:Comment')->findByThread('news_'.$entity->getId());
        $comments = $paginator->paginate($comments, 1, 10);
        
        $images = $entity->getAdditionalImages();

        if (!empty($images[0])) {
            $provider = $this->get('sonata.media.pool')->getProvider($images[0]->getProviderName());
        
            $format = $provider->getFormatName($images[0], 'big');
            $image_meta = $provider->generatePublicUrl($images[0], $format);
        } else {
            $image_meta = null;
        }
        
        // Similar news
        $tags = $entity->getTags()->map(function($tag) { 
            return $tag->getId();
        })->toArray();
        $similar_news = $em->getRepository('IlichNewsBundle:Post')->getSimilarPosts($entity->getId(), $tags);
                   
        
        return $this->render('IlichNewsBundle:Post:show.html.twig', array(
            'entity' => $entity,
            'specNews' => $specNews,
            'posters' => $posters,
            'comments' => $comments,
            'image_meta' => $image_meta,
            'image_meta' => $image_meta,
            'similar_news' => $similar_news,
        ));
    }
    
    /**
     * Displays a form to create a new Post by users  .
     *
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */    
    public function addAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userLogin= $this->getUser();
        $userName= $this->getUser()->getName();
        $entity = new Post();
        $form = $this->createForm(new PostType(), $entity); 
        $request = $this->getRequest();
        $error = null;
        
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if (count($form->get('additionalImages')) > 0) {
                $entity->setPostedAt($entity->getUpdatedAt());
                $entity->setCategories($form->getData()->getCategories());
                $entity->setUser($userLogin);
                $entity->setAuthor($userName);
                $em->persist($entity);
                $em->flush();

                $admin_link = 'http://'.$_SERVER['HTTP_HOST'].'/admin/ilich/news/post/'.$entity->getId().'/edit';

                $mailer = $this->get('mailer');
                $transport = $mailer->getTransport();
                $transport->start();
                
                $message = \Swift_Message::newInstance()
                        ->setSubject('Предложение новости '.$entity->getTitle())
                        ->setFrom(array(
                            $this->container->getParameter('mail.site_mail_from') => $this->container->getParameter('mail.site_mail_from_name')
                        ))
                        ->setTo($this->container->getParameter('mail.notify'));
                if ($this->container->getParameter('mail.notify.copy')) {
                    $message->setCc($this->container->getParameter('mail.notify.copy'));
                }
                
                $message->addPart($this->renderView('IlichNewsBundle:Mail:notify_new.html.twig', 
                        array('username' => $userName,
                            'title' => $entity->getTitle(),
                            'link' => $admin_link,
                            
                        )
                    ), 'text/html');

                $isSent = $transport->send($message);

                return $this->redirect($this->generateUrl('thank'));
            } else {
                $error = 'ilich_news.post.noimage';        
            }
       }
         
        return $this->render('IlichNewsBundle:Post:add.html.twig', array(
            'userLogin' => $userLogin,
            'userName' => $userName,
            'form' => $form->createView(),
            'error' => $error,
        ));
    }
    
    /**
     * Displays a 'Thank you' page  
     *
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function thankAction(){
        $userLogin= $this->getUser();
        $userName= $this->getUser()->getName();        
        
        return $this->render('IlichNewsBundle:Post:thank.html.twig', array(
            'userLogin' => $userLogin,
            'userName' => $userName
            )
        );
    }
        
    public function lastAction($page, $number)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('IlichNewsBundle:Post')->getLastPosts(
            $this->getRequest()->query->getInt('offset'),
            $this->container->getParameter('ilich_news.posts_per_page')
        );
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $page,
                $number
        );
        $pagination->setUsedRoute('news_page');
        
        return $this->render('IlichNewsBundle:Post:last.html.twig', array(
            'pagination' => $pagination
        ));
    }

    public function complaintCommentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $sc = $this->get('security.context');

        if ($sc->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
            $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($id);
            $complaint = $em->getRepository('IlichSiteBundle:Complaint')->findOneBy(array('user' => $user, 'comment' => $comment));

            if (!$complaint) {
                $complaint = new Complaint();
                $complaint->setComment($comment);
                $complaint->setUser($user);

                $em->persist($complaint);

                $count = $comment->getComplaint() + 1;
                $comment->setComplaint($count);

                $em->persist($comment);
                $em->flush();
            } else {
                return new JsonResponse(array(
                    'status' => 'alreadyComplaint',
                    'message' => $this->get('translator')->trans('ilich_site.comments.complaint.alreadyComplaint', array(), 'IlichSiteBundle')
                ));
            }
        } else {
            return new JsonResponse(array(
                'status' => 'notLogged',
                'message' => $this->get('translator')->trans('ilich_site.comments.complaint.notLogged', array(), 'IlichSiteBundle')
            ));
        }

        return new JsonResponse(array(
            'status' => 'OK',
            'message' => $this->get('translator')->trans('ilich_site.comments.complaint.success', array(), 'IlichSiteBundle')
        ));
    }
}
