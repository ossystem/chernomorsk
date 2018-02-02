<?php

namespace Ilich\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Widop\PluploadBundle\Form\Type\ImageType;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Description of CategoryAdmin
 *
 * @author Valentyn Diduryk <stranger@ossystem.com.ua>
 */
class PostAdmin  extends Admin
{

    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
                                 // (default = the model's id field, if any)

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('slug', 'text', array('label' => 'ilich_news.post.slug', 'required' => false))
            ->add('title', 'text', array('label' => 'ilich_news.post.title'))
            ->add('categories', 'sonata_type_model', array('label' => 'ilich_news.categories','expanded' => true, 'by_reference' => false, 'multiple' => true, 'compound' => true))
            ->add('anounce', 'textarea', array('label' => 'ilich_news.post.anounce', 'required' => false))
            ->add('content', 'ckeditor', array(
                    'label' => 'ilich_news.post.content',
                    'attr' => array('class' => 'fieldClass'),

                ))
            ->add('tags', 'entity', array(
                'class'=>'IlichTagBundle:Tag',
                'multiple' => true,
                'required' => false,
                'attr'=>array('style'=>'width: 100%;'))
            )
            ->add('specnews', 'checkbox', array('label' => 'ilich_news.post.specnews', 'required' => false))
            ->add('enabled', 'checkbox', array('label' => 'ilich_news.post.enabled', 'required' => false))
            ->add('gallery', 'sonata_type_model_list', array('required' => false, 'label' => 'ilich_news.post.photoreport'), array('link_parameters'   => array('context' => 'photoreports')));
        if($this->id($this->getSubject())){ //for edit form
            $formMapper->add('vkontakte', 'checkbox', array('label' => 'ilich_news.post.vkontakte', 'required' => false, 'disabled' => true))
                ->add('facebook', 'checkbox', array('label' => 'ilich_news.post.facebook', 'required' => false, 'disabled' => true))
                ->add('twitter', 'checkbox', array('label' => 'ilich_news.post.twitter', 'required' => false, 'disabled' => true));
        } else {
            $formMapper->add('vkontakte', 'checkbox', array('label' => 'ilich_news.post.vkontakte', 'required' => false))
                ->add('facebook', 'checkbox', array('label' => 'ilich_news.post.facebook', 'required' => false))
                ->add('twitter', 'checkbox', array('label' => 'ilich_news.post.twitter', 'required' => false));
        }
        $formMapper->end();
        $formMapper->with('Medias')
                ->add('additionalImages', 'images', array(
                    'options' => array('context'  => 'posts'),
                    'type' => new ImageType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => true
                    ))
                ->add('youtubeVideos', 'collection', array('required' => false,
                                                        'type' => 'sonata_media_type',
                                                        'allow_add' => true,
                                                        'allow_delete' => true,
                                                        'by_reference' => true,
                                                        'options' => array('provider' => 'sonata.media.provider.youtube',
                                                        'context' => 'posts')
                                                    ))

                /*->add('vimeoVideos', 'collection', array('required' => false,
                                                        'type' => 'sonata_media_type',
                                                        'allow_add' => true,
                                                        'allow_delete' => true,
                                                        'by_reference' => false,
                                                        'options' => array('provider' => 'sonata.media.provider.vimeo',
                                                        'context' => 'posts')
                                                    ))*/
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('slug', null, array('label' => 'ilich_news.post.slug'), null, array())
            ->add('title', null, array('label' => 'ilich_news.post.title'), null, array())
            ->add('content', null, array('label' => 'ilich_news.post.content'), null, array())
            ->add('tags', null, array(), null, array('multiple' => true))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array('label' => 'ilich_news.post.title'))
            ->add('specnews', null, array('label' => 'ilich_news.post.specnews'))
            ->add('enabled', null, array('label' => 'ilich_news.post.enabled'))
            ->add('postedAt', null, array('label' => 'ilich_news.post.postedAt'))
            ->add('user', null, array('label' => 'ilich_news.post.user'))
            ->add('author', null, array('label' => 'ilich_news.post.author'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                ),
                'label' => 'ilich_news.actions'
            ))
        ;
    }

    /**
     * Конфигурация отображения записи
     *
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     * @return void
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title', null, array('label' => 'ilich_news.post.title'))
            ->add('content', null, array('label' => 'ilich_news.post.content'))
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'IlichNewsBundle:Post:__clear-cache.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clear', 'clear');
    }

}
