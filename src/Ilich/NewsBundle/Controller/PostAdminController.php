<?php

namespace Ilich\NewsBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Post controller.
 *
 */

class PostAdminController extends CRUDController
{
    /**
     * return the Response object associated to the create action
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return Response
     */
    public function createAction()
    {
         // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        if ('POST' === $this->getRestMethod()) {
            $form->submit($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                if (false === $this->admin->isGranted('CREATE', $object)) {
                    throw new AccessDeniedException();
                }

                $user= $this->getUser();
                $object->setUser($user);
                $object->setCreatedAt(new \DateTime);
                $object->setUpdatedAt(new \DateTime);
                $this->admin->create($object);

                $this->admin->update($object);

                if( $object->getEnabled() ) {
                    $this->postVKWallMessage($object);
                    $this->postFBWallMessage($object);
                    $this->postTwitterWallMessage($object);
                }

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result' => 'ok',
                        'objectId' => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->addFlash('sonata_flash_success', $this->admin->trans('flash_create_success', array('%name%' => $this->admin->toString($object)), 'SonataAdminBundle'));

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash('sonata_flash_error', $this->admin->trans('flash_create_error', array('%name%' => $this->admin->toString($object)), 'SonataAdminBundle'));
                }
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form'   => $view,
            'object' => $object,
        ));
    }

    /**
     * return the Response object associated to the edit action
     *
     *
     * @param mixed $id
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    public function editAction($id = null)
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);


        if ($this->getRestMethod() == 'POST') {
            $form->submit($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                //temporary remove logic for post to the social networks when editing
                /*if( $object->getEnabled() ) {
                    $this->postVKWallMessage($object);
                    $this->postFBWallMessage($object);
                    $this->postTwitterWallMessage($object);
                }*/
                $user= $this->getUser();
                $object->setUser($user);
                $this->admin->update($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->addFlash('sonata_flash_success', $this->admin->trans('flash_edit_success', array('%name%' => $this->admin->toString($object)), 'SonataAdminBundle'));

                // redirect to edit mode
                return $this->redirectTo($object);
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash('sonata_flash_error', $this->admin->trans('flash_edit_error', array('%name%' => $this->admin->toString($object)), 'SonataAdminBundle'));
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form'   => $view,
            'object' => $object,
        ));
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @param mixed $id
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction($id)
    {
        $id     = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedException();
        }

        if ($this->getRestMethod() == 'DELETE') {
            // check the csrf token
            $this->validateCsrfToken('sonata.delete');

            try {
                $this->admin->delete($object);
                $this->deleteVKWallMessage($object);
                $this->deleteFBWallMessage($object);
                $this->deleteTwitterWallMessage($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array('result' => 'ok'));
                }

                $this->addFlash(
                    'sonata_flash_success',
                    $this->admin->trans(
                        'flash_delete_success',
                        array('%name%' => $this->admin->toString($object)),
                        'SonataAdminBundle'
                    )
                );

            } catch (ModelManagerException $e) {
                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array('result' => 'error'));
                }

                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans(
                        'flash_delete_error',
                        array('%name%' => $this->admin->toString($object)),
                        'SonataAdminBundle'
                    )
                );
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render($this->admin->getTemplate('delete'), array(
            'object'     => $object,
            'action'     => 'delete',
            'csrf_token' => $this->getCsrfToken('sonata.delete')
        ));
    }

    /**
     * execute a batch delete
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @param \Sonata\AdminBundle\Datagrid\ProxyQueryInterface $query
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchActionDelete(ProxyQueryInterface $query)
    {
        if (false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();
        $selectedModels = $query->execute();
        try {
            foreach($selectedModels as $selectedModel){
                $modelManager->delete($selectedModel);
                $this->deleteVKWallMessage($selectedModel, false);
                $this->deleteFBWallMessage($selectedModel, false);
                $this->deleteTwitterWallMessage($selectedModel, false);
            }
            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (ModelManagerException $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }

    /**
     * helper function to post message on the VK's wall
     *
     * @param type $object
     */
    public function postVKWallMessage($object)
    {
        if ($object->getVkontakte()) {
            $vk = $this->container->get('ilich_social_api.vkontakte');
            $this->checkVKParameters($object, $vk);
            $res = $vk->api('wall.post', array(
                'owner_id' => $vk->getGroupId()*(-1),
                'message' => strip_tags($object->getContent()),
                'from_group' => 1,
            ));

            if (isset($res->error)){
                $object->setVkontakte(false);
                $this->addFlash('sonata_flash_error', $this->admin->trans("Vkontakte. " . $res->error->error_msg));
            } else {
                $object->setVkId($res->response->post_id);
                $this->addFlash('sonata_flash_success', $this->admin->trans('Successfully posted to Vkontakte'));
            }
        }
    }

    /**
     * helper function to post message to the Facebook
     *
     * @param type $object
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postFBWallMessage($object) {
        if ($object->getFacebook()) {
            $facebook = $this->get('ilich_social_api.facebook');

            if (!$this->container->hasParameter('ilich_social_api.facebook.access_token')) {
                $object->setFacebook(false);

                return new Response('There is no access_token. And we can not go on at this point. For posting to wall you have to get "permanent" access_token');
            }

            $params = array(
              "access_token" => $this->container->getParameter('ilich_social_api.facebook.access_token'), // see: https://developers.facebook.com/docs/facebook-login/access-tokens/
              "message" => strip_tags($object->getContent()),
              "link" => "http://" . $_SERVER['SERVER_NAME']."/news/". $object->getSlug()
              //"link" => "http://ilich.com.ua/news/il-ichievtsy-moghut-pomoch-kiborgham"
            );
            // post to Facebook
            // see: https://developers.facebook.com/docs/reference/php/facebook-api/
            try {
              $ret = $facebook->api('/' . $this->container->getParameter('ilich_social_api.facebook.group_id') . '/feed', 'POST', $params);
              $postId = substr($ret['id'], strpos( $ret['id'], '_') + 1 );
              $object->setFbId($postId);
              $this->addFlash('sonata_flash_success', $this->admin->trans('Successfully posted to Facebook'));
            } catch(Exception $e) {
              $object->setFacebook(false);
              $this->addFlash('sonata_flash_error', $this->admin->trans('Facebook. ' . $e->getMessage()));
            }
        }
    }

    /**
     * helper function to delete message from the Facebook
     *
     * @param type $object
     * @param bool $showSuccessInfo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFBWallMessage($object, $showSuccessInfo = true) {
        if ($object->getFacebook()) {
            $facebook = $this->get('ilich_social_api.facebook');

            if (!$this->container->hasParameter('ilich_social_api.facebook.access_token')) {
                $object->setFacebook(false);

                return new Response('There is no access_token. And we can not go on at this point. For removing from the wall you have to get "permanent" access_token');
            }

            try {
              $ret = $facebook->api('/' . $this->container->getParameter('ilich_social_api.facebook.user_id') . '_' . $object->getFbId(), 'DELETE');

              if (1 == $ret){
                  if ($showSuccessInfo) {
                    $this->addFlash('sonata_flash_success', $this->admin->trans('Successfully removed from Facebook'));
                  }
              }
            } catch(\Exception $e) {
                $object->setFacebook(false);
                $this->addFlash('sonata_flash_error', $this->admin->trans('Facebook. ' . $e->getMessage()));
            }
        }
    }

    /**
     * helper function to post message on the Twitter's wall
     *
     * @param type $object
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postTwitterWallMessage($object)
    {
        if ($object->getTwitter()) {
            $twitter = $this->container->get('ilich_social_api.twitter');
            
            if (!$this->container->hasParameter('ilich_social_api.twitter.access_token')) {
                $object->setTwitter(false);
                
                return new Response('There is no access_token. And we can not go on at this point. For posting to wall you have to get "permanent" access_token');
            }

            $res = $twitter->post('statuses/update', array('status' => strip_tags($object->getAnounce())));
            
            if (isset($res->errors)) {
                $object->setTwitter(false);
                $this->addFlash('sonata_flash_error', $this->admin->trans("Twitter. " . $res->errors[0]->message));
            } else {
                $object->setTwitId($res->id_str);
                $this->addFlash('sonata_flash_success', $this->admin->trans('Successfully posted to Twitter'));
            }
        }
    }
}
